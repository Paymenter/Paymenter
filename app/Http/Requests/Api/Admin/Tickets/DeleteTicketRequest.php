<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.delete';
}
