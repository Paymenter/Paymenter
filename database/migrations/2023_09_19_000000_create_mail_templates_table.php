<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailable');
            $table->text('subject')->nullable();
            $table->string('preview_text')->nullable();
            $table->longtext('html_template');
            $table->longtext('text_template')->nullable();
            $table->timestamps();
        });
    }
};