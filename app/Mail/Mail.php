<?php

namespace App\Mail;

use App\Models\NotificationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\View\Compilers\BladeCompiler;

class Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_log_id;

    public array $data = [];

    public NotificationTemplate $emailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(
        NotificationTemplate $emailTemplate,
        array $data = []
    ) {
        $this->emailTemplate = $emailTemplate;
        $this->data = $data;
        $this->data['body'] = $this->emailTemplate->body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BladeCompiler::render($this->emailTemplate->subject, $this->data),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'components.mail.base',
            with: $this->data,
        );
    }
}
