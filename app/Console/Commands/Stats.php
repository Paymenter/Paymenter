<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Stats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Stats to Paymenter API';

    protected $url = 'https://api.paymenter.org/stats';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('settings::stats.disabled')) {
            return;
        }
        $this->info('Posting Stats to Paymenter API');
        $token = config('settings::stats.token');
        if (! $token) {
            $token = Str::uuid();
            Setting::updateOrCreate(['key' => 'stats.token'], ['value' => $token]);
        }
        $extensions = [];
        foreach (Extension::where('enabled', 1)->get() as $extension) {
            $extensions[] = [
                'name' => $extension->name,
                'count' => Product::where('extension_id', $extension->id)->count(),
            ];
        }
        $userCoount = 0;
        foreach (Role::all() as $role) {
            $role = Role::find($role->id);
            if ($role->id !== 2) {
                $userCoount += $role->users()->count();
            }
        }
        $data = [
            'token' => $token,
            'stats' => [
                'orders' => [
                    'count' => Order::count(),
                ],
                'invoices' => [
                    'count' => Invoice::count(),
                    'pending' => Invoice::where('status', 'pending')->count(),
                    'paid' => Invoice::where('status', 'paid')->count(),
                ],
                'tickets' => [
                    'count' => Ticket::count(),
                ],
                'products' => [
                    'count' => Product::count(),
                ],
                'coupons' => [
                    'count' => Coupon::count(),
                ],
                'categories' => [
                    'count' => Category::count(),
                ],
                'users' => [
                    'count' => User::count(),
                    'admins' => $userCoount,
                ],
                'extensions' => [
                    'servers' => Extension::where('type', 'server')->count(),
                    'gateway' => Extension::where('type', 'gateway')->count(),
                    'list' => $extensions,
                ],
                'php_version' => phpversion(),
                'paymenter_version' => config('app.version'),
            ],
        ];
        $response = Http::post($this->url, $data);
        if ($response->successful()) {
            $this->info('Stats Posted Successfully');
        } else {
            $this->error($response->body());
            $this->error('Error Posting Stats');
        }
    }
}
