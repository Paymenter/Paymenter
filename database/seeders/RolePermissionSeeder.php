<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use App\Utils\Permissions;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $availablePermissions = Permissions::$flags;
        $permissions = [];
        foreach ($availablePermissions as $permission => $value) {
            $permissions[] = $permission;
        }
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'permissions' => Permissions::create($permissions), 'id' => 1]);
        } else {
            $admin = Role::where('name', 'admin')->first();
            $admin->permissions = Permissions::create($permissions);
            $admin->save();
        }
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user', 'permissions' => 0, 'id' => 2]);
        } else {
            $user = Role::where('name', 'user')->first();
            $user->permissions = 0;
            $user->save();
        }
        User::all()->each(function ($user) {
            if (!$user->role_id) {
                $user->role_id = 2;
            }
            $user->save();
        });
    }
}
