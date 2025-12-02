<?php

namespace App\Livewire\Tickets;

use App\Attributes\DisabledIf;
use App\Livewire\Component;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[DisabledIf('tickets_disabled')]
class Create extends Component
{
    use WithFileUploads;

    public array $attachments = [];

    public string $message;

    public string $subject;

    public string $department;

    public ?int $service = null;

    public string $priority;

    public function create()
    {
        // Add rules for the department
        $this->validate([
            'department' => count((array) config('settings.ticket_departments')) > 0 ? 'required|in:' . implode(',', array_values((array) config('settings.ticket_departments'))) : '',
            'service' => 'nullable|exists:services,id',
            'subject' => 'required|string',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachments.*' => 'file|max:10240',
        ]);

        $rateLimitKey = 'create-ticket:' . Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $this->notify('Too many ticket creation attempts. Please try again in 30 seconds.', 'error');

            return;
        }

        RateLimiter::increment($rateLimitKey, 30);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'department' => $this->department,
            'service_id' => $this->service,
            'subject' => $this->subject,
            'priority' => $this->priority,
        ]);

        $message = $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        foreach ($this->attachments as $attachment) {
            $newName = Str::ulid() . '.' . $attachment->getClientOriginalExtension();
            $path = 'tickets/uploads/' . $newName;
            $attachment->storeAs('tickets/uploads', $newName);

            $message->attachments()->create([
                'path' => $path,
                'filename' => $attachment->getClientOriginalName(),
                'mime_type' => File::mimeType(storage_path('app/' . $path)),
                'filesize' => File::size(storage_path('app/' . $path)),
            ]);
        }

        $this->notify('Message sent successfully', redirect: true);

        $this->reset(['attachments', 'message', 'subject', 'department', 'service', 'priority']);
        $this->dispatch('saved');

        $this->redirect(route('tickets.show', $ticket), true);
    }

    public function render()
    {
        /** @var User */
        $user = Auth::user();

        return view('tickets.create', [
            'departments' => (array) config('settings.ticket_departments'),
            'services' => $user->services()->orderBy('id', 'desc')->get(),
        ])->layoutData([
            'title' => 'Create Ticket',
            'sidebar' => true,
        ]);
    }
}
