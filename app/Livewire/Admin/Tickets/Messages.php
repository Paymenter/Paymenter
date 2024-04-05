<?php

namespace App\Livewire\Admin\Tickets;

use App\Helpers\FileUploadHelper;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On; 

class Messages extends Component
{
    use WithFileUploads;
    public Ticket $ticket;
    public  $messages;


    public function mount()
    {
        $this->updateMessages();
    }

    #[On('refreshMessages')]
    public function updateMessages()
    {
        $this->messages = $this->ticket->messages()->with('user')->get();
    }

    public function render()
    {
        return view('livewire.admin.tickets.messages');
    }
}
