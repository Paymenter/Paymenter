<?php

return [
    'services' => '服务',
    'product' => '产品',
    'price' => '价格',
    'status' => '状态',
    'name' => '名称',
    'actions' => '操作',
    'view' => '查看',

    'product_details' => '产品详情',
    'billing_cycle' => '计费周期',
    'cancel' => '取消',
    'cancellation' => ':service 的取消',
    'cancel_are_you_sure' => '您确定要取消此服务吗？',
    'cancel_reason' => '取消原因',
    'cancel_type' => '取消类型',
    'cancel_immediate' => '立即取消',
    'cancel_end_of_period' => '在计费周期结束时取消',
    'cancel_immediate_warning' => '当您点击下方的按钮后，服务将立即取消，您将无法再使用。',
    'cancellation_requested' => '已提交取消申请',

    'current_plan' => '当前方案',
    'new_plan' => '新方案',
    'change_plan' => '变更方案',
    'current_price' => '当前价格',
    'new_price' => '新价格',
    'upgrade' => '升级',
    'upgrade_summary' => '升级摘要',
    'total_today' => '今日总计',
    'upgrade_service' => '升级服务',
    'upgrade_choose_product' => '选择要升级的产品',
    'upgrade_choose_config' => '选择要升级的配置',
    'next_step' => '下一步',

    'upgrade_pending' => '若已有升级/降级相关账单，则无法执行此操作',

    'outstanding_invoice' => '您有一项未支付的账单。',
    'view_and_pay' => '点击查看并支付',

    'statuses' => [
        'pending' => '待处理',
        'active' => '已启用',
        'cancelled' => '已取消',
        'suspended' => '已暂停',
        'cancellation_pending' => '取消申请处理中',
    ],
    'billing_cycles' => [
        'day' => '天',
        'week' => '周',
        'month' => '月',
        'year' => '年',
    ],
    'every_period' => '每 :period :unit',
    'price_every_period' => '每 :period :unit :price 元',
    'price_one_time' => ':price 元（一次性）',
    'expires_at' => '有效期至',
    'auto_pay' => '自动支付方式',
    'auto_pay_not_configured' => '未配置',

    'no_services' => '暂无服务记录',
    'update_billing_agreement' => '更新支付协议',
    'clear_billing_agreement' => '清空支付协议',
    'select_billing_agreement' => '选择支付协议',

    'remove_payment_method' => '移除支付方式',
    'remove_payment_method_confirm' => '您确定要从此服务中移除支付方式「:name」吗？移除后，该服务的账单将无法自动支付。',
];
