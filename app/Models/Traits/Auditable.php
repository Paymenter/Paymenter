<?php
namespace App\Models\Traits;

use OwenIt\Auditing\Auditable as AuditableTrait;
use Request;
use Str;

trait Auditable
{
    use AuditableTrait;

    public function generateTags(): array
    {
        // Check current url contains admin
        if (Str::contains(Request::livewireUrl(), '/admin')) {
            return [
                'admin',
            ];
        }

        return [];
    }
}