<?php

namespace App\Enums;

enum AdjustmentNoteType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
}
