<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Hash;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Password;
use Str;

class PasswordReset extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user\'s password and show the new password in the console';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->argument('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format');

            return;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found");

            return;
        }

        if (!$this->confirm("Are you sure you want to reset the password for user with email '{$email}'?")) {
            $this->info('Operation cancelled.');

            return;
        }

        // Make a strong password
        $password = Str::password(16);
        $user->forceFill([
            'password' => Hash::make($password),
        ])->setRememberToken(Str::random(60));

        $user->save();

        // Output the new password to the console
        $this->info("Password for user with email '{$email}' has been reset.");
        $this->info("New password: <options=bold;fg=red>{$password}</>");
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'email' => 'What is the user\'s email address?',
        ];
    }
}
