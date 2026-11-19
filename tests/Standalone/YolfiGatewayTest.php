<?php

// Isolated adapter tests: only HTTP, routing, invoice persistence and payment
// recording are test doubles. The Gateway/Extension base and Yolfi adapter are real.
namespace Illuminate\Http {
    class Request
    {
        public function __construct(private string $raw, private array $headers = []) {}
        public function getContent() { return $this->raw; }
        public function header($name) { return $this->headers[strtolower($name)] ?? null; }
    }
}
namespace Illuminate\Support\Facades {
    class DB
    {
        public static int $depth = 0;
        public static function transaction($callback)
        {
            $before = unserialize(serialize(\App\Models\Invoice::$rows));
            self::$depth++;
            try { return $callback(); }
            catch (\Throwable $e) { \App\Models\Invoice::$rows = $before; throw $e; }
            finally { self::$depth--; }
        }
    }
    class Http
    {
        public static array $requests = [];
        public static array $responses = [];
        public static function withToken($token) { return new TestHttp($token); }
    }
    class TestHttp
    {
        public array $options = [];
        public function __construct(private string $token) {}
        public function acceptJson() { return $this; }
        public function asJson() { return $this; }
        public function connectTimeout($value) { $this->options['connect_timeout'] = $value; return $this; }
        public function timeout($value) { $this->options['timeout'] = $value; return $this; }
        public function withoutRedirecting() { $this->options['redirects'] = false; return $this; }
        public function withHeaders($headers) { $this->options['headers'] = $headers; return $this; }
        public function post($url, $payload) { return $this->send('POST', $url, $payload); }
        public function get($url) { return $this->send('GET', $url, null); }
        private function send($method, $url, $payload)
        {
            \check(DB::$depth === 0, 'HTTP must not hold the invoice lock');
            $this->options['bearer'] = $this->token;
            Http::$requests[] = compact('method', 'url', 'payload') + ['options' => $this->options];
            $next = array_shift(Http::$responses);
            if ($next instanceof \Throwable) { throw $next; }
            if (is_callable($next)) { $next = $next($payload); }
            \check(is_array($next), 'unexpected HTTP request');
            return new TestResponse($next[0], $next[1]);
        }
    }
    class TestResponse
    {
        public function __construct(private int $code, private $data) {}
        public function successful() { return $this->code >= 200 && $this->code < 300; }
        public function json() { return $this->data; }
    }
    class Route
    {
        public static array $routes = [];
        public static function post($path, $action)
        {
            self::$routes[] = ['path' => $path, 'action' => $action];
            return new TestRoute;
        }
    }
    class TestRoute
    {
        public function withoutMiddleware($middleware) { Route::$routes[array_key_last(Route::$routes)]['without'] = $middleware; return $this; }
        public function name($name) { Route::$routes[array_key_last(Route::$routes)]['name'] = $name; return $this; }
    }
}
namespace App\Models {
    class Gateway
    {
        public static array $settings = [];
        public static function where($key, $name)
        {
            \check($key === 'extension' && $name === 'Yolfi', 'extension configuration lookup');
            return new class {
                public function first() { return (object) ['settings' => new TestSettings]; }
            };
        }
    }
    class TestSettings
    {
        public function pluck($value, $key) { \check($value === 'value' && $key === 'key', 'settings columns'); return $this; }
        public function toArray() { return Gateway::$settings; }
    }
    class Invoice
    {
        public static array $rows = [];
        public array $props = [];
        public string $status = 'pending';
        public string $currency_code = 'EUR';
        public $remaining = '12.34';
        public function __construct(public int $id = 42) { self::$rows[$id] = $this; }
        public static function whereKey($id) { return new InvoiceQuery($id); }
        public function properties() { return new Properties($this); }
    }
    class InvoiceQuery
    {
        private bool $locked = false;
        public function __construct(private $id) {}
        public function lockForUpdate() { $this->locked = true; return $this; }
        public function first()
        {
            \check($this->locked && \Illuminate\Support\Facades\DB::$depth > 0, 'invoice lookup must be row locked');
            return Invoice::$rows[$this->id] ?? null;
        }
    }
    class Properties
    {
        private string $key;
        public function __construct(private Invoice $invoice) {}
        public function where($field, $value) { \check($field === 'key', 'property lookup key'); $this->key = $value; return $this; }
        public function value($field) { \check($field === 'value', 'property lookup value'); return $this->invoice->props[$this->key] ?? null; }
        public function updateOrCreate($keys, $values)
        {
            \check(\Illuminate\Support\Facades\DB::$depth > 0, 'state write must be transactional');
            $this->invoice->props[$keys['key']] = $values['value'];
        }
    }
}
namespace App\Helpers {
    class ExtensionHelper
    {
        public static array $payments = [];
        public static bool $fail = false;
        public static function addPayment($invoice, $gateway, $amount, $fee = null, $transactionId = null)
        {
            \check(\Illuminate\Support\Facades\DB::$depth > 0, 'payment must be inside transaction');
            if (self::$fail) { throw new \RuntimeException('synthetic payment-store failure'); }
            \check(!isset(self::$payments[$transactionId]), 'confirmation was applied twice');
            self::$payments[$transactionId] = compact('invoice', 'gateway', 'amount');
            $row = \App\Models\Invoice::$rows[$invoice];
            $row->remaining = '0.00'; $row->status = 'paid';
        }
    }
}
namespace {
    use App\Helpers\ExtensionHelper;
    use App\Models\Invoice;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Paymenter\Extensions\Gateways\Yolfi\Yolfi;

