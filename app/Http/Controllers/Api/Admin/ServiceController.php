<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Services\CreateServiceRequest;
use App\Http\Requests\Api\Admin\Services\DeleteServiceRequest;
use App\Http\Requests\Api\Admin\Services\GetServiceRequest;
use App\Http\Requests\Api\Admin\Services\GetServicesRequest;
use App\Http\Requests\Api\Admin\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Services', weight: 3)]
class ServiceController extends ApiController
{
    protected const INCLUDES = [
        'order',
        'coupon',
        'user',
        'product',
        'invoices',
        'properties',
    ];

    /**
     * List Services
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetServicesRequest $request)
    {
        // Fetch services with pagination
        $services = QueryBuilder::for(Service::class)
            ->allowedFilters(['quantity', 'price', 'expires_at', 'subscription_id', 'status'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'expires_at', 'status'])
            ->simplePaginate(request('per_page', 15));

        // Return the services as a JSON response
        return ServiceResource::collection($services);
    }

    /**
     * Create a new service
     */
    public function store(CreateServiceRequest $request)
    {
        // Validate and create the service
        $service = Service::create($request->validated());

        // Return the created service as a JSON response
        return new ServiceResource($service);
    }

    /**
     * Show a specific service
     */
    public function show(GetServiceRequest $request, Service $service)
    {
        $service = QueryBuilder::for(Service::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($service->id);

        // Return the service as a JSON response
        return new ServiceResource($service);
    }

    /**
     * Update a specific service
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Validate and update the service
        $service->update($request->validated());

        // Return the updated service as a JSON response
        return new ServiceResource($service);
    }

    /**
     * Delete a specific service
     */
    public function destroy(DeleteServiceRequest $request, Service $service)
    {
        // Delete the service
        $service->delete();

        return $this->returnNoContent();
    }
}
