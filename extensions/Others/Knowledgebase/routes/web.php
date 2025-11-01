<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Category;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Index;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Show;

Route::group(['middleware' => ['web']], function () {
    Route::get('/knowledgebase', Index::class)->name('knowledgebase.index');
    Route::get('/knowledgebase/category/{category:slug}', Category::class)->name('knowledgebase.category');
    Route::get('/knowledgebase/article/{article:slug}', Show::class)->name('knowledgebase.show');
});
