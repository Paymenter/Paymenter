<?php

namespace App\Console\Commands\User;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;

class Disable2FA extends Command
{
    /**
     * Disable 2FA for a specific user
     *
     * @var string
     */
    protected $signature = 'p:user:disable-2fa {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable 2FA for a specific user';

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

        if(!$user->tfa_secret) {
            exit($this->error('2FA is not enabled for this user.'));
        }

        $user->tfa_secret = null;
        $user->save();

        $this->info('2FA disabled successfully');

        return Command::SUCCESS;
    }
}
