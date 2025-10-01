<?php

namespace App\Enums;

enum InvoiceTransactionStatus: string
{
    case PROCESSING = 'processing';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}