<?php

namespace App\Policies;

use App\Models\TicketMessageAttachment;
use App\Models\User;

class TicketMessageAttachmentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketMessageAttachment $attachment): bool
    {
        return $user->hasPermission('admin.tickets.view') || $user->id === $attachment->ticketMessage->ticket->user_id;
    }
}
