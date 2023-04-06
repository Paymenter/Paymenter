<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('announcement');
            $table->boolean('published')->default(true);
            $table->timestamps();
        });
        // Add first announcement
        $announcement = new \App\Models\Announcement();
        $announcement->title = 'Welcome to your new panel!';
        $announcement->announcement = 'Welcome to your new panel! You can edit this announcement in the admin panel.';
        $announcement->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};
