<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\App;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Request;
use Str;

trait Auditable
{
    use AuditableTrait;

    public function generateTags(): array
    {
        if (App::runningInConsole() || !Str::contains(Request::livewireUrl(), '/admin')) {
            return [];
        }

        // Check current url contains admin
        return [
            'admin',
        ];
    }
}
