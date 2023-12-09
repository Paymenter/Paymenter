<?php

namespace App\Livewire\Admin\Tickets;

use App\Helpers\FileUploadHelper;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Ticket $ticket;

    public $attachments = [];
    public $message;

    public function reply()
    {
        $this->validate([
            'message' => 'required|min:3',
        ]);

        $message = $this->ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);

        foreach ($this->attachments as $attachment) {
            FileUploadHelper::upload($attachment, $message, TicketMessage::class);
            
        }

        $this->ticket->update([
            'status' => 'waiting',
        ]);

        $this->message = '';
        $this->attachments = [];

        $this->dispatch('refreshMessages');
    }
    
    public function render()
    {
        return view('livewire.admin.tickets.show');
    }
}
