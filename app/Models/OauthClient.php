<?php

namespace App\Models;

use Laravel\Passport\Client as PassportClient;
use OwenIt\Auditing\Contracts\Auditable;

class OauthClient extends PassportClient implements Auditable
{
    use \App\Models\Traits\Auditable;
}
