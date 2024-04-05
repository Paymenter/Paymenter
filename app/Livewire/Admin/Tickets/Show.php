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
            'message' => 'required_without:attachments',
            'attachments.*' => 'nullable|file',
        ], [
            'message.required_without' => 'Either message or attachment is required',
        ]);

        $message = $this->ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $this->message, 
        ]);

        foreach ($this->attachments as $attachment) {
            FileUploadHelper::upload($attachment, $message, TicketMessage::class);
            
        }

        $this->ticket->update([
            'status' => 'replied',
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
