<?php

return [
    'role' => [
        '*' => 'All Permissions',

        'admin' => [
            'settings' => [
                'view' => 'View Settings',
                'update' => 'Update Settings',
            ],
            'users' => [
                'create' => 'Create Users',
                'update' => 'Update Users',
                'viewAny' => 'View Users',
                'delete' => 'Delete Users',
                'impersonate' => 'Impersonate Users',
            ],
            'invoices' => [
                'create' => 'Create Invoices',
                'update' => 'Update Invoices',
                'viewAny' => 'View Invoices',
                'delete' => 'Delete Invoices',
                'deleteAny' => 'Bulk Delete Invoices',
            ],
            'invoice_transactions' => [
                'create' => 'Create Invoice Transactions',
                'update' => 'Update Invoice Transactions',
                'viewAny' => 'View Invoice Transactions',
                'delete' => 'Delete Invoice Transactions',
                'deleteAny' => 'Bulk Delete Invoice Transactions',
            ],
            'products' => [
                'create' => 'Create Products',
                'update' => 'Update Products',
                'viewAny' => 'View Products',
                'delete' => 'Delete Products',
                'deleteAny' => 'Bulk Delete Products',
            ],
            'categories' => [
                'create' => 'Create Categories',
                'update' => 'Update Categories',
                'viewAny' => 'View Categories',
                'delete' => 'Delete Categories',
                'deleteAny' => 'Bulk Delete Categories',
            ],
            'tickets' => [
                'create' => 'Create Tickets',
                'update' => 'Update Tickets',
                'viewAny' => 'View Tickets',
                'delete' => 'Delete Tickets',
                'deleteAny' => 'Bulk Delete Tickets',
            ],
            'ticket_messages' => [
                'delete' => 'Delete Ticket Messages',
            ],
            'orders' => [
                'create' => 'Create Orders',
                'update' => 'Update Orders',
                'viewAny' => 'View Orders',
                'delete' => 'Delete Orders',
                'deleteAny' => 'Bulk Delete Orders',
            ],
            'services' => [
                'create' => 'Create Services',
                'update' => 'Update Services',
                'viewAny' => 'View Services',
                'delete' => 'Delete Services',
                'deleteAny' => 'Bulk Delete Services',
            ],
            'service_cancellations' => [
                'create' => 'Create Service Cancellations',
                'update' => 'Update Service Cancellations',
                'viewAny' => 'View Service Cancellations',
                'delete' => 'Delete Service Cancellations',
                'deleteAny' => 'Bulk Delete Service Cancellations',
            ],
            'custom_properties' => [
                'create' => 'Create Custom Properties',
                'update' => 'Update Custom Properties',
                'viewAny' => 'View Custom Properties',
                'delete' => 'Delete Custom Properties',
                'deleteAny' => 'Bulk Delete Custom Properties',
            ],
            'currencies' => [
                'create' => 'Create Currencies',
                'update' => 'Update Currencies',
                'viewAny' => 'View Currencies',
                'delete' => 'Delete Currencies',
                'deleteAny' => 'Bulk Delete Currencies',
            ],
            'audits' => [
                'viewAny' => 'View Audits',
            ],
            'cron_stats' => [
                'view' => 'View Cron Stats',
            ],
            'debug_logs' => [
                'view' => 'View Debug Logs',
            ],
            'roles' => [
                'create' => 'Create Roles',
                'update' => 'Update Roles',
                'viewAny' => 'View Roles',
                'delete' => 'Delete Roles',
                'deleteAny' => 'Bulk Delete Roles',
            ],
            'coupons' => [
                'create' => 'Create Coupons',
                'update' => 'Update Coupons',
                'viewAny' => 'View Coupons',
                'delete' => 'Delete Coupons',
                'deleteAny' => 'Bulk Delete Coupons',
            ],
            'config_options' => [
                'create' => 'Create Config Options',
                'update' => 'Update Config Options',
                'viewAny' => 'View Config Options',
                'delete' => 'Delete Config Options',
                'deleteAny' => 'Bulk Delete Config Options',
            ],
            'tax_rates' => [
                'create' => 'Create Tax Rates',
                'update' => 'Update Tax Rates',
                'viewAny' => 'View Tax Rates',
                'delete' => 'Delete Tax Rates',
                'deleteAny' => 'Bulk Delete Tax Rates',
            ],
            'gateways' => [
                'create' => 'Create Gateways',
                'update' => 'Update Gateways',
                'viewAny' => 'View Gateways',
                'delete' => 'Delete Gateways',
                'deleteAny' => 'Bulk Delete Gateways',
            ],
            'servers' => [
                'create' => 'Create Servers',
                'update' => 'Update Servers',
                'viewAny' => 'View Servers',
                'delete' => 'Delete Servers',
                'deleteAny' => 'Bulk Delete Servers',
            ],
            'api_keys' => [
                'create' => 'Create API Keys',
                'update' => 'Update API Keys',
                'viewAny' => 'View API Keys',
                'delete' => 'Delete API Keys',
            ],
            'extensions' => [
                'update' => 'Update Extensions',
                'viewAny' => 'View Extensions',
                'install' => 'Install Extensions',
                'delete' => 'Delete Extensions',
            ],
            'failed_jobs' => [
                'viewAny' => 'View Failed Jobs',
            ],
            'email_logs' => [
                'viewAny' => 'View Email Logs',
                'view' => 'View Email Log',
            ],
            'email_templates' => [
                'create' => 'Create Email Templates',
                'update' => 'Update Email Templates',
                'viewAny' => 'View Email Templates',
                'delete' => 'Delete Email Templates',
                'deleteAny' => 'Bulk Delete Email Templates',
            ],
            'oauth_clients' => [
                'create' => 'Create OAuth Clients',
                'update' => 'Update OAuth Clients',
                'viewAny' => 'View OAuth Clients',
                'delete' => 'Delete OAuth Clients',
                'deleteAny' => 'Bulk Delete OAuth Clients',
            ],
            'widgets' => [
                'revenue' => 'View Revenue Widget',
                'overview' => 'View Overview Widget',
                'support' => 'View Tickets Widget',
                'active_users' => 'View Active Users Widget',
            ],
            'updates' => [
                'update' => 'View and Update Application',
            ],
        ],
    ],
    'api' => [
        'admin' => [
            'users' => [
                'create' => 'Create Users',
                'update' => 'Update Users',
                'view' => 'View Users',
                'delete' => 'Delete Users',
                'impersonate' => 'Impersonate Users',
            ],
            'invoices' => [
                'create' => 'Create Invoices',
                'update' => 'Update Invoices',
                'view' => 'View Invoices',
                'delete' => 'Delete Invoices',
            ],
            'invoice_items' => [
                'create' => 'Create Invoice Items',
                'update' => 'Update Invoice Items',
                'view' => 'View Invoice Items',
                'delete' => 'Delete Invoice Items',
            ],
            'tickets' => [
                'create' => 'Create Tickets',
                'update' => 'Update Tickets',
                'view' => 'View Tickets',
                'delete' => 'Delete Tickets',
            ],
            'ticket_messages' => [
                'create' => 'Create Ticket Messages',
                'view' => 'View Ticket Messages',
                'delete' => 'Delete Ticket Messages',
            ],
            'orders' => [
                'create' => 'Create Orders',
                'update' => 'Update Orders',
                'view' => 'View Orders',
                'delete' => 'Delete Orders',
            ],
            'services' => [
                'create' => 'Create Services',
                'update' => 'Update Services',
                'view' => 'View Services',
                'delete' => 'Delete Services',
            ],
            'coupons' => [
                'create' => 'Create Coupons',
                'update' => 'Update Coupons',
                'view' => 'View Coupons',
                'delete' => 'Delete Coupons',
            ],
            'credits' => [
                'create' => 'Create Credits',
                'update' => 'Update Credits',
                'view' => 'View Credits',
                'delete' => 'Delete Credits',
            ],
            'products' => [
                'view' => 'View Products',
            ],
            'categories' => [
                'view' => 'View Categories',
            ],
            'properties' => [
                'view' => 'View Custom Properties',
            ],
            'roles' => [
                'view' => 'View Roles',
            ],
        ],
    ],
];
