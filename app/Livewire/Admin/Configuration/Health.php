<?php

namespace App\Livewire\Admin\Configuration;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Health extends Component
{
    public $warnings = [];
    public $errors = [];

    public function mount()
    {
        $this->checkEnv();
    }


    private function checkEnv()
    {
        // Check if cache is set to Redis
        if (config('cache.default') != 'redis') {
            $this->warnings[] = 'Cache is not set to Redis<br>Redis provides better performance and scalability';
        } else {
            // Check if redis works
            try {
                \Illuminate\Support\Facades\Redis::set('health', 'ok');
            } catch (\Exception $e) {
                $this->errors[] = 'Redis is not working<br>Redis provides better performance and scalability';
            }
        }

        // Check APP_DEBUG
        if (config('app.debug') == true) {
            $this->errors[] = 'APP_DEBUG is set to true<br>Set APP_DEBUG to false in production';
        }

        // Check if queue worker is working (ALWAYS database)
        if (DB::table('jobs')->count() != 0) {
            $this->errors[] = 'Queue worker is not working<br>Queue worker is required for background tasks';
        }
    }

    public function render()
    {
        return view('admin.configuration.health')->layoutData(['title' => __('System Health')]);
    }
}
