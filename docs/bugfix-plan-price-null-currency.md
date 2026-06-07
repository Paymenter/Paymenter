# Bugfix — 500 "Attempt to read property `setup_fee` on null" (Pricing / Cart / Checkout)

**Severity:** High — customer-facing 500, blocks purchase.
**Status:** Fixed (this branch).
**Components:** `Plan::price()`, `Checkout`, `CartItem`.

## Symptom

```
Attempt to read property "setup_fee" on null
app/Models/Plan.php:59
```

Trigger (`Plan::price()` before fix):

```php
$price = $this->prices->where('currency_code', $currency)->first(); // may be null
return new PriceClass((object) [
    'price'     => $price,
    'setup_fee' => $price->setup_fee, // <- null deref when plan has no price in $currency
    'currency'  => $price->currency,
]);
```

## Root cause

`Plan::price($currency)` returns `null` when the plan has no price row in the
requested currency, then dereferences it. This is **by design data**, not bad
data: some services are sold only in IDR, some only in LAB. The codebase already
has `Product::availablePlans($currency)` to filter for exactly this — but two
call paths bypassed it and called `Plan::price()` with the wrong/implicit
currency:

1. **Checkout** (`app/Livewire/Products/Checkout.php`) selected the default plan
   from `$product->plans` (ALL plans) instead of `availablePlans()`. A
   single-currency plan could be picked while the session ran another currency,
   then `updatePricing()` called `price()` → null → 500.
2. **CartItem** (`app/Models/CartItem.php`) called `plan->price()` with no
   currency. The cart locks its own `currency_code` at creation
   (`Cart::createCart()`), but the item resolved price against the *session*
   currency. Cart created in LAB, rendered in an IDR session → mismatch → 500.

Real incident: cart #90 (currency `LAB`, plan priced only in LAB) rendered in a
default (IDR) session.

## Fix — three layers

### 1. Checkout selects a plan priced in the active currency, auto-switches if needed

`app/Livewire/Products/Checkout.php` — `mount()` now selects from
`availablePlans()` and calls a new `autoSwitchCurrencyIfNeeded()` that switches
the session currency to one supporting the requested/first plan (mirrors
`Products\Show::autoSwitchCurrencyIfNeeded`). No-op when the cart already holds
items (its currency is locked). If the product has no plan priced in any
currency, it redirects to the product page (treated as unavailable).

```php
$this->autoSwitchCurrencyIfNeeded();
$available = $this->product->availablePlans();
$this->plan = ($this->plan_id ? $available->firstWhere('id', $this->plan_id) : null) ?? $available->first();
if (!$this->plan) {
    return $this->redirect(route('products.show', [...]), true);
}
```

UX decision (per product owner): **auto-switch currency** to the one the product
supports, then render normally.

### 2. CartItem prices against the cart currency, not the session

`app/Models/CartItem.php` (lines 51, 52, 81):

```php
$this->plan->price($this->cart->currency_code)
```

Keeps item pricing consistent with the cart's locked currency.

### 3. `Plan::price()` safety net — never crash on a missing price

`app/Models/Plan.php`:

```php
$price = $this->prices->where('currency_code', $currency)->first();
if (!$price) {
    return new PriceClass(['currency' => Currency::find($currency)]);
}
```

Catches any remaining path (legacy links, old services). `App\Models\Currency`
resolves in-namespace (same as the existing free-plan branch).

## Temporary mitigation already applied in production

Cart #90 deleted with its items to stop the user's 500. The stale browser cookie
is harmless — `Cart::getOnce()` no longer finds the row and treats it as empty.

## Recommended follow-up

1. Scan `carts`/`cart_items` for items whose plan has no price in the cart's
   `currency_code`; clean up before wide rollout.
2. Block adding an item to a cart when the plan has no price in the cart currency.
3. Regression test: render checkout & cart for a single-currency plan under a
   different session currency → must not 500.

## Affected files

| File | Lines | Change |
|------|-------|--------|
| `app/Models/Plan.php` | 55–61 | null-safe `price()` |
| `app/Livewire/Products/Checkout.php` | mount + `autoSwitchCurrencyIfNeeded()` | select from `availablePlans()`, auto-switch currency |
| `app/Models/CartItem.php` | 51, 52, 81 | price against cart currency |
