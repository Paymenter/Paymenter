<?php

return [
    'settings' => 'Settings',
    'saved_successfully' => 'Saved successfully!',
    
    // Group categories
    'groups' => [
        'general' => 'General',
        'security' => 'Security',
        'social-login' => 'Social Login',
        'tax' => 'Tax',
        'mail' => 'Mail',
        'tickets' => 'Tickets',
        'cronjob' => 'Cron Job',
        'credits' => 'Credits',
        'theme' => 'Theme',
        'invoices' => 'Invoices',
        'other' => 'Other',
    ],
    
    // Labels
    'labels' => [
        // General
        'company_name' => 'Company Name',
        'timezone' => 'Timezone',
        'app_language' => 'Default Language',
        'allowed_languages' => 'Allowed Languages',
        'app_url' => 'App URL',
        'logo' => 'Logo (Light Mode)',
        'logo_dark' => 'Logo (Dark Mode)',
        'favicon' => 'Favicon',
        'system_email_address' => 'System Email Address',
        'tos' => 'Terms of Service',
        
        // Security
        'captcha' => 'Captcha',
        'captcha_site_key' => 'Captcha Site Key',
        'captcha_secret' => 'Captcha Secret',
        'trusted_proxies' => 'Trusted Proxies',
        'session_validation' => 'Session Validation',
        
        // Social
        'oauth_google' => 'Google Enabled',
        'oauth_google_client_id' => 'Google Client ID',
        'oauth_google_client_secret' => 'Google Client Secret',
        'oauth_github' => 'GitHub Enabled',
        'oauth_github_client_id' => 'GitHub Client ID',
        'oauth_github_client_secret' => 'GitHub Client Secret',
        'oauth_discord' => 'Discord Enabled',
        'oauth_discord_client_id' => 'Discord Client ID',
        'oauth_discord_client_secret' => 'Discord Client Secret',
        
        // Tax
        'tax_enabled' => 'Tax Enabled',
        'tax_type' => 'Tax Type',
        
        // Mail
        'mail_disable' => 'Disable Mail',
        'mail_must_verify' => 'Users must verify email before buying',
        'mail_host' => 'Mail Host',
        'mail_port' => 'Mail Port',
        'mail_username' => 'Mail Username',
        'mail_password' => 'Mail Password',
        'mail_encryption' => 'Mail Encryption',
        'mail_from_address' => 'Mail From Address',
        'mail_from_name' => 'Mail From Name',
        'mail_header' => 'Header',
        'mail_footer' => 'Footer',
        'mail_css' => 'Mail CSS',
        
        // Tickets
        'tickets_disabled' => 'Disable Tickets',
        'ticket_departments' => 'Ticket Departments',
        'ticket_client_closing_disabled' => 'Disallow clients from closing tickets',
        'ticket_mail_piping' => 'Email Piping',
        'ticket_mail_host' => 'Email Host',
        'ticket_mail_port' => 'Email Port',
        'ticket_mail_email' => 'Email Address',
        'ticket_mail_password' => 'Email Password',
        
        // Cronjob
        'cronjob_time' => 'Cron Job Time',
        'cronjob_invoice' => 'Send invoice if due date is x days away',
        'cronjob_invoice_reminder' => 'Send invoice reminder if due date is x days away',
        'cronjob_order_cancel' => 'Cancel order if pending for x days',
        'cronjob_order_suspend' => 'Suspend server if invoice is x days overdue',
        'cronjob_order_terminate' => 'Delete server if invoice is x days overdue (also cancels the invoice)',
        'cronjob_delete_email_logs' => 'Delete email logs older than x days',
        'cronjob_close_ticket' => 'Close tickets if no response for x days',
        
        // Credits
        'credits_enabled' => 'Credits Enabled',
        'credits_minimum_deposit' => 'Minimum Deposit',
        'credits_maximum_deposit' => 'Maximum Deposit',
        'credits_maximum_credit' => 'Maximum Credit',
        'credits_auto_use' => 'Automatically use credits',
        'credits_on_downgrade' => 'Enable credits on service downgrade',
        
        // Invoices
        'bill_to_text' => 'Bill To Text',
        'invoice_number' => 'Invoice Number',
        'invoice_number_padding' => 'Invoice Number Padding',
        'invoice_number_format' => 'Invoice number format',
        'invoice_proforma' => 'Proforma Invoices',
        'invoice_snapshot' => 'Invoice Snapshot',
        
        // Other
        'gravatar_default' => 'Gravatar Default',
        'default_currency' => 'Default Currency',
        'registration_disabled' => 'Disable User Registration',
        'pagination' => 'Pagination',
        'debug' => 'Debug Mode',
        
        // Theme / Colors
        'theme' => 'Theme',
    ],
    
    // Options for select lists
    'options' => [
        'captcha' => [
            'disabled' => 'Disabled',
            'recaptcha-v2' => 'Google reCAPTCHA v2',
            'recaptcha-v3' => 'Google reCAPTCHA v3',
            'turnstile' => 'Cloudflare Turnstile',
            'hcaptcha' => 'hCaptcha',
        ],
        'session_validation' => [
            'none' => 'None',
            'ip_admin' => 'Lock session to IP address (Admin)',
            'ip_client' => 'Lock session to IP address (Client)',
            'ip_both' => 'Lock session to IP address (Admin & Client)',
            'user_agent_admin' => 'Lock session to User Agent (Admin)',
            'user_agent_client' => 'Lock session to User Agent (Client)',
            'user_agent' => 'Lock session to User Agent (Admin & Client)',
            'ip_user_agent_admin' => 'Lock session to IP address and User Agent (Admin)',
            'ip_user_agent_client' => 'Lock session to IP address and User Agent (Client)',
            'ip_user_agent_both' => 'Lock session to IP address and User Agent (Admin & Client)',
        ],
        'tax_type' => [
            'inclusive' => 'Inclusive (Price includes tax)',
            'exclusive' => 'Exclusive (Price does not include tax)',
        ],
        'gravatar_default' => [
            'mp' => 'Mystery Person',
            'identicon' => 'Identicon',
            'monsterid' => 'Monster',
            'wavatar' => 'Wavatar',
            'retro' => 'Retro',
            'robohash' => 'Robohash',
        ],
    ],
];
