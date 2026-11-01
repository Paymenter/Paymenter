<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceDueDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_period_from_the_31st_ends_at_the_end_of_a_shorter_month(): void
    {
        $product = $this->createProduct();
        $service = Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => '2026-10-31 12:00:00',
            'currency_code' => 'USD',
        ]);

        $this->assertSame('Test Product (Oct 31, 2026 - Nov 30, 2026)', $service->description);
        $this->assertSame('2026-11-30', $service->calculateNextDueDate()->toDateString());
    }
}
