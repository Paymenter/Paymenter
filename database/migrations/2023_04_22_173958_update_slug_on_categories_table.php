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
        // Loop through all categories and make sure the slug is unique
        $categories = \App\Models\Category::all();
        $slugs = [];
        foreach($categories as $category) {
            $slug = $category->slug;
            if(in_array($slug, $slugs)) {
                $category->slug = $slug . '-' . $category->id;
                $category->save();
            }
            $slugs[] = $slug;
        }
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->change();
        });
    }
};
