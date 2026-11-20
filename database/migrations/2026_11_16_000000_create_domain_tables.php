<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('domain_mode')->default('none');
            $table->json('domain_products')->nullable();
        });

        Schema::create('tlds', function (Blueprint $table) {
            $table->id();
            $table->string('tld', 63);
            $table->foreignId('server_id')->nullable()->constrained('extensions')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['tld', 'server_id']);
        });

        Schema::create('tld_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tld_id')->constrained()->cascadeOnDelete();
            $table->char('currency_code', 3);
            $table->unsignedSmallInteger('years')->default(1);
            $table->decimal('cost', 17, 2)->nullable();
            $table->decimal('register', 17, 2)->nullable();
            $table->decimal('renew', 17, 2)->nullable();
            $table->decimal('transfer', 17, 2)->nullable();

            $table->unique(['tld_id', 'currency_code', 'years']);
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('server_id')->nullable()->constrained('extensions')->nullOnDelete();
            $table->foreignId('tld_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 255)->unique();
            $table->string('tld', 63)->nullable();
            $table->string('registrar')->nullable();
            $table->string('registrar_ref')->nullable();
            $table->string('status')->default('pending');
            $table->date('registered_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->date('grace_until')->nullable();
            $table->date('redemption_until')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->boolean('locked')->default(false);
            $table->boolean('privacy')->nullable();
            $table->json('nameservers')->nullable();
            $table->text('contacts')->nullable();
            $table->text('epp_code')->nullable();
            $table->string('transfer_status')->nullable();
            $table->text('sync_error')->nullable();
            $table->string('last_transition_reason')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index('last_synced_at');
        });

        Schema::create('domain_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('stage')->nullable();
            $table->string('cycle', 10)->default('');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['domain_id', 'key', 'stage', 'cycle']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('cart_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::dropIfExists('domain_notices');
        Schema::dropIfExists('domains');
        Schema::dropIfExists('tld_prices');
        Schema::dropIfExists('tlds');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['domain_mode', 'domain_products']);
        });
    }
};
