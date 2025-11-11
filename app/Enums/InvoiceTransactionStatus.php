<?php

namespace App\Enums;

enum InvoiceTransactionStatus: string
{
    case Processing = 'processing';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
}