    function check($condition, string $message): void { if (!$condition) { throw new \RuntimeException($message); } }
    function same($expected, $actual, string $message): void { check($expected === $actual, $message . ': ' . json_encode($actual)); }
    function class_basename($class) { return substr(strrchr('\\' . $class, '\\'), 1); }
    function route($name, $invoice = null) { return 'https://merchant.example.com/invoices/' . ($invoice?->id ?? 'webhook'); }
    function response($body = '', $status = 200) { return (object) compact('body', 'status'); }
    function resetFixture(): array
    {
        Invoice::$rows = []; Http::$requests = []; Http::$responses = [];
        ExtensionHelper::$payments = []; ExtensionHelper::$fail = false;
        $gateway = new Yolfi(array_combine(['api_key', 'webhook_secret'], ['EXAMPLE_API_KEY', 'EXAMPLE_WEBHOOK_SECRET']));
        return [$gateway, new Invoice];
    }
    function snapshot(array $payload): array
    {
        return ['id' => 'ycs_test_session', 'object' => 'checkout_session', 'status' => 'OPEN',
            'amount' => $payload['amount'], 'currency' => $payload['currency'],
            'merchantOrderId' => $payload['merchantOrderId'], 'url' => 'https://pay.yolfi.com/ycs_test_session'];
    }
    function nativeResponse($data, int $status = 200): array
    {
        return [$status, ['success' => true, 'data' => $data]];
    }
    function createCheckout(): array
    {
        [$gateway, $invoice] = resetFixture();
        Http::$responses[] = fn ($payload) => nativeResponse(snapshot($payload), 201);
        $gateway->pay($invoice, '12.34');
        return [$gateway, $invoice, snapshot(Http::$requests[0]['payload'])];
    }
    function event(array $session, string $type = 'payment.confirmed'): array
    {
        // Synthetic token receivable intentionally differs from pricing-currency total.
        return ['id' => 'evt_test_confirmation', 'type' => $type, 'created' => 1700000000, 'livemode' => false,
            'data' => ['checkoutSessionId' => $session['id'], 'merchantOrderId' => $session['merchantOrderId'],
                'invoiceId' => '00000000-0000-4000-8000-000000000042', 'paymentType' => 'ONE_TIME',
                'status' => $type === 'payment.confirmed' ? 'confirmed' : 'expired',
                'amount' => '99.95', 'amountUsd' => '99.95', 'rate' => '0.1234', 'currency' => $session['currency']]];
    }
    function signed(array $event, ?string $raw = null, ?string $signature = null): Request
    {
        $raw ??= json_encode($event, JSON_THROW_ON_ERROR);
        $signature ??= base64_encode(hash_hmac('sha256', $raw, 'EXAMPLE_WEBHOOK_SECRET', true));
        return new Request($raw, ['x-yolfi-signature' => $signature]);
    }
    function confirmedSnapshot(array $session): array { $session['status'] = 'COMPLETED'; return $session; }
    function rejects(callable $action): void
    {
        try { $action(); } catch (\RuntimeException|\InvalidArgumentException $e) { return; }
        throw new \RuntimeException('operation should reject invalid input');
    }

