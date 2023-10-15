<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyEmail extends Command
{
    /**
     * Verify the email of a specific user
     *
     * @var string
     */
    protected $signature = 'p:user:verify-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the email of a specific user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('What is the email of the user?');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit($this->error('Invalid email address stublifer.'));
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            exit($this->error('User does not exist.'));
        }

        if($user->email_verified_at) {
            exit($this->error('Email is already verified.'));
        }

        $user->email_verified_at = now();
        $user->save();

        $this->info('Email verified successfully');

        return Command::SUCCESS;
    }
}
