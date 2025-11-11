<?php

namespace App\Http\Controllers;

use App\Models\TicketMessageAttachment;

class TicketAttachmentController extends Controller
{
    public function download(TicketMessageAttachment $attachment)
    {
        return response()->download($attachment->localPath, $attachment->filename);
    }
}
