<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

abstract class ApiController extends Controller
{
    protected function allowedIncludes($includes = []): array
    {
        // Check if user has permission to include the specified relationships
        $allowedIncludes = [];

        foreach ($includes as $include) {
            if (Auth::user()->tokenCan("admin.{$include}.view") && Auth::user()->hasPermission("admin.{$include}.view")) {
                $allowedIncludes[] = $include;
            }
        }

        return $allowedIncludes;
    }
}
