<?php

return [
    'services' => 'Services',
    'product' => 'Product',
    'price' => 'Price',
    'status' => 'Status',
    'name' => 'Name',
    'actions' => 'Actions',
    'view' => 'View',

    'product_details' => 'Product Details',
    'billing_cycle' => 'Billing Cycle',
    'cancel' => 'Cancel',
    'cancellation' => 'Cancellation of :service',
    'cancel_are_you_sure' => 'Are you sure you want to cancel this service?',
    'cancel_reason' => 'Reason for cancellation',
    'cancel_type' => 'Cancellation Type',
    'cancel_immediate' => 'Cancel immediate',
    'cancel_end_of_period' => 'Cancel at the end of the billing period',
    'cancel_immediate_warning' => 'When you press the button below, the service will be cancelled immediately and you will not be able to use it anymore.',
    'cancellation_requested' => 'Cancellation requested',

    'current_plan' => 'Current Plan',
    'new_plan' => 'New Plan',
    'change_plan' => 'Change Plan',
    'current_price' => 'Current Price',
    'new_price' => 'New Price',
    'upgrade' => 'Upgrade',
    'upgrade_summary' => 'Upgrade Summary',
    'total_today' => 'Total Today',
    'upgrade_service' => 'Upgrade Service',
    'upgrade_choose_product' => 'Choose a product to upgrade to',
    'upgrade_choose_config' => 'Choose the configuration for the upgrade',
    'next_step' => 'Next Step',

    'upgrade_pending' => 'You cannot upgrade whilst there is already an upgrade / downgrade invoice open',

    'outstanding_invoice' => 'You have an outstanding invoice.',
    'view_and_pay' => 'Click here to view and pay',

    'statuses' => [
        'pending' => 'Pending',
        'active' => 'Active',
        'cancelled' => 'Cancelled',
        'suspended' => 'Suspended',
        'cancellation_pending' => 'Cancellation Pending',
    ],
    'billing_cycles' => [
        'day' => 'day|days',
        'week' => 'week|weeks',
        'month' => 'month|months',
        'year' => 'year|years',
    ],
    'every_period' => 'Every :period :unit',
    'price_every_period' => ':price per :period :unit',
    'price_one_time' => ':price one time',
    'expires_at' => 'Expires at',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
