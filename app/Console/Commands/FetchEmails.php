<?php

namespace App\Console\Commands;

use App\Models\TicketMailLog;
use App\Models\Ticket;
use App\Models\TicketMessage;
use DirectoryTree\ImapEngine\Mailbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FetchEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ticket emails using IMAP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('settings.ticket_mail_piping', false)) {
            $this->info('Email piping is not enabled. Skipping email fetch.');

            return;
        }
        Config::set('audit.console', true);

        try {
            $mailbox = new Mailbox([
                'port' => config('settings.ticket_mail_port'),
                'username' => config('settings.ticket_mail_email'),
                'password' => config('settings.ticket_mail_password'),
                'host' => config('settings.ticket_mail_host'),
            ]);

            // Fetch emails from the mailbox
            $emails = $mailbox->inbox();

            foreach ($emails->messages()->since(now()->subDays(1))->withHeaders()->withBody()->get() as $email) {
                if (TicketMailLog::where('message_id', $email->messageId())->exists()) {
                    continue;
                }

                $body = \EmailReplyParser\EmailReplyParser::parseReply($email->text());
                if (!trim($body)) {
                    $body = (string) $email->text();
                }

                if ($this->processReplyEmail($email, $body)) {
                    continue;
                }

                if ($this->createGuestTicketFromEmail($email, $body)) {
                    continue;
                }

                $this->failedEmailLog($email);
            }
        } catch (\Exception $e) {
            \Log::error('FetchEmails failed: ' . $e->getMessage());
        } finally {
            // Ensure the mailbox is disconnected
            if (isset($mailbox)) {
                $mailbox->disconnect();
            }
        }
    }

    private function processReplyEmail($email, string $body): bool
    {
        $replyTo = $email->inReplyTo();
        if (!$replyTo || count($replyTo) === 0) {
            return false;
        }

        if (!preg_match('/^(\d+)@/', (string) $replyTo[0], $matches)) {
            return false;
        }

        $ticketMessage = TicketMessage::find($matches[1]);
        if (!$ticketMessage) {
            return false;
        }

        $ticket = $ticketMessage->ticket;
        if (!$ticket) {
            return false;
        }

        $senderEmail = $this->normalizeEmail($email->from()?->email());
        $ticketEmail = $this->normalizeEmail($ticket->ownerEmail());

        if (!$senderEmail || !$ticketEmail || $senderEmail !== $ticketEmail) {
            return false;
        }

        $ticketMailLog = $this->createEmailLog($email, 'processed');

        $message = $ticket->messages()->create([
            'message' => $body,
            'user_id' => $ticket->user_id,
            'ticket_mail_log_id' => $ticketMailLog->id,
        ]);

        $this->storeAttachments($email, $message);

        return true;
    }

    private function createGuestTicketFromEmail($email, string $body): bool
    {
        if (!config('settings.ticket_mail_create_guest_tickets', false)) {
            return false;
        }

        $senderEmail = $this->normalizeEmail($email->from()?->email());
        if (!$senderEmail) {
            return false;
        }

        $guestName = trim((string) $email->from()?->name());
        if (!$guestName) {
            $guestName = Str::before($senderEmail, '@');
        }

        $ticket = Ticket::create([
            'user_id' => null,
            'guest_name' => $guestName,
            'guest_email' => $senderEmail,
            'department' => collect((array) config('settings.ticket_departments'))->first(),
            'subject' => $email->subject() ?: 'Email ticket',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $ticketMailLog = $this->createEmailLog($email, 'processed');

        $message = $ticket->messages()->create([
            'user_id' => null,
            'message' => $body,
            'ticket_mail_log_id' => $ticketMailLog->id,
        ]);

        $this->storeAttachments($email, $message);

        return true;
    }

    private function createEmailLog($email, string $status): TicketMailLog
    {
        return TicketMailLog::create([
            'message_id' => $email->messageId(),
            'subject' => $email->subject(),
            'from' => $email->from()?->email() ?: '',
            'to' => $this->resolveRecipient($email),
            'body' => $email->text(),
            'status' => $status,
        ]);
    }

    private function storeAttachments($email, TicketMessage $message): void
    {
        foreach ($email->attachments() as $attachment) {
            $extension = pathinfo($attachment->filename(), PATHINFO_EXTENSION);
            $newName = Str::ulid() . ($extension ? '.' . $extension : '');
            $path = 'tickets/uploads/' . $newName;

            $attachment->save(storage_path('app/' . $path));

            $message->attachments()->create([
                'path' => $path,
                'filename' => $attachment->filename(),
                'mime_type' => File::mimeType(storage_path('app/' . $path)),
                'filesize' => File::size(storage_path('app/' . $path)),
            ]);
        }
    }

    private function resolveRecipient($email): string
    {
        $to = $email->to();
        if (!is_array($to) || count($to) === 0) {
            return '';
        }

        return $to[0]?->email() ?? '';
    }

    private function normalizeEmail(?string $email): ?string
    {
        if (!$email) {
            return null;
        }

        $email = Str::lower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function failedEmailLog($email): TicketMailLog
    {
        return $this->createEmailLog($email, 'unprocessed');
    }
}
