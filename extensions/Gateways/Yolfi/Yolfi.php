<?php

namespace Paymenter\Extensions\Gateways\Yolfi;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

#[ExtensionMeta(
    name: 'Yolfi',
    description: 'One-time hosted checkout with signed payment confirmation',
    version: '1.0.0',
    author: 'yolfipay',
    url: 'https://docs.yolfi.com/en/checkout',
)]
class Yolfi extends Gateway
{
    private const API = 'https://app.yolfi.com/api/checkout-sessions';

    private const PROPERTY = 'yolfi_checkout';

    private const CURRENCIES = ['USD', 'EUR', 'CNY', 'RUB', 'INR', 'TRY', 'KRW', 'JPY'];

    public function boot()
    {
        require __DIR__ . '/routes.php';
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'api_key',
                'label' => 'Yolfi API key',
                'type' => 'text',
                'required' => true,
                'encrypted' => true,
            ],
            [
                'name' => 'webhook_secret',
                'label' => 'Yolfi webhook signing secret',
                'description' => 'The signing secret for the native webhook endpoint, not the API key.',
                'type' => 'text',
                'required' => true,
                'encrypted' => true,
            ],
        ];
    }

    public function pay(Invoice $invoice, $total)
    {
        $this->required('api_key');
        $this->required('webhook_secret');
        $amount = $this->money($total);

        // Commit the immutable request before HTTP. A lost response must not create
        // a new reference or idempotency key when the customer retries.
        $attempt = DB::transaction(function () use ($invoice, $amount) {
            $locked = Invoice::whereKey($invoice->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'pending' || $this->money($locked->remaining) !== $amount) {
                throw new InvalidArgumentException('The invoice balance is no longer payable.');
            }
            if (!in_array($locked->currency_code, self::CURRENCIES, true)) {
                throw new InvalidArgumentException('Yolfi does not support this pricing currency.');
            }

            $attempt = $this->state($locked);
            if ($attempt && $attempt['status'] !== 'expired') {
                if ($attempt['status'] === 'confirmed' || $attempt['amount'] !== $amount || $attempt['currency'] !== $locked->currency_code) {
                    throw new InvalidArgumentException('The existing checkout requires reconciliation before another attempt.');
                }

                return $attempt;
            }

            $reference = 'paymenter-' . $locked->id . '-' . bin2hex(random_bytes(16));
            $return = route('invoices.show', $locked);
            $attempt = [
                'reference' => $reference,
                'amount' => $amount,
                'currency' => $locked->currency_code,
                'session_id' => null,
                'status' => 'creating',
                'request' => [
                    'amount' => $amount,
                    'currency' => $locked->currency_code,
                    'merchantOrderId' => $reference,
                    'successUrl' => $return . '?checkPayment=true',
                    'cancelUrl' => $return,
                    'expiresInMinutes' => 60,
                ],
            ];
            $this->saveState($locked, $attempt, makeActive: true);

            return $attempt;
        });

        $session = $this->request('POST', self::API, $attempt['request'], [
            'Idempotency-Key' => 'yolfi-' . hash('sha256', $attempt['reference']),
        ]);
        $this->validateSession($session, $attempt);

        $expired = DB::transaction(function () use ($invoice, $attempt, $session) {
            $locked = Invoice::whereKey($invoice->id)->lockForUpdate()->first();
            $current = $locked ? $this->state($locked) : null;
            if (!$current || $current['reference'] !== $attempt['reference'] || ($current['session_id'] !== null && $current['session_id'] !== $session['id'])) {
                throw new RuntimeException('Checkout attempt changed; refresh the invoice.');
            }
            if ($current['status'] !== 'confirmed' && ($locked->status !== 'pending' || $locked->currency_code !== $attempt['currency'] || $this->money($locked->remaining) !== $attempt['amount'])) {
                throw new RuntimeException('The invoice balance changed; refresh the invoice.');
            }
            $current['session_id'] = $session['id'];
            if (!in_array($current['status'], ['confirmed', 'expired'], true)) {
                $current['status'] = $session['status'] === 'EXPIRED' ? 'expired' : 'open';
            }
            $this->saveState($locked, $current);

            return $current['status'] === 'expired';
        });

        if ($expired) {
            throw new RuntimeException('This checkout expired. Retry to start a new checkout.');
        }

        return $session['url'];
    }

    public function webhook(Request $request)
    {
        // Authenticate the exact bytes before JSON parsing or any HTTP/database work.
        try {
            $raw = $request->getContent();
            $signature = $request->header('X-Yolfi-Signature');
            $expected = base64_encode(hash_hmac('sha256', $raw, $this->required('webhook_secret'), true));
            if (!is_string($signature) || !hash_equals($expected, $signature)) {
                return response('Invalid signature', 400);
            }
            $event = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
            if (!is_array($event) || !is_string($event['id'] ?? null) || !preg_match('/\Aevt_[A-Za-z0-9_-]{1,128}\z/', $event['id']) || !is_string($event['type'] ?? null)) {
                return response('Invalid event', 400);
            }
            if (!in_array($event['type'], ['payment.confirmed', 'invoice.overdue'], true)) {
                return response('Ignored', 200);
            }
            $data = $event['data'] ?? null;
            if (!is_array($data) || !is_string($data['merchantOrderId'] ?? null) || !preg_match('/\Apaymenter-([1-9][0-9]{0,17})-[a-f0-9]{32}\z/', $data['merchantOrderId'], $match)) {
                return response('Invalid reference', 400);
            }
            $invoiceId = $match[1];
            if (!$this->sessionId($data['checkoutSessionId'] ?? null) || ($data['paymentType'] ?? null) !== 'ONE_TIME' || !is_string($data['invoiceId'] ?? null) || !preg_match('/\A[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12}\z/', $data['invoiceId']) || !is_string($data['currency'] ?? null)) {
                return response('Invalid payment data', 400);
            }
            $status = $event['type'] === 'payment.confirmed' ? 'confirmed' : 'expired';
            if (($data['status'] ?? null) !== $status) {
                return response('Invalid payment status', 400);
            }
        } catch (Throwable $e) {
            return response('Invalid webhook', 400);
        }

        try {
            // data.amount and data.amountUsd are token/converted receivables, not
            // the invoice pricing total. Retrieve the immutable pricing snapshot.
            $session = $this->request('GET', self::API . '/' . rawurlencode($data['checkoutSessionId']));
        } catch (Throwable $e) {
            return response('Unable to verify checkout; retry later', 503);
        }

        try {
            $code = DB::transaction(function () use ($invoiceId, $event, $data, $session) {
                $invoice = Invoice::whereKey($invoiceId)->lockForUpdate()->first();
                $attempt = $invoice ? $this->state($invoice, $data['merchantOrderId']) : null;
                if (!$attempt || $attempt['reference'] !== $data['merchantOrderId']) {
                    return 409;
                }
                $this->validateSession($session, $attempt);
                if ($session['id'] !== $data['checkoutSessionId'] || $data['currency'] !== $attempt['currency'] || ($attempt['session_id'] !== null && $attempt['session_id'] !== $session['id']) || (isset($attempt['invoice_id']) && $attempt['invoice_id'] !== $data['invoiceId'])) {
                    return 409;
                }
                // Check this before the current balance, which is zero after the
                // first successful application. Replays cause no helper/event calls.
                if ($attempt['status'] === 'confirmed') {
                    return 200;
                }
                if ($event['type'] === 'payment.confirmed' && $session['status'] !== 'COMPLETED') {
                    return 409;
                }
                if ($invoice->status !== 'pending' || $invoice->currency_code !== $attempt['currency'] || $this->money($invoice->remaining) !== $attempt['amount']) {
                    return 409;
                }

                $attempt['session_id'] = $session['id'];
                $attempt['invoice_id'] = $data['invoiceId'];
                $attempt['event_id'] = $event['id'];
                if ($event['type'] === 'invoice.overdue') {
                    // No succeeded/processing transaction was created at checkout.
                    // Leave the invoice unpaid and permit a fresh payment attempt.
                    $attempt['status'] = 'expired';
                } else {
                    ExtensionHelper::addPayment($invoice->id, 'Yolfi', $attempt['amount'], transactionId: 'yolfi:' . $session['id']);
                    $attempt['status'] = 'confirmed';
                }
                $this->saveState($invoice, $attempt);

                return 200;
            });

            return response($code === 200 ? 'Accepted' : 'Payment reference or balance mismatch', $code);
        } catch (InvalidArgumentException $e) {
            return response('Payment reference or balance mismatch', 409);
        } catch (Throwable $e) {
            return response('Unable to record payment; retry later', 503);
        }
    }

    private function required(string $name): string
    {
        $value = $this->config($name);
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException('Yolfi API key and webhook signing secret must be configured.');
        }

        return $value;
    }

    private function request(string $method, string $url, array $payload = [], array $headers = []): array
    {
        try {
            $http = Http::withToken($this->required('api_key'))->acceptJson()->asJson()
                ->connectTimeout(5)->timeout(15)->withoutRedirecting()->withHeaders($headers);
            $response = $method === 'POST' ? $http->post($url, $payload) : $http->get($url);
            $body = $response->json();
            if (!$response->successful() || !is_array($body) || ($body['success'] ?? null) !== true || !is_array($body['data'] ?? null) || array_is_list($body['data'])) {
                throw new RuntimeException('Invalid API response');
            }

            return $body['data'];
        } catch (Throwable $e) {
            // Never expose an API response, request headers or HTTP exception text.
            throw new RuntimeException('Yolfi checkout request failed. Retry later.');
        }
    }

    private function validateSession(array $session, array $attempt): void
    {
        if (!$this->sessionId($session['id'] ?? null) || ($session['object'] ?? null) !== 'checkout_session' || !in_array($session['status'] ?? null, ['OPEN', 'PROCESSING', 'COMPLETED', 'EXPIRED'], true) || ($session['merchantOrderId'] ?? null) !== $attempt['reference'] || ($session['currency'] ?? null) !== $attempt['currency'] || $this->money($session['amount'] ?? null) !== $attempt['amount']) {
            throw new InvalidArgumentException('Checkout pricing or reference mismatch.');
        }
        $url = isset($session['url']) && is_string($session['url']) ? parse_url($session['url']) : false;
        if (!$url || ($url['scheme'] ?? null) !== 'https' || ($url['host'] ?? null) !== 'pay.yolfi.com' || isset($url['user']) || isset($url['pass']) || isset($url['port']) || isset($url['fragment'])) {
            throw new InvalidArgumentException('Invalid hosted checkout URL.');
        }
    }

    private function sessionId($value): bool
    {
        return is_string($value) && preg_match('/\Aycs_[A-Za-z0-9_-]{1,128}\z/', $value) === 1;
    }

    private function state(Invoice $invoice, ?string $reference = null): ?array
    {
        $key = $reference === null ? self::PROPERTY : self::PROPERTY . '_' . hash('sha256', $reference);
        $value = $invoice->properties()->where('key', $key)->value('value');
        if ($value === null) {
            return null;
        }
        $state = json_decode($value, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($state)) {
            throw new RuntimeException('Invalid checkout state.');
        }

        return $state;
    }

    private function saveState(Invoice $invoice, array $state, bool $makeActive = false): void
    {
        // Retain expired attempt correlation for delayed signed confirmations.
        // Updating an old attempt must never replace a newer active request.
        $value = json_encode($state, JSON_THROW_ON_ERROR);
        $invoice->properties()->updateOrCreate([
            'key' => self::PROPERTY . '_' . hash('sha256', $state['reference']),
        ], ['value' => $value]);
        $active = $this->state($invoice);
        if ($makeActive || ($active && $active['reference'] === $state['reference'])) {
            $invoice->properties()->updateOrCreate(['key' => self::PROPERTY], ['value' => $value]);
        }
    }

    private function money($value): string
    {
        // Paymenter supplies major-unit floats at the pay boundary and decimal:2
        // values in persistence. Reject sub-cent values instead of truncating them.
        if (is_float($value)) {
            if (!is_finite($value) || $value < 0.01 || $value > 999999999.99 || abs($value - round($value, 2)) > 0.000001) {
                throw new InvalidArgumentException('Invalid invoice pricing amount.');
            }
            $value = number_format($value, 2, '.', '');
        } elseif (is_int($value)) {
            $value = (string) $value;
        }
        if (!is_string($value) || !preg_match('/\A(0|[1-9][0-9]{0,8})(?:\.([0-9]{1,2})0*)?\z/', $value, $match)) {
            throw new InvalidArgumentException('Invalid invoice pricing amount.');
        }
        $amount = $match[1] . '.' . str_pad($match[2] ?? '', 2, '0');
        if ($amount === '0.00') {
            throw new InvalidArgumentException('Invoice pricing amount must be at least 0.01.');
        }

        return $amount;
    }
}
