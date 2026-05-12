<?php

namespace App\Enums;

enum CreditNoteType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
}
