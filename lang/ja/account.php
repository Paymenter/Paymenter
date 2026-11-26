<?php

return [
    'account' => 'アカウント',
    'personal_details' => '個人情報',
    'security' => 'セキュリティ',
    'credits' => 'クレジット',

    'change_password' => 'パスワードを変更',

    'two_factor_authentication' => '二要素認証',
    'two_factor_authentication_description' => '二要素認証を有効にすると、アカウントのセキュリティをさらに強化できます。',
    'two_factor_authentication_enabled' => 'お使いのアカウントでは二要素認証が有効になっています。',
    'two_factor_authentication_enable' => '二要素認証を有効にする',
    'two_factor_authentication_disable' => '二要素認証を無効にする',
    'two_factor_authentication_disable_description' => '二要素認証を無効にしてもよろしいですか？アカウントの追加のセキュリティ保護が解除されます。',
    'two_factor_authentication_enable_description' => '二要素認証を有効にするには、Google Authenticator や Authy などの認証アプリで下のQRコードをスキャンする必要があります。',
    'two_factor_authentication_qr_code' => '認証アプリで下のQRコードをスキャンしてください:',
    'two_factor_authentication_secret' => 'または、次のコードを手動で入力してください:',

    'sessions' => 'セッション',
    'sessions_description' => '他のブラウザやデバイスでログイン中のセッションを管理し、ログアウトさせることができます。',
    'logout_sessions' => 'このセッションをログアウト',
    'current_device' => '現在のデバイス',

    'input' => [
        'current_password' => '現在のパスワード',
        'current_password_placeholder' => '現在のパスワードを入力',
        'new_password' => '新しいパスワード',
        'new_password_placeholder' => '新しいパスワードを入力',
        'confirm_password' => 'パスワードの確認',
        'confirm_password_placeholder' => '新しいパスワードを再入力',

        'two_factor_code' => '認証アプリに表示されたコードを入力してください',
        'two_factor_code_placeholder' => '二要素認証コード',

        'currency' => '通貨',
        'amount' => '金額',
        'payment_gateway' => '決済ゲートウェイ',
    ],

    'notifications' => [
        'password_changed' => 'パスワードを変更しました。',
        'password_incorrect' => '現在のパスワードが正しくありません。',
        'two_factor_enabled' => '二要素認証を有効にしました。',
        'two_factor_disabled' => '二要素認証を無効にしました。',
        'two_factor_code_incorrect' => 'コードが正しくありません。',
        'session_logged_out' => 'セッションをログアウトしました。',
        'unauthorized' => 'この操作は許可されていません。',
    ],

    'no_credit' => 'クレジットがありません。',
    'add_credit' => 'クレジットを追加',
    'credit_deposit' => 'クレジット入金（:currency）',

    'payment_methods' => '支払い方法',
    'recent_transactions' => '最近の取引',
    'saved_payment_methods' => '保存済みの支払い方法',
    'setup_payment_method' => '新しい支払い方法を設定',
    'no_saved_payment_methods' => '保存済みの支払い方法はありません。',
    'saved_payment_methods_description' => '保存済みの支払い方法を管理します。保存した支払い方法は、購入手続きの迅速化や自動支払いに利用できます。',
    'no_saved_payment_methods_description' => '支払い方法を追加すると、今後の支払いをより早く簡単に行えるほか、サービスの自動支払いを有効にできます。',
    'add_payment_method' => '支払い方法を追加',
    'payment_method_statuses' => [
        'active' => '有効',
        'inactive' => '無効',
        'expired' => '期限切れ',
        'pending' => '保留中',
    ],
    'payment_method_added' => '支払い方法を追加しました。',
    'payment_method_add_failed' => '支払い方法を追加できませんでした。もう一度お試しください。',
    'services_linked' => ':count件のサービスに紐付け済み',
    'remove' => '削除',
    'remove_payment_method' => '支払い方法を削除',
    'remove_payment_method_confirm' => '「:name」を削除してもよろしいですか？この操作は元に戻せません。',
    'expires' => '有効期限 :date',
    'cancel' => 'キャンセル',
    'confirm' => 'はい、削除します',
    'email_notifications' => 'メール通知',
    'in_app_notifications' => 'アプリ内通知',
    'notifications_description' => '通知設定を管理します。通知は、メール、アプリ内（プッシュ）、またはその両方で受け取ることができます。',
    'notification' => '通知',

    'push_notifications' => 'プッシュ通知',
    'push_notifications_description' => 'プッシュ通知を有効にすると、サイトを開いていないときでも、ブラウザで最新情報をリアルタイムに受け取れます。',
    'enable_push_notifications' => 'プッシュ通知を有効にする',
    'push_status' => [
        'not_supported' => 'お使いのブラウザはプッシュ通知に対応していません。',
        'denied' => 'プッシュ通知がブロックされています。ブラウザの設定で許可してください。',
        'subscribed' => 'プッシュ通知は有効です。',
    ],
];
