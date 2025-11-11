<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\TicketMessages\CreateTicketMessageRequest;
use App\Http\Requests\Api\Admin\TicketMessages\DeleteTicketMessageRequest;
use App\Http\Requests\Api\Admin\TicketMessages\GetTicketMessageRequest;
use App\Http\Requests\Api\Admin\TicketMessages\GetTicketMessagesRequest;
use App\Http\Resources\TicketMessageResource;
use App\Models\TicketMessage;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Ticket Messages', weight: 6)]
class TicketMessageController extends ApiController
{
    protected const INCLUDES = [
        'user',
        'ticket',
        'attachments',
    ];

    /**
     * List TicketMessages
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetTicketMessagesRequest $request)
    {
        // Fetch ticketMessages with pagination
        $ticketMessages = QueryBuilder::for(TicketMessage::class)
            ->allowedFilters(['id', 'currency_code'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'currency_code'])
            ->simplePaginate(request('per_page', 15));

        // Return the ticketMessages as a JSON response
        return TicketMessageResource::collection($ticketMessages);
    }

    /**
     * Create a new ticketMessage
     */
    public function store(CreateTicketMessageRequest $request)
    {
        // Validate and create the ticketMessage
        $ticketMessage = TicketMessage::create($request->validated());

        // Return the created ticketMessage as a JSON response
        return new TicketMessageResource($ticketMessage);
    }

    /**
     * Show a specific ticketMessage
     */
    public function show(GetTicketMessageRequest $request, TicketMessage $ticketMessage)
    {
        $ticketMessage = QueryBuilder::for(TicketMessage::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($ticketMessage->id);

        // Return the ticketMessage as a JSON response
        return new TicketMessageResource($ticketMessage);
    }

    /**
     * Delete a specific ticketMessage
     */
    public function destroy(DeleteTicketMessageRequest $request, TicketMessage $ticketMessage)
    {
        // Delete the ticketMessage
        $ticketMessage->delete();

        return $this->returnNoContent();
    }
}
