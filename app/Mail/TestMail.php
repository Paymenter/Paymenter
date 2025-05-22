<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestMail extends Mailable
{
    use Queueable;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email from ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: sprintf(
                '<h1>Test Email</h1><p>This is a test email from %s</p><p>Time: %s</p>',
                e(config('app.name')),
                e(now()->toDateTimeString())
            ),
        );
    }
}
