<?php

namespace App\Enums;

enum CancellationReason: string
{
    case Fraud = 'fraud';
    case OrderCancellation = 'order_cancellation';
}
