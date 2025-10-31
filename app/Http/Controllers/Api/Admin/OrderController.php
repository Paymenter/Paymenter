<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Orders\CreateOrderRequest;
use App\Http\Requests\Api\Admin\Orders\DeleteOrderRequest;
use App\Http\Requests\Api\Admin\Orders\GetOrderRequest;
use App\Http\Requests\Api\Admin\Orders\GetOrdersRequest;
use App\Http\Requests\Api\Admin\Orders\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Orders', weight: 2)]
class OrderController extends ApiController
{
    protected const INCLUDES = [
        'services',
        'user',
    ];

    /**
     * List Orders
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetOrdersRequest $request)
    {
        // Fetch orders with pagination
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters(['id', 'currency_code'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'currency_code'])
            ->simplePaginate(request('per_page', 15));

        // Return the orders as a JSON response
        return OrderResource::collection($orders);
    }

    /**
     * Create a new order
     */
    public function store(CreateOrderRequest $request)
    {
        // Validate and create the order
        $order = Order::create($request->validated());

        // Return the created order as a JSON response
        return new OrderResource($order);
    }

    /**
     * Show a specific order
     */
    public function show(GetOrderRequest $request, Order $order)
    {
        $order = QueryBuilder::for(Order::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($order->id);

        // Return the order as a JSON response
        return new OrderResource($order);
    }

    /**
     * Update a specific order
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        // Validate and update the order
        $order->update($request->validated());

        // Return the updated order as a JSON response
        return new OrderResource($order);
    }

    /**
     * Delete a specific order
     */
    public function destroy(DeleteOrderRequest $request, Order $order)
    {
        // Delete the order
        $order->delete();

        return $this->returnNoContent();
    }
}
