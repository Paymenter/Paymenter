<?php

return [
    'account' => '账户',
    'personal_details' => '个人资料',
    'security' => '安全中心',
    'credits' => '余额',

    'change_password' => '修改密码',

    'two_factor_authentication' => '双因素认证',
    'two_factor_authentication_description' => '启用双因素认证，为您的账户增加一层安全保护。',
    'two_factor_authentication_enabled' => '您的账户已启用双因素认证。',
    'two_factor_authentication_enable' => '启用双因素认证',
    'two_factor_authentication_disable' => '停用双因素认证',
    'two_factor_authentication_disable_description' => '您确定要停用双因素认证吗？这将移除您账户的额外安全保护。',
    'two_factor_authentication_enable_description' => '若要启用双因素认证，您需要使用 Google Authenticator 或 Authy 等验证应用扫描下方的二维码。',
    'two_factor_authentication_qr_code' => '使用您的验证应用扫描下方的二维码：',
    'two_factor_authentication_secret' => '或手动输入以下代码：',

    'sessions' => '登录会话',
    'sessions_description' => '管理并退出您在其他浏览器和设备上的活跃登录会话。',
    'logout_sessions' => '退出此会话',

    'input' => [
        'current_password' => '当前密码',
        'current_password_placeholder' => '您当前的密码',
        'new_password' => '新密码',
        'new_password_placeholder' => '您的新密码',
        'confirm_password' => '确认密码',
        'confirm_password_placeholder' => '确认您的新密码',

        'two_factor_code' => '输入您的验证应用中的验证码',
        'two_factor_code_placeholder' => '您的双因素验证码',

        'currency' => '币种',
        'amount' => '金额',
        'payment_gateway' => '支付渠道',
    ],

    'notifications' => [
        'password_changed' => '密码已修改。',
        'password_incorrect' => '当前密码不正确。',
        'two_factor_enabled' => '双因素认证已启用。',
        'two_factor_disabled' => '双因素认证已停用。',
        'two_factor_code_incorrect' => '验证码不正确。',
        'session_logged_out' => '登录会话已退出。',
    ],

    'no_credit' => '您暂无可用余额。',
    'add_credit' => '充值余额',
    'credit_deposit' => '余额充值 (:currency)',

    'payment_methods' => '支付方式',
    'recent_transactions' => '最近交易',
    'saved_payment_methods' => '已保存的支付方式',
    'setup_payment_method' => '添加新的支付方式',
    'no_saved_payment_methods' => '您暂无已保存的支付方式。',
    'saved_payment_methods_description' => '管理您已保存的支付方式，以实现快速结账和自动支付。',
    'no_saved_payment_methods_description' => '您可以添加支付方式，让后续支付更快捷方便，并为您的服务启用自动支付功能。',
    'add_payment_method' => '添加支付方式',
    'payment_method_statuses' => [
        'active' => '已激活',
        'inactive' => '未激活',
        'expired' => '已过期',
        'pending' => '待审核',
    ],
    'payment_method_added' => '支付方式已添加。',
    'payment_method_add_failed' => '支付方式添加失败，请重试。',
    'services_linked' => '关联了 :count 个服务',
    'remove' => '移除',
    'remove_payment_method' => '移除支付方式',
    'remove_payment_method_confirm' => '您确定要移除 :name 吗？此操作不可撤销。',
    'expires' => '过期时间：:date',
    'cancel' => '取消',
    'confirm' => '确认移除',
    'email_notifications' => '邮件通知',
    'in_app_notifications' => '应用内通知',
    'notifications_description' => '管理您的通知偏好设置。您可以选择通过邮件、应用内（推送）或两种方式接收通知。',
    'notification' => '通知',

    'push_notifications' => '推送通知',
    'push_notifications_description' => '启用推送通知后，即使您未访问本站，也能在浏览器中实时接收更新提醒。',
    'enable_push_notifications' => '启用推送通知',
    'push_status' => [
        'not_supported' => '您的浏览器不支持推送通知。',
        'denied' => '推送通知已被阻止，请在浏览器设置中启用。',
        'subscribed' => '推送通知已启用。',
    ],
];
