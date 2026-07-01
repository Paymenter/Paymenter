<?php

use App\Models\Extension;
use App\Models\Product;
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
        Schema::create('location_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('group_type')->default('custom');
            $table->json('service_types')->nullable();
            $table->string('status')->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('location_groups', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('location_groups')
                ->nullOnDelete();
        });

        Schema::create('location_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_group_id')->nullable()->constrained('location_groups')->nullOnDelete();
            $table->unsignedInteger('legacy_id')->nullable()->index();
            $table->string('code')->unique();
            $table->string('display_name');
            $table->string('option_type')->default('geo');
            $table->string('place_country_iso2', 2)->nullable()->index();
            $table->string('place_subdivision_code')->nullable()->index();
            $table->string('network_type')->nullable();
            $table->string('isp_name')->nullable();
            $table->string('selection_policy')->default('fixed');
            $table->json('service_types')->nullable();
            $table->string('status')->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('provider_location_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Extension::class, 'provider_id')->constrained('extensions')->cascadeOnDelete();
            $table->foreignId('location_option_id')->constrained('location_options')->cascadeOnDelete();
            $table->string('service_type');
            $table->boolean('enabled')->default(true);
            $table->string('stock_state')->default('unknown');
            $table->json('capabilities')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'location_option_id', 'service_type'], 'provider_location_offerings_unique');
        });

        Schema::create('provider_location_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_location_offering_id')->constrained('provider_location_offerings')->cascadeOnDelete();
            $table->string('external_location_id')->nullable()->index();
            $table->string('external_location_code')->nullable()->index();
            $table->string('external_name')->nullable();
            $table->integer('priority')->default(0);
            $table->integer('weight')->default(100);
            $table->json('raw_payload')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('product_location_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignId('provider_location_offering_id')->constrained('provider_location_offerings')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->decimal('price_delta', 17, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'provider_location_offering_id'], 'product_location_offerings_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_location_offerings');
        Schema::dropIfExists('provider_location_targets');
        Schema::dropIfExists('provider_location_offerings');
        Schema::dropIfExists('location_options');
        Schema::dropIfExists('location_groups');
    }
};
