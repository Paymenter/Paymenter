<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'メールアドレスまたはパスワードが正しくありません。',
    'password' => '入力されたパスワードが正しくありません。',
    'throttle' => 'ログイン試行回数が上限を超えました。:seconds秒後にもう一度お試しください。',

    'sign_in' => 'ログイン',
    'sign_in_title' => 'アカウントにログイン',
    'or_sign_in_with' => 'または次の方法でログイン',
    'forgot_password' => 'パスワードをお忘れですか？',
    'remember_me' => 'Remember me',
    'dont_have_account' => 'アカウントをお持ちでないですか？',
    'already_have_account' => 'すでにアカウントをお持ちですか？',

    'sign_up' => '新規登録',
    'sign_up_title' => 'アカウントを作成',

    'logout' => 'ログアウト',

    'input' => [
        'email' => 'メールアドレス',
        'email_label' => 'メールアドレス',
        'password' => 'パスワード',
    ],

    'oauth' => [
        'unverified_discord_account' => 'お使いのDiscordアカウントは認証されていません。',
        'unverified_google_account' => 'Your Google account is not verified.',
        'account_not_registered' => 'このアカウントはこのサイトに登録されていません。',
        'authorization_request' => 'Authorization Request',
        'requesting_permission' => 'is requesting permission to access your account.',
        'scopes_title' => 'This application will be able to:',
        'authorize' => 'Authorize',
    ],

    'reset_password' => 'パスワードをリセット',

    'verify_2fa' => '二要素認証コードの確認',
    'verify' => '確認',
    'sign_in_with_passkey' => 'Sign in with a passkey',
    'passkey_signing_in' => 'Waiting for your passkey…',
    'confirm_password_title' => 'Confirm your password',
    'confirm_password_description' => 'For your security, confirm your password before managing passkeys.',
    'confirm_password' => 'Confirm password',

    'verification' => [
        'notice' => 'メールアドレスを確認してください',
        'sent' => '新しい確認用リンクをメールアドレス宛てに送信しました。',
        'check_your_email' => '続行する前に、確認用リンクが記載されたメールをご確認ください。',
        'not_received' => 'メールが届いていない場合は、確認メールを再送信できます。',
        'request_another' => '確認メールを再送信',
    ],
];
