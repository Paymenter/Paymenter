<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Stats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Stats to Paymenter API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(config('settigns::stats.disabled')) {
            return;
        }
        $this->info('Posting Stats to Paymenter API');
        $url = 'https://api.paymenter.org/stats';
        $token = config('settings::stats.token');
        if(!$token) {
            $token = Str::uuid();
            Setting::updateOrCreate(['key' => 'stats.token'], ['value' => $token]);
        }
        $extensions = [];
        foreach(\App\Models\Extension::where('enabled', 1)->get() as $extension) {
            $extensions[] = [
                'name' => $extension->name,
                'count' => \App\Models\Product::where('server_id', $extension->id)->count(),
            ];
        }
        $data = [
            'token' => $token,
            'stats' => [
                'orders' => [
                    'count' => \App\Models\Order::count(),
                ],
                'invoices' => [
                    'count' => \App\Models\Invoice::count(),
                    'pending' => \App\Models\Invoice::where('status', 'pending')->count(),
                    'paid' => \App\Models\Invoice::where('status', 'paid')->count(),
                ],
                'tickets' => [
                    'count' => \App\Models\Ticket::count(),
                ],
                'products' => [
                    'count' => \App\Models\Product::count(),
                ],
                'coupons' => [
                    'count' => \App\Models\Coupon::count(),
                ],
                'categories' =>[
                    'count' => \App\Models\Category::count(),
                ],
                'users' => [
                    'count' => \App\Models\User::count(),
                    'admins' => \App\Models\User::where('is_admin', '1')->count(),
                ],
                'extensions' => [
                    'servers' => \App\Models\Extension::where('type', 'server')->count(),
                    'gateway' => \App\Models\Extension::where('type', 'gateway')->count(),
                    'list' => $extensions,
                ],
                'php_version' => phpversion(),
                'paymenter_version' => config('app.version'),
            ]
        ];
        $response = Http::post($url, $data);
        if($response->successful()) {
            $this->info('Stats Posted Successfully');
        } else {
            $this->error($response->body());
            $this->error('Error Posting Stats');
        }
    }
}