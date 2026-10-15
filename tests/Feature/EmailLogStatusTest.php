<?php

namespace Tests\Feature;

use App\Helpers\NotificationHelper;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Tests\TestCase;

class EmailLogStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['settings.mail_disable' => false]);
    }

    public function test_queued_template_mail_marks_its_log_as_sent(): void
    {
        $user = User::factory()->create();

        $template = NotificationTemplate::create([
            'key' => 'test_template',
            'subject' => 'Template subject',
            'body' => 'Template body',
            'enabled' => true,
        ]);

        NotificationHelper::sendEmailNotification($template, [], $user);

        $this->assertDatabaseHas('email_logs', [
            'to' => $user->email,
            'subject' => 'Template subject',
            'status' => 'sent',
        ]);
    }

    public function test_queued_system_mail_marks_its_log_as_sent(): void
    {
        NotificationHelper::sendSystemEmailNotification('System subject', 'System body', email: 'admin@example.com');

        $this->assertDatabaseHas('email_logs', [
            'to' => 'admin@example.com',
            'subject' => 'System subject',
            'status' => 'sent',
        ]);
        $this->assertDatabaseMissing('email_logs', ['sent_at' => null]);
    }

    public function test_failed_queued_system_mail_marks_its_log_as_failed(): void
    {
        config([
            'mail.default' => 'failing',
            'mail.mailers.failing' => ['transport' => 'failing'],
        ]);

        Mail::extend('failing', fn () => new class extends AbstractTransport
        {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('Connection refused');
            }

            public function __toString(): string
            {
                return 'failing://';
            }
        });

        try {
            NotificationHelper::sendSystemEmailNotification('System subject', 'System body', email: 'admin@example.com');
        } catch (TransportException $e) {
            // The sync queue rethrows the job's exception after marking it failed
        }

        $this->assertDatabaseHas('email_logs', [
            'to' => 'admin@example.com',
            'status' => 'failed',
            'error' => 'Connection refused',
        ]);
    }
}
