<?php

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
        Schema::create('service_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Service::class)->constrained()->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->enum('type', ['immediate', 'end_of_period'])->default('immediate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cancellations');
    }
};
