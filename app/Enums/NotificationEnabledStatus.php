<?php

namespace App\Enums;

enum NotificationEnabledStatus: string
{
    case Force = 'force';
    case ChoiceOn = 'choice_on';
    case ChoiceOff = 'choice_off';
    case Never = 'never';
}
