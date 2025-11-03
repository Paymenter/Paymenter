<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_log_id;

    public $mail;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $mail,
    ) {
        $this->mail = $mail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mail['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $appName = config('app.name');
        $body = <<<HTML
        <p>Hi,</p>

        {$this->mail['body']}

        <small>This is an automated message sent from {$appName}</small>
        HTML;

        return new Content(
            html: 'components.mail.system',
            with: ['body' => $body],
        );
    }
}
