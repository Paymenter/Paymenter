<?php

use App\Console\Commands\CronJob;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CronJob::class)->daily();