<?php

namespace App\Mail;

use App\Mail\Mailable;
use App\Models\User;

class Test extends Mailable
{
    /** @var string */
    public $name;
    public function __construct(User $user)
    {
        $this->name = $user->name;
    }
}