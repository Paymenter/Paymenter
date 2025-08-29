<?php

namespace App\Console\Commands\User;

use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Password;
use Throwable;

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
    protected $description = 'Send password reset email to a user';

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

        try {
            NotificationHelper::passwordResetNotification($user, [
                'url' => url(route('password.reset', [
                    'token' => Password::createToken($user),
                    'email' => $user->email,
                ], false)),
            ]);

            $this->info("Password reset email sent successfully to '{$email}'");
        } catch (Throwable $e) {
            $this->error('Failed to send password reset email: ' . $e->getMessage());
        }
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'email' => 'What is the user\'s email address?',
        ];
    }
}
