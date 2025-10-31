<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Tickets\CreateTicketRequest;
use App\Http\Requests\Api\Admin\Tickets\DeleteTicketRequest;
use App\Http\Requests\Api\Admin\Tickets\GetTicketRequest;
use App\Http\Requests\Api\Admin\Tickets\GetTicketsRequest;
use App\Http\Requests\Api\Admin\Tickets\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Tickets', weight: 5)]
class TicketController extends ApiController
{
    protected const INCLUDES = [
        'messages',
        'user',
        'assigned_to',
    ];

    /**
     * List Tickets
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetTicketsRequest $request)
    {
        // Fetch tickets with pagination
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedFilters(['id', 'currency_code', 'user_id', 'status', 'priority', 'department'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'currency_code', 'status', 'priority', 'department'])
            ->simplePaginate(request('per_page', 15));

        // Return the tickets as a JSON response
        return TicketResource::collection($tickets);
    }

    /**
     * Create a new ticket
     */
    public function store(CreateTicketRequest $request)
    {
        // Validate and create the ticket
        $ticket = Ticket::create($request->validated());

        // Return the created ticket as a JSON response
        return new TicketResource($ticket);
    }

    /**
     * Show a specific ticket
     */
    public function show(GetTicketRequest $request, Ticket $ticket)
    {
        $ticket = QueryBuilder::for(Ticket::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($ticket->id);

        // Return the ticket as a JSON response
        return new TicketResource($ticket);
    }

    /**
     * Update a specific ticket
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // Validate and update the ticket
        $ticket->update($request->validated());

        // Return the updated ticket as a JSON response
        return new TicketResource($ticket);
    }

    /**
     * Delete a specific ticket
     */
    public function destroy(DeleteTicketRequest $request, Ticket $ticket)
    {
        // Delete the ticket
        $ticket->delete();

        return $this->returnNoContent();
    }
}
