<?php

return [
    'services' => 'Servicios',
    'product' => 'Producto',
    'price' => 'Precio',
    'status' => 'Estado',
    'name' => 'Nombre',
    'actions' => 'Acciones',
    'view' => 'Ver',

    'product_details' => 'Detalles del producto',
    'billing_cycle' => 'Ciclo de facturación',
    'cancel' => 'Cancelar',
    'cancellation' => 'Cancelación de :service',
    'cancel_are_you_sure' => '¿Estás seguro de que quieres cancelar este servicio?',
    'cancel_reason' => 'Razón de la cancelación',
    'cancel_type' => 'Tipo de cancelación',
    'cancel_immediate' => 'Cancelar de inmediato',
    'cancel_end_of_period' => 'Cancelar al final del período de facturación',
    'cancel_immediate_warning' => 'Al pulsar el botón de abajo, el servicio se cancelará inmediatamente y ya no podrás utilizarlo.',
    'cancellation_requested' => 'Cancelación solicitada',

    'current_plan' => 'Plan actual',
    'new_plan' => 'Nuevo plan',
    'change_plan' => 'Cambiar plan',
    'current_price' => 'Precio actual',
    'new_price' => 'Nuevo precio',
    'upgrade' => 'Mejorar',
    'upgrade_summary' => 'Resumen de la actualización',
    'total_today' => 'Total hoy',
    'upgrade_service' => 'Actualizar el servicio',
    'upgrade_choose_product' => 'Elige el producto al que mejorar',
    'upgrade_choose_config' => 'Elige la configuración para la mejora',
    'next_step' => 'Siguiente paso',

    'upgrade_pending' => 'No es posible realizar una actualización si ya existe una factura de mejora/disminución abierta',

    'outstanding_invoice' => 'Tienes una factura pendiente.',
    'view_and_pay' => 'Haz clic aquí para ver y pagar',

    'statuses' => [
        'pending' => 'Pendiente',
        'active' => 'Activo',
        'cancelled' => 'Cancelado',
        'suspended' => 'Suspendido',
        'cancellation_pending' => 'Cancelación pendiente',
    ],
    'billing_cycles' => [
        'day' => 'día|días',
        'week' => 'semana|semanas',
        'month' => 'mes|meses',
        'year' => 'año|años',
    ],
    'every_period' => 'Cada :period :unit',
    'price_every_period' => ':price por :period :unit',
    'price_one_time' => ':price una sola vez',
    'expires_at' => 'Vence el',
    'auto_pay' => 'Auto paying using',
    'auto_pay_not_configured' => 'Not configured',

    'no_services' => 'No services found',
    'update_billing_agreement' => 'Update Billing Agreement',
    'clear_billing_agreement' => 'Clear Billing Agreement',
    'select_billing_agreement' => 'Select Billing Agreement',

    'remove_payment_method' => 'Remove Payment Method',
    'remove_payment_method_confirm' => 'Are you sure you want to remove the payment method ":name" from this service? Your service will no longer be able to auto pay its invoices.',
];
