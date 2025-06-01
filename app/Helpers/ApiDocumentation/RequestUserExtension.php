<?php

namespace App\Helpers\ApiDocumentation;

use App\Models\User;
use Dedoc\Scramble\Infer\Extensions\Event\MethodCallEvent;
use Dedoc\Scramble\Infer\Extensions\MethodReturnTypeExtension;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Type;
use Illuminate\Http\Request;

class RequestUserExtension implements MethodReturnTypeExtension
{
    public function shouldHandle(ObjectType $type): bool
    {
        return $type->isInstanceOf(Request::class);
    }

    public function getMethodReturnType(MethodCallEvent $event): ?Type
    {
        if ($event->name === 'user') {
            return new ObjectType(User::class);
        }

        return null;
    }
}
