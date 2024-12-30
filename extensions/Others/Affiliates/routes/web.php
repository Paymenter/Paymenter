<?php

use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates\Affiliate;

Route::group(['middleware' => ['web']], function () {
    Route::get('/account/affiliate', Affiliate::class)->name('affiliate.index');
});
