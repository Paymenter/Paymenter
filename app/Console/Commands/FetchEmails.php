<?php

namespace App\Console\Commands;

use App\Models\TicketMailLog;
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

                // Check headers to see if this email is a reply
                $replyTo = $email->inReplyTo();
                if (!$replyTo || count($replyTo) === 0) {
                    // Create email log but don't process
                    $this->failedEmailLog($email);

                    continue;
                }

                // Validate if in reply to another ticket (<ticket message id>@hostname)

                if (!preg_match('/^(\d+)@/', $replyTo[0], $matches)) {
                    $this->failedEmailLog($email);

                    continue;
                }

                $ticketMessageId = $matches[1];
                // Check if the ticket exists
                $ticketMessage = TicketMessage::find($ticketMessageId);
                if (!$ticketMessage) {
                    $this->failedEmailLog($email);

                    continue;
                }

                $ticket = $ticketMessage->ticket;

                // Check if from email matches ticket's email
                if ($email->from()->email() !== $ticket->user->email) {
                    $this->failedEmailLog($email);

                    continue;
                }

                // // Log the successful email processing
                $ticketMailLog = TicketMailLog::create([
                    'message_id' => $email->messageId(),
                    'subject' => $email->subject(),
                    'from' => $email->from()->email(),
                    'to' => $email->to()[0]->email(),
                    'body' => $email->text(),
                    'status' => 'processed',
                ]);

                // // Add reply to ticket
                $message = $ticket->messages()->create([
                    'message' => $body,
                    'user_id' => $ticket->user_id,
                    'ticket_mail_log_id' => $ticketMailLog->id,
                ]);

                // Foreach attachment
                foreach ($email->attachments() as $attachment) {
                    $extension = pathinfo($attachment->filename(), PATHINFO_EXTENSION);
                    // Randomize filename
                    $newName = Str::ulid() . '.' . $extension;
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
        } catch (\Exception $e) {
            \Log::error('FetchEmails failed: ' . $e->getMessage());
        } finally {
            // Ensure the mailbox is disconnected
            if (isset($mailbox)) {
                $mailbox->disconnect();
            }
        }
    }

    private function failedEmailLog($email): TicketMailLog
    {
        return TicketMailLog::create([
            'message_id' => $email->messageId(),
            'subject' => $email->subject(),
            'from' => $email->from()->email(),
            'to' => $email->to()[0]->email(),
            'body' => $email->text(),
            'status' => 'unprocessed',
        ]);
    }
}
