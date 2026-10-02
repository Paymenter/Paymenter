<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

abstract class ApiController extends Controller
{
    const MAPPED_INCLUDES = [
        'assigned_to' => 'users',
        'category' => 'categories',
        'children' => 'categories',
        'coupon' => 'coupons',
        'invoice' => 'invoices',
        'items' => 'invoice_items',
        'messages' => 'ticket_messages',
        'attachments' => 'ticket_messages',
        'order' => 'orders',
        'parent' => 'categories',
        'product' => 'products',
        'products.plans.prices' => 'products',
        'role' => 'roles',
        'user' => 'users',
        'ticket' => 'tickets',
        'plans.prices' => 'products',
    ];

    protected function allowedIncludes($includes = []): array
    {
        // Check if user has permission to include the specified relationships
        $allowedIncludes = [];

        foreach ($includes as $include) {
            // Check if the include is mapped to a specific relation
            $relation = self::MAPPED_INCLUDES[$include] ?? $include;
            if (in_array('admin.' . $relation . '.view', request()->attributes->get('api_key_permissions', []))) {
                $allowedIncludes[] = $include;
            }
        }

        return $allowedIncludes;
    }

    protected function loadAllowedIncludes(Model $model, array $includes): Model
    {
        return QueryBuilder::for($model::class)
            ->allowedIncludes($this->allowedIncludes($includes))
            ->findOrFail($model->getKey());
    }

    /**
     * Return an HTTP/204 response for the API.
     */
    protected function returnNoContent(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
