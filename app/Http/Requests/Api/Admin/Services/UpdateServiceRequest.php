<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Plan;

class UpdateServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.update';

    public function rules(): array
    {
        return [
            'product_id' => 'sometimes|required|exists:products,id',
            'plan_id' => [
                'sometimes',
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
            'user_id' => 'sometimes|required|exists:users,id',
            /**
             * @default 1
             */
            'quantity' => 'sometimes|required|integer|min:1',
            /**
             * @default pending
             */
            'status' => 'sometimes|required|in:pending,active,cancelled,suspended',
            'expires_at' => 'sometimes|nullable|date|after_or_equal:today',
            /**
             * @example USD
             */
            'currency_code' => 'sometimes|required|string|exists:currencies,code',
            'price' => 'sometimes|required|numeric|min:0',
            'coupon_id' => 'sometimes|nullable|exists:coupons,id',
            'subscription_id' => 'sometimes|nullable|string|max:255',
            'order_id' => 'sometimes|nullable|exists:orders,id',
        ];
    }
}
