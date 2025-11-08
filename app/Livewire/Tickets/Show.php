<?php

namespace App\Livewire\Tickets;

use App\Attributes\DisabledIf;
use App\Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[DisabledIf('tickets_disabled')]
class Show extends Component
{
    use WithFileUploads;

    #[Locked]
    public Ticket $ticket;

    #[Validate(['attachments.*' => 'file|max:10240'])]
    public array $attachments = [];

    #[Rule('required', 'string')]
    public string $message;

    public function save()
    {
        $this->validate();

        $message = $this->ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        foreach ($this->attachments as $attachment) {
            $newName = Str::ulid() . '.' . $attachment->getClientOriginalExtension();
            $path = 'tickets/uploads/' . $newName;
            $attachment->storeAs('tickets/uploads', $newName);

            // Security: Sanitize filename to prevent header injection
            $safeFilename = $this->sanitizeFilename($attachment->getClientOriginalName());

            $message->attachments()->create([
                'path' => $path,
                'filename' => $safeFilename,
                'mime_type' => File::mimeType(storage_path('app/' . $path)),
                'filesize' => File::size(storage_path('app/' . $path)),
            ]);
        }

        $this->notify('Message sent successfully');

        $this->message = '';
        $this->attachments = [];
        $this->dispatch('saved');
    }

    public function closeTicket()
    {
        $this->authorize('update', $this->ticket);
        if (config('settings.ticket_client_closing_disabled', false)) {
            abort(403, 'Closing tickets is disabled.');
        }

        $this->ticket->update(['status' => 'closed']);
        $this->ticket->refresh();

        $this->notify(__('ticket.close_ticket_success'));
    }

    /**
     * Sanitize filename to prevent security issues
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove any path separators
        $filename = basename($filename);

        // Remove null bytes and control characters
        $filename = preg_replace('/[\x00-\x1F\x7F]/', '', $filename);

        // Limit length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 255 - strlen($extension) - 1) . '.' . $extension;
        }

        return $filename;
    }

    public function render()
    {
        return view('tickets.show')->layoutData([
            'sidebar' => true,
        ]);
    }
}
