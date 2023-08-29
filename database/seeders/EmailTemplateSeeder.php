<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Mail\Markdown;
use Spatie\MailTemplates\Models\MailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!MailTemplate::where('mailable', \App\Mail\Test::class)->exists()) {
            $html = '
# Hello, {{ name }}.

This is a test mail.

You can use markdown here

## This is a heading
### This is a subheading

- This is a list
- This is a list
- This is a list';
            $html = Markdown::parse($html);
            MailTemplate::create([
                'mailable' => \App\Mail\Test::class,
                'subject' => 'Test Mail',
                'html_template' => $html,
                'text_template' => 'Hello, {{ name }}.'
            ]);
        }
    }
}
