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
        $ticketUserId = $attachment->ticketMessage?->ticket?->user_id;

        return $user->hasPermission('admin.tickets.view')
            || (!is_null($ticketUserId) && $user->id === $ticketUserId);
    }
}
