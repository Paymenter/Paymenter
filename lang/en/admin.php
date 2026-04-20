<?php

return [

    'cronjob' => [
        'invoices_created' => 'Invoices created',
        'orders_cancelled' => 'Orders cancelled',
        'upgrade_invoices_updated' => 'Upgrade invoices updated',
        'services_suspended' => 'Services suspended',
        'services_terminated' => 'Services terminated',
        'tickets_closed' => 'Tickets closed',
        'email_logs_deleted' => 'Email logs deleted',
    ],

    'coupon' => [
        'restrict_to_role' => 'Restrict to Role',
        'restrict_to_role_placeholder' => 'Public (any user can apply this coupon)',
        'restrict_to_role_helper' => 'If set, only users with this role can apply the coupon. Leave empty for public coupons.',
        'restrict_to_role_column' => 'Restricted To',
        'restrict_to_role_public' => 'Public',
        'apply_after_tax' => 'Apply After Tax',
        'apply_after_tax_helper' => 'When enabled, this discount is subtracted after tax. The discount amount is still calculated on the pre-tax base.',
    ],

    'invoice' => [
        'coupon' => 'Coupon',
        'coupon_placeholder' => 'Select a coupon to apply a discount',
        'coupon_helper' => 'Selecting a coupon appends a discount line item based on the current items total.',
        'coupon_expired' => 'This coupon has expired.',
        'coupon_not_active' => 'This coupon is not active yet.',
        'coupon_max_uses' => 'This coupon has reached its maximum number of uses.',
        'coupon_no_items' => 'Add at least one item before applying a coupon.',
        'coupon_no_discount' => 'This coupon does not apply to the current items.',
        'coupon_applied' => 'Coupon applied, a discount line item has been added.',
    ],

    'invoice_item' => [
        'unit' => 'Unit',
        'unit_placeholder' => 'e.g. hours, GB (leave empty for plain quantity)',
        'unit_helper' => 'Optional label shown next to the quantity (e.g. "62 hours").',
    ],
];
