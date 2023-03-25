<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class User extends Command
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

        $password = $this->secret('Password for this new user?');

        $name = $this->ask('What is his/her name?');

        $admin = $this->confirm('Is this user an admin?');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit($this->error('Invalid email address stublifer.'));
        }

        if (\App\Models\User::where('email', $email)->exists()) {
            exit($this->error('User already exists.'));
        }

        $user = \App\Models\User::create([
            'email' => $email,
            'name' => $name,
            'password' => \Hash::make($password),
            'is_admin' => $admin,
        ]);
        $this->info('Account created successfully!');
        echo $this->table(['name', 'email', 'admin'], [
            [$user->name, $user->email, $user->is_admin ? 'yes' : 'no'],
        ]);
    }
}