    $root = dirname(__DIR__, 2);
    require $root . '/app/Classes/Extension/Extension.php';
    require $root . '/app/Classes/Extension/Gateway.php';
    require $root . '/app/Attributes/ExtensionMeta.php';
    $adapter = $root . '/extensions/Gateways/Yolfi/Yolfi.php';
    if (is_file($adapter)) { require $adapter; }
    try { check(class_exists(Yolfi::class), 'Missing behavior: discoverable Yolfi hosted-checkout gateway'); }
    catch (\Throwable $e) { fwrite(STDERR, 'FAIL ' . $e->getMessage() . "\n"); exit(1); }

    $tests = [];
    $tests['false missing malformed and non-2xx API envelopes fail closed for create and lookup'] = function () {
        $invalidBodies = function ($s) {
            $bodies = [$s, null, 'synthetic response detail', [], [['success' => true, 'data' => $s]],
                ['data' => $s] + $s, ['success' => true] + $s];
            foreach ([false, null, 0, 1, 'true'] as $success) { $bodies[] = ['success' => $success, 'data' => $s] + $s; }
            foreach ([null, false, 'synthetic response detail', 1, [], [$s]] as $data) { $bodies[] = ['success' => true, 'data' => $data] + $s; }
            return array_map(fn ($body) => [200, $body], $bodies);
        };
        foreach (['create', 'lookup'] as $operation) {
            // A valid inner session and valid bare decoy fields cannot bypass the envelope.
            $templates = $invalidBodies(confirmedSnapshot(snapshot(['amount' => '12.34', 'currency' => 'EUR', 'merchantOrderId' => 'template'])));
            $statuses = [301, 400, 401, 409, 429, 500, 503];
            foreach (array_merge(array_keys($templates), array_map(fn ($status) => 'status-' . $status, $statuses)) as $case) {
                if ($operation === 'create') {
                    [$g, $i] = resetFixture();
                    Http::$responses[] = function ($p) use ($case, $invalidBodies) {
                        $s = snapshot($p);
                        return is_int($case) ? $invalidBodies($s)[$case] : nativeResponse($s, (int) substr($case, 7));
                    };
                    $message = null;
                    try { $g->pay($i, '12.34'); } catch (\RuntimeException $e) { $message = $e->getMessage(); }
                    same('Yolfi checkout request failed. Retry later.', $message, 'sanitized envelope rejection');
                    same(1, count(Http::$requests), 'no extra request on create rejection');
                } else {
                    [$g, $i, $s] = createCheckout();
                    Http::$responses[] = is_int($case) ? $invalidBodies(confirmedSnapshot($s))[$case] : nativeResponse(confirmedSnapshot($s), (int) substr($case, 7));
                    $response = $g->webhook(signed(event($s)));
                    same(503, $response->status, 'bad lookup is retryable');
                    same('Unable to verify checkout; retry later', $response->body, 'lookup hides response detail');
                    same(2, count(Http::$requests), 'no extra request on lookup rejection');
                }
                same([], ExtensionHelper::$payments, 'bad envelope never credits');
                same('pending', $i->status, 'invoice stays unpaid');
                foreach (Http::$requests as $request) {
                    same('app.yolfi.com', parse_url($request['url'], PHP_URL_HOST), 'credential origin unchanged');
                    same(false, $request['options']['redirects'], 'redirect disabled on failures');
                }
            }
        }
    };
    $tests['unsafe hosted origins reject for create and confirmation without credential leaks'] = function () {
        foreach (['http://pay.yolfi.com/ycs_test_session', 'ftp://pay.yolfi.com/ycs_test_session', '//pay.yolfi.com/ycs_test_session',
            'https://app.yolfi.com/ycs_test_session', 'https://evil.example.com/ycs_test_session', 'https://pay.yolfi.com.evil.example.com/ycs_test_session',
            'https://checkout.pay.yolfi.com/ycs_test_session', 'https://pay.yolfi.com./ycs_test_session',
            sprintf('https://%s@%s/ycs_test_session', 'example', 'pay.yolfi.com'), sprintf('https://%s:%s@%s/ycs_test_session', 'example', 'synthetic', 'pay.yolfi.com'),
            sprintf('https://%s@%s/ycs_test_session', '', 'pay.yolfi.com'), 'https://pay.yolfi.com:443/ycs_test_session',
            'https://pay.yolfi.com:8443/ycs_test_session', 'https://pay.yolfi.com/ycs_test_session#fragment'] as $url) {
            [$g, $i] = resetFixture();
            Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), ['url' => $url]), 201);
            $message = null;
            try { $g->pay($i, '12.34'); } catch (\InvalidArgumentException $e) { $message = $e->getMessage(); }
            same('Invalid hosted checkout URL.', $message, 'unsafe create URL rejected');
            same(1, count(Http::$requests), 'unsafe URL never fetched');
            same([], ExtensionHelper::$payments, 'unsafe create never credits');
            [$g, $i, $s] = createCheckout();
            Http::$responses[] = nativeResponse(array_replace(confirmedSnapshot($s), ['url' => $url]));
            same(409, $g->webhook(signed(event($s)))->status, 'unsafe lookup URL rejected');
            same([], ExtensionHelper::$payments, 'unsafe lookup never credits');
            same(2, count(Http::$requests), 'only authenticated API requests');
            foreach (Http::$requests as $request) {
                same('app.yolfi.com', parse_url($request['url'], PHP_URL_HOST), 'bearer never sent to hosted URL');
                same(false, $request['options']['redirects'], 'never follow redirect');
            }
        }
    };
    $tests['public pay origin is returned without receiving API credentials'] = function () {
        [$g, $i] = resetFixture();
        Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), ['url' => 'https://pay.yolfi.com/ycs_test_session']), 201);
        same('https://pay.yolfi.com/ycs_test_session', $g->pay($i, '12.34'), 'documented hosted origin');
        $s = snapshot(Http::$requests[0]['payload']);
        $s['url'] = 'https://pay.yolfi.com/ycs_test_session';
        Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'hosted origin accepted for authenticated confirmation');
        same(2, count(Http::$requests), 'only create and authenticated lookup');
        same('POST', Http::$requests[0]['method'], 'create method');
        same('https://app.yolfi.com/api/checkout-sessions', Http::$requests[0]['url'], 'fixed create credential destination');
        same('GET', Http::$requests[1]['method'], 'lookup method');
        same('https://app.yolfi.com/api/checkout-sessions/ycs_test_session', Http::$requests[1]['url'], 'fixed lookup credential destination');
        foreach (Http::$requests as $request) {
            same('EXAMPLE_API_KEY', $request['options']['bearer'], 'synthetic bearer only on API requests');
            same(false, $request['options']['redirects'], 'no credential forwarding');
        }
    };
    $tests['native success envelope creates hosted checkout'] = function () {
        [$g, $i] = resetFixture();
        Http::$responses[] = fn ($p) => [201, ['success' => true, 'data' => snapshot($p)]];
        same('https://pay.yolfi.com/ycs_test_session', $g->pay($i, '12.34'), 'inner checkout URL');
        same([], ExtensionHelper::$payments, 'create never credits');
    };
    $tests['native success envelope authenticates confirmation after lost create response'] = function () {
        [$g, $i] = resetFixture();
        Http::$responses[] = new \RuntimeException('synthetic timeout');
        rejects(fn () => $g->pay($i, '12.34'));
        $s = snapshot(Http::$requests[0]['payload']);
        Http::$responses[] = [200, ['success' => true, 'data' => confirmedSnapshot($s)]];
        same(200, $g->webhook(signed(event($s)))->status, 'inner authenticated snapshot accepted');
        same(1, count(ExtensionHelper::$payments), 'confirmed once');
    };
    $tests['hosted checkout uses pricing decimals, correlation, timeouts and idempotency'] = function () {
        [$g, $i] = resetFixture(); Http::$responses[] = fn ($p) => nativeResponse(snapshot($p), 201);
        same('https://pay.yolfi.com/ycs_test_session', $g->pay($i, 12.34), 'checkout URL');
        $r = Http::$requests[0]; same('POST', $r['method'], 'POST checkout');
        same('https://app.yolfi.com/api/checkout-sessions', $r['url'], 'native endpoint');
        same('12.34', $r['payload']['amount'], 'major-unit amount'); same('EUR', $r['payload']['currency'], 'currency');
        check(str_starts_with($r['payload']['merchantOrderId'], 'paymenter-42-'), 'invoice correlation');
        same('https://merchant.example.com/invoices/42?checkPayment=true', $r['payload']['successUrl'], 'return URL');
        same('https://merchant.example.com/invoices/42', $r['payload']['cancelUrl'], 'cancel URL');
        check(!isset($r['payload']['customerEmail']) && !isset($r['payload']['customerName']), 'no customer fields');
        check(strlen($r['options']['headers']['Idempotency-Key']) >= 16, 'required idempotency key');
        same(false, $r['options']['redirects'], 'never forward credential via redirect');
        same('EXAMPLE_API_KEY', $r['options']['bearer'], 'configured bearer credential');
        check($r['options']['timeout'] <= 30 && $r['options']['connect_timeout'] <= 10, 'bounded HTTP');
        same([], ExtensionHelper::$payments, 'creating checkout never pays invoice');
    };
    $tests['retry after timeout preserves exact request and key across instances'] = function () {
        [$g, $i] = resetFixture(); Http::$responses[] = new \RuntimeException('synthetic timeout');
        rejects(fn () => $g->pay($i, '12.34')); $first = Http::$requests[0];
        $g = new Yolfi(array_combine(['api_key', 'webhook_secret'], ['EXAMPLE_API_KEY', 'EXAMPLE_WEBHOOK_SECRET']));
        Http::$responses[] = fn ($p) => nativeResponse(snapshot($p), 201); $g->pay($i, '12.34');
        same($first, Http::$requests[1], 'persistent idempotent retry');
    };
    $tests['non-2xx, malformed and unsafe checkout responses fail closed'] = function () {
        foreach ([400, 401, 409, 429, 500] as $status) {
            [$g, $i] = resetFixture(); Http::$responses[] = [$status, ['message' => 'synthetic rejection']];
            rejects(fn () => $g->pay($i, '12.34')); same([], ExtensionHelper::$payments, 'no credit on API rejection');
        }
        foreach (['url' => 'https://evil.example.com/pay', 'amount' => '12.35', 'currency' => 'USD', 'merchantOrderId' => 'different', 'id' => '../invalid', 'object' => 'invoice', 'status' => 'INVALID'] as $key => $value) {
            [$g, $i] = resetFixture(); Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), [$key => $value]), 201);
            rejects(fn () => $g->pay($i, '12.34'));
        }
        [$g, $i] = resetFixture(); Http::$responses[] = [201, null]; rejects(fn () => $g->pay($i, '12.34'));
    };
    $tests['invalid amounts, currency and invoice state reject before HTTP'] = function () {
        foreach (['0', '-1', '12.345', '1e2', 'NaN', '1000000000.00', '', null, true, INF, NAN] as $amount) {
            [$g, $i] = resetFixture(); rejects(fn () => $g->pay($i, $amount)); same([], Http::$requests, 'invalid input no HTTP');
        }
        [$g, $i] = resetFixture(); rejects(fn () => $g->pay($i, '12.33'));
        $i->currency_code = 'GBP'; rejects(fn () => $g->pay($i, '12.34'));
        $i->currency_code = 'EUR'; $i->status = 'cancelled'; rejects(fn () => $g->pay($i, '12.34'));
    };
    $tests['all documented currencies retain decimal major units'] = function () {
        foreach (['USD', 'EUR', 'CNY', 'RUB', 'INR', 'TRY', 'KRW', 'JPY'] as $currency) {
            [$g, $i] = resetFixture(); $i->currency_code = $currency; $i->remaining = '0.01';
            Http::$responses[] = fn ($p) => nativeResponse(snapshot($p), 201); $g->pay($i, '0.01');
            same('0.01', Http::$requests[0]['payload']['amount'], 'no minor-unit conversion');
        }
    };
    $tests['signed confirmed payment credits session pricing amount, not token amount'] = function () {
        [$g, $i, $s] = createCheckout(); Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'confirmation accepted');
        same(['invoice' => 42, 'gateway' => 'Yolfi', 'amount' => '12.34'], array_values(ExtensionHelper::$payments)[0], 'fiat posting');
        same('paid', Invoice::$rows[42]->status, 'invoice credited');
    };
    $tests['raw-body signatures reject missing, tampered and wrong-secret requests'] = function () {
        [$g, $i, $s] = createCheckout(); $e = event($s); $raw = json_encode($e);
        foreach ([new Request($raw), signed($e, $raw, 'invalid'), signed($e, $raw . ' ', base64_encode(hash_hmac('sha256', $raw, 'EXAMPLE_WEBHOOK_SECRET', true))), signed($e, $raw, base64_encode(hash_hmac('sha256', $raw, 'EXAMPLE_API_KEY', true)))] as $request) {
            same(400, $g->webhook($request)->status, 'invalid signature');
        }
        same(1, count(Http::$requests), 'bad signatures do not call API'); same([], ExtensionHelper::$payments, 'no unauthorized credit');
    };
    $tests['signed malformed JSON and event envelope rejected'] = function () {
        [$g, $i, $s] = createCheckout();
        same(400, $g->webhook(signed([], '{'))->status, 'invalid JSON');
        $e = event($s); unset($e['id']); same(400, $g->webhook(signed($e))->status, 'signed event id required');
        $e = event($s); $e['data'] = 'invalid'; same(400, $g->webhook(signed($e))->status, 'object required');
    };
    $tests['payment and session money/reference mismatches never credit'] = function () {
        foreach (['merchantOrderId' => 'paymenter-43-00000000000000000000000000000000', 'checkoutSessionId' => 'ycs_other', 'currency' => 'USD', 'status' => 'pending', 'paymentType' => 'RECURRING', 'invoiceId' => 'invalid'] as $key => $value) {
            [$g, $i, $s] = createCheckout(); $e = event($s); $e['data'][$key] = $value;
            Http::$responses[] = nativeResponse(confirmedSnapshot($s)); check($g->webhook(signed($e))->status >= 400, 'event mismatch rejects');
            same([], ExtensionHelper::$payments, 'mismatch no credit');
        }
        foreach (['amount' => '12.35', 'currency' => 'USD', 'merchantOrderId' => 'other', 'id' => 'ycs_other', 'object' => 'invoice', 'status' => 'OPEN'] as $key => $value) {
            [$g, $i, $s] = createCheckout(); Http::$responses[] = nativeResponse(array_replace(confirmedSnapshot($s), [$key => $value]));
            check($g->webhook(signed(event($s)))->status >= 400, 'retrieved snapshot mismatch rejects');
            same([], ExtensionHelper::$payments, 'session mismatch no credit');
        }
    };
    $tests['COMPLETED checkout alone and unrelated signed events do not settle'] = function () {
        [$g, $i] = resetFixture(); Http::$responses[] = fn ($p) => nativeResponse(confirmedSnapshot(snapshot($p)), 201); $g->pay($i, '12.34');
        same([], ExtensionHelper::$payments, 'COMPLETED not confirmation');
        $s = snapshot(Http::$requests[0]['payload']); $e = event($s, 'invoice.created');
        same(200, $g->webhook(signed($e))->status, 'unhandled event acknowledged'); same([], ExtensionHelper::$payments, 'no invoice-created credit');
    };
    $tests['duplicate signed id and a second event for same session apply only once'] = function () {
        [$g, $i, $s] = createCheckout(); $e = event($s);
        for ($n = 0; $n < 3; $n++) {
            Http::$responses[] = nativeResponse(confirmedSnapshot($s));
            if ($n === 2) { $e['id'] = 'evt_second_confirmation'; }
            same(200, $g->webhook(signed($e))->status, 'duplicate accepted');
        }
        same(1, count(ExtensionHelper::$payments), 'only one payment');
    };
    $tests['invoice changed or paid elsewhere is not overcredited'] = function () {
        foreach (['remaining' => '10.00', 'currency_code' => 'USD', 'status' => 'cancelled'] as $key => $value) {
            [$g, $i, $s] = createCheckout(); $i->$key = $value; Http::$responses[] = nativeResponse(confirmedSnapshot($s));
            same(409, $g->webhook(signed(event($s)))->status, 'changed invoice conflict'); same([], ExtensionHelper::$payments, 'no overcredit');
        }
    };
    $tests['confirmation can recover a lost create response by persisted reference'] = function () {
        [$g, $i] = resetFixture(); Http::$responses[] = new \RuntimeException('synthetic timeout'); rejects(fn () => $g->pay($i, '12.34'));
        $s = snapshot(Http::$requests[0]['payload']); Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'lost response recovery'); same(1, count(ExtensionHelper::$payments), 'recovered credit');
    };
    $tests['overdue leaves unpaid, permits new attempt and cannot undo confirmed payment'] = function () {
        [$g, $i, $s] = createCheckout(); Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'settled');
        Http::$responses[] = nativeResponse(confirmedSnapshot($s)); same(200, $g->webhook(signed(event($s, 'invoice.overdue')))->status, 'late overdue');
        same('paid', Invoice::$rows[42]->status, 'late expiry cannot undo payment');
        [$g, $i, $s] = createCheckout(); $expired = $s; $expired['status'] = 'COMPLETED'; Http::$responses[] = nativeResponse($expired);
        same(200, $g->webhook(signed(event($s, 'invoice.overdue')))->status, 'expiry acknowledged');
        same('pending', $i->status, 'expiry unpaid'); same([], ExtensionHelper::$payments, 'expiry no credit');
        $first = Http::$requests[0]; Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), ['id' => 'ycs_next', 'url' => 'https://pay.yolfi.com/ycs_next']), 201);
        $g->pay($i, '12.34'); $last = Http::$requests[array_key_last(Http::$requests)];
        check($first['payload']['merchantOrderId'] !== $last['payload']['merchantOrderId'], 'expiry new attempt reference');
        check($first['options']['headers'] !== $last['options']['headers'], 'expiry new idempotency key');
    };
    $tests['late create response cannot reopen an attempt expired by its webhook'] = function () {
        [$g, $i] = resetFixture();
        Http::$responses[] = function ($payload) use ($g) {
            $s = snapshot($payload); Http::$responses[] = nativeResponse(confirmedSnapshot($s));
            same(200, $g->webhook(signed(event($s, 'invoice.overdue')))->status, 'webhook during create');
            return nativeResponse($s, 201);
        };
        rejects(fn () => $g->pay($i, '12.34'));
        $first = Http::$requests[0];
        Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), ['id' => 'ycs_next', 'url' => 'https://pay.yolfi.com/ycs_next']), 201);
        $g->pay($i, '12.34'); $last = Http::$requests[array_key_last(Http::$requests)];
        check($first['payload']['merchantOrderId'] !== $last['payload']['merchantOrderId'], 'expired state must survive late OPEN response');
    };
    $tests['replaced expired attempts retain correlation for delayed confirmation'] = function () {
        [$g, $i, $s] = createCheckout();
        Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s, 'invoice.overdue')))->status, 'expire first attempt');
        $next = null;
        Http::$responses[] = function ($p) use (&$next) { $next = array_replace(snapshot($p), ['id' => 'ycs_next', 'url' => 'https://pay.yolfi.com/ycs_next']); return nativeResponse($next, 201); };
        $g->pay($i, '12.34');
        Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'delayed original confirmation still correlated');
        Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'delayed original duplicate');
        Http::$responses[] = nativeResponse(confirmedSnapshot($next));
        same(409, $g->webhook(signed(array_replace(event($next), ['id' => 'evt_different_payment'])))->status, 'another actual payment cannot overcredit');
        same(1, count(ExtensionHelper::$payments), 'only first valid payment applied');
    };
    $tests['expired checkout response can rotate a fresh idempotent attempt'] = function () {
        [$g, $i] = resetFixture();
        Http::$responses[] = fn ($p) => nativeResponse(array_replace(snapshot($p), ['status' => 'EXPIRED']));
        rejects(fn () => $g->pay($i, '12.34')); $first = Http::$requests[0];
        Http::$responses[] = fn ($p) => nativeResponse(snapshot($p), 201);
        $g->pay($i, '12.34'); $last = Http::$requests[array_key_last(Http::$requests)];
        check($first['options']['headers'] !== $last['options']['headers'], 'expired session gets a fresh key');
    };
    $tests['route-created controller loads encrypted settings through real base class'] = function () {
        [$g, $i, $s] = createCheckout();
        \App\Models\Gateway::$settings = array_combine(['api_key', 'webhook_secret'], ['EXAMPLE_API_KEY', 'EXAMPLE_WEBHOOK_SECRET']);
        $controller = new Yolfi;
        Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $controller->webhook(signed(event($s)))->status, 'controller loads saved configuration');
        same(1, count(ExtensionHelper::$payments), 'configured route confirmation');
    };
    $tests['GET errors are retryable and do not settle'] = function () {
        foreach ([[503, null], [401, null], [200, null], new \RuntimeException('synthetic timeout')] as $failure) {
            [$g, $i, $s] = createCheckout(); Http::$responses[] = $failure;
            same(503, $g->webhook(signed(event($s)))->status, 'lookup failure retryable'); same([], ExtensionHelper::$payments, 'no credit on HTTP error');
        }
    };
    $tests['failed local payment write rolls back state and confirmation retries'] = function () {
        [$g, $i, $s] = createCheckout(); ExtensionHelper::$fail = true; Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(503, $g->webhook(signed(event($s)))->status, 'local failure retryable');
        ExtensionHelper::$fail = false; Http::$responses[] = nativeResponse(confirmedSnapshot($s));
        same(200, $g->webhook(signed(event($s)))->status, 'retry succeeds'); same(1, count(ExtensionHelper::$payments), 'one retry payment');
    };
    $tests['missing credentials reject and unsupported features retain base contract'] = function () {
        [$g, $i] = resetFixture(); $g = new Yolfi(array_combine(['api_key'], ['EXAMPLE_API_KEY'])); rejects(fn () => $g->pay($i, '12.34'));
        same(400, $g->webhook(signed([]))->status, 'missing endpoint secret');
        same(false, $g->supportsRefunds(), 'refund unsupported'); same(false, $g->supportsBillingAgreements(), 'renewal unsupported');
    };
    $tests['gateway metadata and CSRF-exempt named webhook are registered'] = function () {
        [$g] = resetFixture(); $g->boot(); $r = \Illuminate\Support\Facades\Route::$routes[0];
        same('/extensions/gateways/yolfi/webhook', $r['path'], 'webhook path');
        same([Yolfi::class, 'webhook'], $r['action'], 'real handler');
        same('extensions.gateways.yolfi.webhook', $r['name'], 'route name');
        check(in_array('Illuminate\\Foundation\\Http\\Middleware\\PreventRequestForgery', $r['without'], true), 'CSRF bypass for HMAC webhook');
        $meta = (new \ReflectionClass($g))->getAttributes(\App\Attributes\ExtensionMeta::class)[0]->newInstance();
        same('yolfipay', $meta->author, 'authorized public authorship');
        $fields = $g->getConfig(); same(['api_key', 'webhook_secret'], array_column($fields, 'name'), 'required configuration');
        foreach ($fields as $field) { same(true, $field['encrypted'], 'secrets encrypted'); same(true, $field['required'], 'secrets required'); }
    };

    $failed = 0;
    foreach ($tests as $name => $test) {
        try { $test(); print('PASS ' . $name . "\n"); }
        catch (\Throwable $e) { $failed++; fwrite(STDERR, 'FAIL ' . $name . ': ' . $e->getMessage() . "\n"); }
    }
    print(count($tests) . ' tests, ' . $failed . " failures\n");
    exit($failed ? 1 : 0);
}
