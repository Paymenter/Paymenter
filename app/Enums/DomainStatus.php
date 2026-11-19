<?php

namespace App\Enums;

enum DomainStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Grace = 'grace';
    case Redemption = 'redemption';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case TransferInPending = 'transfer_in_pending';
    case TransferOutPending = 'transfer_out_pending';
}
