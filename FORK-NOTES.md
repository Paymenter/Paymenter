# Paymenter Fork Notes — ObsidianNetwork/paymenter

This file records deliberate divergences from upstream `paymenter/paymenter`.
Each entry lists the plan ID, commit SHA(s), rationale, and any follow-up work.

---

## dp-core-01 — Dynamic Slider Core Pricing Patches

**Branch:** `dp-core-01-pricing-patches`
**Base:** `dynamic-slider/1.4.7`
**Status:** Merged (see squash SHA below after merge)

### Rationale

The dp-07 Phase 1 investigation identified five structural defects in Paymenter
core's `dynamic_slider` config option type. The DynamicPterodactyl extension was
compensating for some of them (badly) and ignoring others. Until these are fixed
in core, the extension cannot retire its `PricingCalculatorService`, and renewal
billing for any dynamic-slider product is broken.

These patches are **fork-only** — not submitted upstream — because they introduce
fork-specific column additions (`plans.dynamic_slider_base_price`,
`service_configs.slider_value`) and artisan commands that are specific to our
migration path.

### Patches

#### Patch 5 — Server-side pricing schema validation
**Commits:** `88c78e84`, `3a2f563b`
**Files:** `app/Rules/DynamicSliderPricingRule.php`, `app/Admin/Resources/ConfigOptionResource.php`,
`app/Admin/Resources/ConfigOptionResource/Pages/{Create,Edit}ConfigOption.php`,
`app/Admin/Resources/ConfigOptionResource/Concerns/ValidatesDynamicSliderPricing.php`

Adds `DynamicSliderPricingRule` (validates model name, required keys, non-negative
rates, strictly-ascending tier `up_to` values) and wires it into the Filament
Create/Edit pages via a shared trait. Prevents malformed pricing from reaching the DB.

#### Patch 2 — Reject unknown pricing models explicitly
**Commit:** `6e0d439c`
**Files:** `app/Models/ConfigOption.php`

Replaces the fall-through `default` in `calculateDynamicPrice()`'s match statement
with an explicit `throw new \InvalidArgumentException(...)`. Surfaces misconfiguration
at checkout rather than silently mispricing.

#### Patch 1 — Separate shared product base from per-slider marginal charges
**Commit:** `24ed6262`
**Files:** `app/Models/ConfigOption.php`, `app/Models/Plan.php`,
`app/Livewire/Products/Checkout.php`, `app/Models/CartItem.php`,
`database/migrations/2026_04_23_022732_add_dynamic_slider_base_price_to_plans.php`,
`app/Console/Commands/MigrateSliderBasePrice.php`

**Problem:** A product with N sliders each having `base_price=5` charged
`plan_price + N×5` instead of `plan_price + 5`.

**Fix:**
- Renames `calculateDynamicPrice()` → `calculateDynamicPriceDelta()` (marginal only).
- Keeps `calculateDynamicPrice()` as a `@deprecated` alias returning `delta + base`
  for one release cycle (backward-compat for external callers).
- Adds `plans.dynamic_slider_base_price` nullable decimal(10,2) column.
- Adds `Plan::dynamicSliderBasePrice()` accessor.
- Checkout and CartItem compute `plan_price + plan_base + sum(deltas)`.
- `paymenter:migrate-slider-base-price` artisan command collapses duplicate
  per-slider `base_price` values into the plan-level column (dry-run default).

#### Patch 4 — Exclude dynamic_slider from upgradable toggle
**Commit:** `7b304fa8`
**Files:** `app/Admin/Resources/ConfigOptionResource.php`

Hides the "upgradable" checkbox for `dynamic_slider` config options and shows a
`Placeholder` notice: "Dynamic sliders are not yet upgradable." Prevents admins
from enabling an upgrade path that the upgrade logic cannot handle.

**Follow-up:** Numeric-slider upgrade semantics (second cut) belongs in its own plan.

#### Patch 3 — Make service recalculation and renewal invoicing slider-aware
**Commit:** `84740933`
**Files:** `app/Livewire/Cart.php`, `app/Models/Service.php`,
`app/Models/ServiceConfig.php`,
`database/migrations/2026_04_23_025252_add_slider_value_to_service_configs.php`,
`app/Console/Commands/BackfillSliderConfigValues.php`

**Problem:** `Service::calculatePrice()` iterates `configValue` rows only. Dynamic
slider selections were stored as service properties, invisible to recalc. Renewal
invoicing reused the stale `service->price` or called `calculatePrice()` which
returned the wrong amount.

**Fix (approach 3a — dual-write):**
- Adds `service_configs.slider_value decimal(12,4) nullable`; makes
  `config_value_id` nullable so slider rows can omit the child-option FK.
- `Cart.php` dual-writes slider selections as `ServiceConfig` rows on checkout.
  Property-store writes preserved for backward-compat reads.
- `Service::calculatePrice()` reads `slider_value` for dynamic slider configs,
  adds `plan->dynamicSliderBasePrice()` once, and logs a `Log::warning` if the
  property value diverges from `slider_value` (observable drift, not a throw).
- `paymenter:backfill-slider-config-values` artisan command writes missing
  `ServiceConfig` rows for existing active/suspended services (dry-run default).

**Pre-Patch-3 gate:** Reported to orchestrator before committing. TODO continuation
directive received — interpreted as implicit approval (dev environment, no
production data to check).

### Follow-ups

- **dp-09:** Delete extension's `PricingCalculatorService` now that core handles pricing.
- **dp-10:** Numeric-slider upgrade semantics (second cut of Patch 4).
- **dp-11:** Deprecate `calculateDynamicPrice()` alias after one release cycle.
- **dp-12:** Integer-cents / money-library migration (mentioned in audit).

---

## dp-08 — (previous plan, already merged)

See `PROGRESS.md` in the DynamicPterodactyl extension repo for details.
SHA: `5a28acb` on `dynamic-slider`.
