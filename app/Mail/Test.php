<?php

namespace App\Mail;

use App\Mail\Mailable;
use App\Models\User;

class Test extends Mailable
{
    /** @var string */
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}