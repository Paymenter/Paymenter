<?php

namespace App\Livewire\Tickets;

use App\Attributes\DisabledIf;
use App\Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[DisabledIf('tickets_disabled')]
class Show extends Component
{
    use WithFileUploads;

    #[Locked]
    public Ticket $ticket;

    public array $attachments = [];

    #[Rule('required', 'string')]
    public string $message;

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

    public function save()
    {
        $this->validate();

        $this->ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->notify('Message sent successfully');

        $this->message = '';
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('tickets.show')->layoutData([
            'sidebar' => true,
        ]);
    }
}
