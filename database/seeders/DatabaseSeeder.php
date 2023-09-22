<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(AnnouncementSeeder::class);
        $this->call(EmailTemplateSeeder::class);
    }
}
