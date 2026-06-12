<?php

namespace App\Enums;

enum AdjustmentNoteStatus: string
{
    case Active = 'active';
    case Voided = 'voided';
}
