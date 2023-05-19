<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Announcement::count() > 0) {
            return;
        }
        Announcement::create([
            'title' => 'Welcome to your new panel!',
            'announcement' => 'Welcome to your new panel! Manage the announcement in the admin area'
        ]);
    }
}
