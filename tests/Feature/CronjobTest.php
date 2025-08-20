<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CronjobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_invoices_are_created_if_due_date_is_reached(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();

        // Set config cronjob_invoice
        // This is the number of days before the due date to send an invoice
        config(['settings.cronjob_invoice' => 7]);

        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => now()->addDays(2)->addHour(-1), // Set expires_at to 6 days from now
            'currency_code' => 'USD',
            'price' => 10.00, // Set a price for the service
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if an invoice was created
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'pending',
            'due_at' => $service->expires_at,
            'currency_code' => 'USD',
        ]);
    }

    public function test_invoices_are_paid_with_credits_if_available(): void
    {
        $user = \App\Models\User::factory()->create();

        $user->credits()->create([
            'currency_code' => 'USD',
            'amount' => 10.00,
        ]);

        // Set config cronjob_invoice
        // This is the number of days before the due date to send an invoice
        config(['settings.cronjob_invoice' => 7]);

        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => now()->addDays(2)->addHour(-1), // Set expires_at to 6 days from now
            'currency_code' => 'USD',
            'price' => 10.00, // Set a price for the service
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if an invoice was created
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'paid',
            'due_at' => $service->expires_at,
            'currency_code' => 'USD',
        ]);
    }

    public function test_services_are_cancelled_if_not_paid_within_configured_days(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();

        // Set config cronjob_order_cancel
        config(['settings.cronjob_order_cancel' => 7]);

        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'pending',
            'currency_code' => 'USD',
            'price' => 10.00,
            'created_at' => now()->subDays(8), // Set created_at to 8 days ago
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if the service was cancelled
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_services_are_suspended_if_due_date_has_passed(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();

        $product = $this->createProduct();

        // Making sure the cronjob_order_suspend is set to 2 days
        config(['settings.cronjob_order_suspend' => 2]);

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => now()->subDays(3), // Set expires_at to 1 day ago
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);

        Queue::fake();

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if an invoice was created
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'pending',
            'due_at' => $service->expires_at,
            'currency_code' => 'USD',
        ]);

        Queue::assertPushed(\App\Jobs\Server\SuspendJob::class, function ($job) use ($service) {
            return $job->service->id === $service->id;
        });

        // Check if the service was suspended
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'suspended',
        ]);
    }

    public function test_orders_are_terminated_if_due_date_is_overdue(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();

        // Set config cronjob_order_terminate
        config(['settings.cronjob_order_terminate' => 14]);

        $product = $this->createProduct();

        // Create a subscription for the user
        $service = \App\Models\Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'expires_at' => now(), // Set expires_at to 15 days ago
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Now it should have generated an invoice
        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'pending',
            'due_at' => $service->expires_at,
            'currency_code' => 'USD',
        ]);

        // Update due date to be overdue
        $service->expires_at = now()->subDays(15);
        $service->save();

        Queue::fake();

        // Run the cron job again
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if the service was terminated
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => \App\Models\Service::STATUS_CANCELLED,
        ]);

        Queue::assertPushed(\App\Jobs\Server\TerminateJob::class, function ($job) use ($service) {
            return $job->service->id === $service->id;
        });

        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'status' => 'cancelled',
            'currency_code' => 'USD',
        ]);
    }

    public function test_tickets_are_closed_if_no_response_for_x_days(): void
    {
        // Create a user
        $user = \App\Models\User::factory()->create();

        // Set config cronjob_ticket_close
        config(['settings.cronjob_close_ticket' => 7]);

        // Create a ticket for the user
        $ticket = \App\Models\Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $differentUser = \App\Models\User::factory()->create();

        // Add message
        \App\Models\TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $differentUser->id,
            'message' => 'This is a test message.',
            'created_at' => now()->subDays(8), // Set created_at to 8 days ago
        ]);

        // Run the cron job
        $this->artisan('app:cron-job')
            ->assertExitCode(0);

        // Check if the ticket was closed
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
        ]);
    }
}
