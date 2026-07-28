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

    /**
     * Kept for listeners (e.g. ticket reply headers). Rendering uses resolved strings.
     */
    public NotificationTemplate $emailTemplate;

    /**
     * Resolved subject template (captured before queue serialization).
     */
    public string $resolvedSubject;

    /**
     * Resolved body template (captured before queue serialization).
     */
    public string $resolvedBody;

    /**
     * Create a new message instance.
     *
     * Subject/body are stored as plain strings so queued workers do not reload
     * untranslated base columns from the database.
     */
    public function __construct(
        NotificationTemplate $emailTemplate,
        array $data = []
    ) {
        $this->emailTemplate = $emailTemplate;
        $this->resolvedSubject = (string) $emailTemplate->subject;
        $this->resolvedBody = (string) $emailTemplate->body;
        $this->data = $data;
        $this->data['body'] = $this->resolvedBody;
        $this->data['emailTemplate'] = $emailTemplate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BladeCompiler::render($this->resolvedSubject, $this->data),
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
