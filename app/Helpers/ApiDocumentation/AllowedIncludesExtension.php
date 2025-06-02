<?php

namespace App\Helpers\ApiDocumentation;

use App\Http\Controllers\Api\ApiController;
use Dedoc\Scramble\Infer\Extensions\Event\MethodCallEvent;
use Dedoc\Scramble\Infer\Extensions\MethodReturnTypeExtension;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Type;

class AllowedIncludesExtension implements MethodReturnTypeExtension
{
    public function shouldHandle(ObjectType $type): bool
    {
        return $type->isInstanceOf(ApiController::class);
    }

    public function getMethodReturnType(MethodCallEvent $event): ?Type
    {
        if ($event->name === 'allowedIncludes') {
            return $event->getArg('includes', 0);
        }

        return null;
    }
}
