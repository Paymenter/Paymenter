<?php

use App\Console\Commands\CronJob;
use Illuminate\Support\Facades\Artisan;

Artisan::command(CronJob::class)->daily();