<?php

namespace Paymenter\Extensions\Servers\FakeServer;

use App\Classes\Extension\Server;

class FakeServer extends Server
{
    public static int $constructed = 0;

    public function __construct($config = [])
    {
        parent::__construct($config);
        self::$constructed++;
    }
}
