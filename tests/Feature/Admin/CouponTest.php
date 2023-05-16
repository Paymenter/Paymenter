<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
    }


    /** 
     * Test if admin can view all the coupon pages
     */
    public function testIfAdminCanViewAllTheCouponPages()
    {
        $response = $this->actingAs($this->user)->get(route('admin.coupons'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get(route('admin.coupons.create'));
        $response->assertStatus(200);

        $coupon = Coupon::factory()->create([
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => 100,
            'time' => 'lifetime'
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.coupons.edit', $coupon));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get(route('admin.coupons'));
        $response->assertStatus(200);
    }

    /**
     * Test if admin can create a coupon
     *
     * @return void
     */
    public function testIfAdminCanCreateACoupon()
    {
        $response = $this->actingAs($this->user)->post(route('admin.coupons.store'), [
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => '100',
            'time' => 'lifetime'
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('coupons', [
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => '100',
            'time' => 'lifetime'
        ]);
    }

    /**
     * Test if admin can update a coupon
     *
     * @return void
     */
    public function testIfAdminCanUpdateACoupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => '100',
            'time' => 'lifetime'
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.coupons.update', $coupon), [
            'code' => 'TEST2',
            'type' => 'percent',
            'value' => '100',
            'time' => 'onetime',
            'max_uses' => 10,
            'start_date' => '2021-01-01',
            'end_date' => '2021-12-31',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('coupons', [
            'code' => 'TEST2',
            'type' => 'percent',
            'value' => '100',
            'time' => 'onetime',
            'max_uses' => 10,
            'start_date' => '2021-01-01',
            'end_date' => '2021-12-31',
        ]);
    }

    /**
     * Test if admin can delete a coupon
     *
     * @return void
     */
    public function testIfAdminCanDeleteACoupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => '100',
            'time' => 'lifetime'
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.coupons.delete', $coupon));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('coupons', [
            'code' => 'TEST',
            'type' => 'fixed',
            'value' => '100',
            'time' => 'lifetime'
        ]);
    }
}
