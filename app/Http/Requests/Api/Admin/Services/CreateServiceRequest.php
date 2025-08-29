<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Plan;
use App\Models\Product;

class CreateServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.create';

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'plan_id' => [
                'required',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    $productId = $this->input('product_id');
                    if ($productId && !Plan::where('id', $value)->where('priceable_type', Product::class)->where('priceable_id', $productId)->exists()) {
                        // Check if the plan belongs to the specified product
                        $fail('The selected plan does not belong to the specified product.');
                    }
                },
            ],
            'user_id' => 'required|exists:users,id',
            /**
             * @default 1
             */
            'quantity' => 'required|integer|min:1',
            /**
             * @default pending
             */
            'status' => 'required|in:pending,active,cancelled,suspended',
            'expires_at' => 'nullable|date|after_or_equal:today',
            /**
             * @example USD
             */
            'currency_code' => 'required|string|exists:currencies,code',
            'price' => 'required|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'subscription_id' => 'nullable|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'quantity' => 1, // Default quantity to 1 if not provided
            'status' => 'pending', // Default status to 'pending' if not provided
        ]);
    }
}
