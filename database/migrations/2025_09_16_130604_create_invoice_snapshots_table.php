<?php

use App\Classes\Settings;
use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->json('properties')->nullable();
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('tax_country')->nullable();
            $table->text('bill_to')->nullable();
            $table->foreignIdFor(Invoice::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Invoice::where('status', 'paid')->whereDoesntHave('snapshot')->chunk(100, function ($invoices) {
            foreach ($invoices as $invoice) {

                $snapshotData = [
                    'name' => $invoice->user->name,
                    'properties' => $invoice->user_properties,
                    'bill_to' => config('settings.bill_to_text', config('settings.company_name')),
                ];

                if ($tax = Settings::tax($invoice->user)) {
                    $snapshotData['tax_name'] = $tax->name;
                    $snapshotData['tax_rate'] = $tax->rate;
                    $snapshotData['tax_country'] = $tax->country;
                }
                $invoice->snapshot()->create($snapshotData);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_snapshots');
    }
};
