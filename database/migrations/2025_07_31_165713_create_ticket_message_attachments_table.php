<?php

use App\Models\TicketMessage;
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
        Schema::create('ticket_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('filename');
            $table->string('path');
            $table->unsignedBigInteger('filesize');
            $table->string('mime_type');
            $table->foreignIdFor(TicketMessage::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_message_attachments');
    }
};
