<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Support\TicketMailId;
use DirectoryTree\ImapEngine\Mailbox;
use DirectoryTree\ImapEngine\Testing\FakeFolder;
use DirectoryTree\ImapEngine\Testing\FakeMailbox;
use DirectoryTree\ImapEngine\Testing\FakeMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FetchEmailsTest extends TestCase
{
    use RefreshDatabase;

    private Ticket $ticket;

    private TicketMessage $staffReply;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'settings.ticket_mail_piping' => true,
            'settings.ticket_mail_email' => 'support@example.com',
        ]);

        $customer = User::factory()->create(['email' => 'customer@example.com']);
        $staff = User::factory()->create();

        $this->ticket = Ticket::factory()->create(['user_id' => $customer->id, 'status' => 'open']);
        TicketMessage::factory()->create(['ticket_id' => $this->ticket->id, 'user_id' => $customer->id]);
        $this->staffReply = TicketMessage::factory()->create(['ticket_id' => $this->ticket->id, 'user_id' => $staff->id]);
    }

    public function test_reply_to_the_sent_notification_is_added_to_the_ticket(): void
    {
        $sent = collect(app('mailer')->getSymfonyTransport()->messages())->last();
        $messageId = $sent->getOriginalMessage()->getHeaders()->get('Message-ID')->getBodyAsString();

        $this->fetch($this->reply(trim($messageId, '<>')));

        $this->assertSame(3, $this->ticket->messages()->count());
        $this->assertDatabaseHas('ticket_mail_logs', ['message_id' => 'reply@mail.example.com', 'status' => 'processed']);
    }

    public function test_reply_with_a_guessed_message_id_is_refused(): void
    {
        $this->fetch($this->reply($this->staffReply->id . '@localhost'));

        $this->assertSame(2, $this->ticket->messages()->count());
        $this->assertDatabaseHas('ticket_mail_logs', ['message_id' => 'reply@mail.example.com', 'status' => 'unprocessed']);
    }

    public function test_reply_from_another_address_is_refused(): void
    {
        $this->fetch($this->reply(TicketMailId::localPart($this->staffReply->id) . '@localhost', 'someone@example.net'));

        $this->assertSame(2, $this->ticket->messages()->count());
    }

    public function test_attachments_are_capped_at_the_upload_limits(): void
    {
        $attachments = [['large.bin', str_repeat('a', 10240 * 1024 + 1)], ...array_fill(0, 11, ['small.txt', 'hello'])];
        File::ensureDirectoryExists(storage_path('app/tickets/uploads'));

        $this->fetch($this->reply(TicketMailId::localPart($this->staffReply->id) . '@localhost', attachments: $attachments));

        $message = $this->ticket->messages()->latest('id')->first();
        $paths = $message->attachments()->pluck('path');
        $paths->each(fn ($path) => File::delete(storage_path('app/' . $path)));

        // Only the first ten are considered, and the oversized one among them is skipped
        $this->assertCount(9, $paths);
        $this->assertSame(['small.txt'], $message->attachments()->distinct()->pluck('filename')->all());
    }

    private function fetch(string $raw): void
    {
        $mailbox = new FakeMailbox(folders: [new FakeFolder('inbox', messages: [new FakeMessage(1, contents: $raw)])]);
        $this->app->bind(Mailbox::class, fn () => $mailbox);

        $this->artisan('app:fetch-emails')->assertSuccessful();
    }

    private function reply(string $inReplyTo, string $from = 'customer@example.com', array $attachments = []): string
    {
        $headers = implode("\r\n", [
            'From: Customer <' . $from . '>',
            'To: support@example.com',
            'Subject: Re: ' . $this->ticket->subject,
            'Message-ID: <reply@mail.example.com>',
            'In-Reply-To: <' . $inReplyTo . '>',
            'Date: ' . now()->toRfc2822String(),
            'MIME-Version: 1.0',
            'Content-Type: multipart/mixed; boundary="boundary"',
        ]);

        $parts = ["Content-Type: text/plain; charset=utf-8\r\n\r\nThanks, that fixed it."];
        foreach ($attachments as [$filename, $contents]) {
            $parts[] = "Content-Type: application/octet-stream; name=\"{$filename}\"\r\n"
                . "Content-Disposition: attachment; filename=\"{$filename}\"\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($contents));
        }

        return $headers . "\r\n\r\n--boundary\r\n" . implode("\r\n--boundary\r\n", $parts) . "\r\n--boundary--\r\n";
    }
}
