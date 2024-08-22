<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->insertOrIgnore([
            [
                'key' => 'new_login_detected',
                'subject' => 'New login detected',
                'body' => <<<'HTML'
                    # New login detected  
                                
                    A new login was detected on your account.
                                
                    Location: {{ $location }}  
                    IP: {{ $ip }}  
                                
                                
                    **If this was you**  
                    You can ignore this message, there is no need to take any action.
                                
                    **If this wasn't you**  
                    Please reset your password [here]({{ route('register') }}).
                HTML,
            ],
        ]);
    }
}
