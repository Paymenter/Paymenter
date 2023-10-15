<?php

namespace App\Console\Commands\User;

use App\Models\Role;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:user:create {--1=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user on the system via the CLI.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('Create a user. What is his/her email?');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit($this->error('Invalid email address stublifer.'));
        }

        if (\App\Models\User::where('email', $email)->exists()) {
            exit($this->error('User already exists.'));
        }

        $password = $this->secret('Password for this new user?');

        $firstname = $this->ask('What is his/her first name?');
        $lastname = $this->ask('What is his/her last name?');
        $roles = Role::all()->pluck('name')->toArray();

        $role = $this->choice('What is his/her role?', $roles, 1);

        $user = \App\Models\User::create([
            'email' => $email,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'password' => \Hash::make($password),
            'role_id' => Role::where('name', $role)->first()->id,
        ]);
        $this->info('Account created successfully!');
        echo $this->table(['first_name', 'email', 'role', 'last_name'], [
            [$user->first_name, $user->email, $user->role->name, $user->last_name],
        ]);
    }
}
