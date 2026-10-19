<?php

return [
    'services' => 'サービス',
    'product' => '商品',
    'price' => '価格',
    'status' => 'ステータス',
    'name' => '名前',
    'actions' => '操作',
    'view' => '表示',

    'product_details' => '商品の詳細',
    'billing_cycle' => '請求サイクル',
    'cancel' => '解約',
    'cancellation' => '「:service」の解約',
    'cancel_are_you_sure' => 'このサービスを解約してもよろしいですか？',
    'cancel_reason' => '解約理由',
    'cancel_type' => '解約のタイミング',
    'cancel_immediate' => '即時解約',
    'cancel_end_of_period' => '請求期間の終了時に解約',
    'cancel_immediate_warning' => '下のボタンを押すと、サービスは直ちに解約され、以後ご利用いただけなくなります。',
    'cancellation_requested' => '解約申請済み',
    'not_cancellable' => 'このサービスは解約できません',

    'current_plan' => '現在のプラン',
    'new_plan' => '新しいプラン',
    'change_plan' => 'プランを変更',
    'current_price' => '現在の価格',
    'new_price' => '新しい価格',
    'upgrade' => 'アップグレード',
    'upgrade_summary' => 'アップグレード内容',
    'total_today' => '本日のお支払い合計',
    'upgrade_service' => 'サービスをアップグレード',
    'upgrade_choose_product' => 'アップグレード先の商品を選択してください',
    'upgrade_choose_config' => 'アップグレード後の構成を選択してください',
    'next_step' => '次へ',

    'upgrade_pending' => '未払いのアップグレード/ダウングレード請求書があるため、アップグレードできません',

    'outstanding_invoice' => '未払いの請求書があります。',
    'view_and_pay' => 'こちらをクリックして確認・支払い',

    'statuses' => [
        'pending' => '保留中',
        'active' => '有効',
        'cancelled' => '解約済み',
        'suspended' => '一時停止中',
        'cancellation_pending' => '解約待ち',
    ],
    'billing_cycles' => [
        'day' => '日|日',
        'week' => '週|週',
        'month' => 'ヶ月|ヶ月',
        'year' => '年|年',
    ],
    'every_period' => '毎:period:unit',
    'price_every_period' => ':price（毎:period:unit）',
    'price_one_time' => ':price（一回払い）',
    'renews_in' => '更新まで',
    'renews_on' => '更新日',
    'auto_pay' => '自動支払い方法',
    'auto_pay_not_configured' => '未設定',

    'no_services' => 'サービスが見つかりません',
    'update_billing_agreement' => '自動支払い方法を変更',
    'clear_billing_agreement' => '自動支払い方法を解除',
    'select_billing_agreement' => '自動支払い方法を選択',

    'remove_payment_method' => '支払い方法を削除',
    'remove_payment_method_confirm' => 'このサービスから支払い方法「:name」を削除してもよろしいですか？削除すると、このサービスの請求書は自動で支払われなくなります。',

    'label' => 'ラベル',
    'label_placeholder' => 'このサービスのカスタムラベルを入力',
    'label_modal_title' => 'サービスのラベルを編集',
    'update_label' => 'ラベルを更新',

];
