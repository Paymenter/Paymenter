<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\InvoiceItems\CreateInvoiceItemRequest;
use App\Http\Requests\Api\Admin\InvoiceItems\DeleteInvoiceItemRequest;
use App\Http\Requests\Api\Admin\InvoiceItems\GetInvoiceItemRequest;
use App\Http\Requests\Api\Admin\InvoiceItems\GetInvoiceItemsRequest;
use App\Http\Requests\Api\Admin\InvoiceItems\UpdateInvoiceItemRequest;
use App\Http\Resources\InvoiceItemResource;
use App\Models\InvoiceItem;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'InvoiceItems', weight: 5)]
class InvoiceItemController extends ApiController
{
    protected const INCLUDES = [
        'gateway',
        'reference',
        'invoice',
    ];

    /**
     * List invoice items
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetInvoiceItemsRequest $request)
    {
        // Fetch invoices with pagination
        $invoices = QueryBuilder::for(InvoiceItem::class)
            ->allowedFilters(['id', 'quantity', 'price', 'reference_type', 'reference_id'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'quantity', 'price'])
            ->simplePaginate(request('per_page', 15));

        // Return the invoices as a JSON response
        return InvoiceItemResource::collection($invoices);
    }

    /**
     * Create a new invoice item
     */
    public function store(CreateInvoiceItemRequest $request)
    {
        // Validate and create the invoice item
        $invoiceItem = InvoiceItem::create($request->validated());

        // Return the created invoice as a JSON response
        return new InvoiceItemResource($invoiceItem);
    }

    /**
     * Show a specific invoice item
     */
    public function show(GetInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        $invoiceItem = QueryBuilder::for(InvoiceItem::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($invoiceItem->id);

        // Return the invoice item as a JSON response
        return new InvoiceItemResource($invoiceItem);
    }

    /**
     * Update a specific invoice item
     */
    public function update(UpdateInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        // Validate and update the invoice item
        $invoiceItem->update($request->validated());

        // Return the updated invoice item as a JSON response
        return new InvoiceItemResource($invoiceItem);
    }

    /**
     * Delete a specific invoice item
     */
    public function destroy(DeleteInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        // Delete the invoice item
        $invoiceItem->delete();

        return $this->returnNoContent();
    }
}
