<?php

namespace App\Livewire\Tickets;

use App\Attributes\DisabledIf;
use App\Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
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

    public function completeUpload($filename)
    {
        // Find the attachment by its name
        foreach ($this->attachments as $key => $attachment) {
            if ($attachment->getFilename() === $filename) {
                $url = $attachment->store('public/ticket-attachments');
                $url = Storage::url($url);

                return url($url);
            }
        }
    }

    public function create()
    {
        // Add rules for the department
        $this->validate([
            'department' => count((array) config('settings.ticket_departments')) > 0 ? 'required|in:' . implode(',', array_values((array) config('settings.ticket_departments'))) : '',
            'service' => 'nullable|exists:services,id',
            'subject' => 'required|string',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        if (RateLimiter::tooManyAttempts('create-ticket', 1)) {
            $this->notify('Too many ticket creation attempts. Please try again in 60 seconds.', 'error');

            return;
        }

        RateLimiter::increment('create-ticket', 30);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'department' => $this->department,
            'service_id' => $this->service,
            'subject' => $this->subject,
            'priority' => $this->priority,
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->notify('Message sent successfully');

        $this->message = '';
        $this->dispatch('saved');

        $this->redirect(route('tickets.show', $ticket), true);
    }

    public function render()
    {
        /** @var \App\Models\User */
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
