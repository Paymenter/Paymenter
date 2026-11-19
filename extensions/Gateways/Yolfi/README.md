# Yolfi gateway

One-time hosted checkout for the full outstanding invoice balance, using the
native [Checkout Sessions API](https://docs.yolfi.com/en/checkout/api-reference).
This is a Paymenter gateway extension, not a card processor or a Stripe adapter.

## Setup

1. Add the Yolfi gateway in Paymenter and configure its API key and webhook
   signing secret. Both settings use Paymenter's encrypted-setting mechanism.
   The webhook secret is the native endpoint signing secret, not the API key.
2. In Yolfi configure a native JSON webhook endpoint at
   `https://merchant.example.com/extensions/gateways/yolfi/webhook` (replace the
   example origin with your Paymenter HTTPS origin). Subscribe to
   `payment.confirmed` and `invoice.overdue`. Do not select a compatibility
   adapter format. See [webhook data](https://docs.yolfi.com/en/webhooks/webhook-details)
   and [signatures](https://docs.yolfi.com/en/webhooks/webhook-signatures).
3. Use a supported invoice pricing currency: USD, EUR, CNY, RUB, INR, TRY, KRW
   or JPY. Totals are decimal major units, including JPY; never multiply by 100
   or send token amounts. The supported balance range is 0.01–999999999.99 with
   at most two significant decimal places, matching Paymenter's money basis.

The authenticated API destination is fixed to `https://app.yolfi.com`. Returned
checkout URLs must use the separate documented `https://pay.yolfi.com` origin,
without credentials, explicit ports or fragments. No customer name, email, cart
or address is sent.

## Confirmation and retries

- Redirects, the `checkPayment` return flag and session `COMPLETED` status never
  record payment. Cancel returns to the still-unpaid invoice. Completion waits
  for an authenticated native `payment.confirmed` event.
- The adapter verifies raw-body base64 HMAC-SHA256 with constant-time comparison,
  then retrieves the authenticated session. It matches session identity, stored
  random merchant reference, immutable pricing amount/currency, payment type,
  signed invoice identity and the current outstanding balance. Webhook `amount`
  and `amountUsd` are not used as the invoice pricing amount.
- The immutable request is persisted in invoice properties before HTTP; retries
  retain its reference, payload and idempotency key even after a lost response.
  HTTP has bounded connect/read timeouts and no redirects. Non-2xx and malformed
  responses fail closed without exposing response bodies or credentials.
- A row-locked transaction records the payment and confirmation state together.
  Repeated delivery, including another event ID for the same session, does not
  call the payment helper again. Failed local writes remain retryable.
- Overdue events leave the invoice unpaid and permit a fresh checkout. If the
  API instead reports session expiration, retry after the expiration message.
  Old attempt references remain available for delayed signed confirmations;
  they cannot overwrite the newer active attempt. Another payment after the
  balance has been satisfied is rejected for manual reconciliation.
- HTTP 503 responses invite webhook retries. HTTP 400/409 responses never credit
  the invoice: inspect configuration, signature, references and balance. Changed
  balances/currencies, cancelled invoices and additional actual payments require
  manual reconciliation; never treat an error or return URL as payment proof.

No card collection, automatic renewal, billing agreements, partial/underpayment
acceptance, refunds, history sync, or converted/token-amount crediting is offered.
The inherited gateway contract reports refunds and billing agreements unsupported.
Invoices generated later by recurring services still require a separate one-time
checkout. Do not remove the saved attempt properties while confirmations can be
retried.

## Focused tests

From the repository root, run with PHP 8.3 or newer:

```
php -n tests/Standalone/YolfiGatewayTest.php
```

This dependency-free runner requires the actual gateway, route definition and
upstream Gateway/Extension base classes. Only HTTP, routing helpers, invoice
persistence/locking and the payment helper are doubles. API responses and payment
events are synthetic test fixtures; no network, real account or database is used.
Tests cover successful checkout/confirmation, exact-byte signatures, tampering,
pricing/reference mismatches, unsupported inputs, errors/timeouts, idempotent
retries, replay protection, rollback, expiration, delayed confirmation and the
late-create/webhook race. Checks explicitly throw rather than relying on PHP's
optional assertion configuration. This is not a full application or concurrent
database test, upstream CI result, or live-payment verification.
