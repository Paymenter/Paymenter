<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Paymenter\Extensions\Others\Affiliates\Http\Requests\CreateAffiliateRequest;
use Paymenter\Extensions\Others\Affiliates\Http\Requests\DeleteAffiliateRequest;
use Paymenter\Extensions\Others\Affiliates\Http\Requests\GetAffiliateRequest;
use Paymenter\Extensions\Others\Affiliates\Http\Requests\GetAffiliatesRequest;
use Paymenter\Extensions\Others\Affiliates\Http\Requests\UpdateAffiliateRequest;
use Paymenter\Extensions\Others\Affiliates\Http\Resources\AffiliateResource;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Affiliates', weight: 1)]
class AffiliateController extends ApiController
{
    protected const INCLUDES = [
        'user',
        'orders',
    ];

    /**
     * List Affiliates
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetAffiliatesRequest $request)
    {
        // Fetch affiliates with pagination
        $affiliates = QueryBuilder::for(Affiliate::class)
            ->allowedFilters(['affiliate_id', 'code', 'visitors', 'reward', 'discount'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'code', 'visitors', 'reward', 'discount', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        // Return the affiliates as a JSON response
        return AffiliateResource::collection($affiliates);
    }

    /**
     * Create a new affiliate
     */
    public function store(CreateAffiliateRequest $request)
    {
        // Validate and create the affiliate
        $affiliate = Affiliate::create($request->validated());

        // Return the created affiliate as a JSON response
        return new AffiliateResource($affiliate);
    }

    /**
     * Show a specific affiliate
     */
    public function show(GetAffiliateRequest $request, Affiliate $affiliate)
    {
        $affiliate = QueryBuilder::for(Affiliate::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($affiliate->id);

        // Return the affiliate as a JSON response
        return new AffiliateResource($affiliate);
    }

    /**
     * Update a specific affiliate
     */
    public function update(UpdateAffiliateRequest $request, Affiliate $affiliate)
    {
        // Validate and update the affiliate
        $affiliate->update($request->validated());

        // Return the updated affiliate as a JSON response
        return new AffiliateResource($affiliate);
    }

    /**
     * Delete a specific affiliate
     */
    public function destroy(DeleteAffiliateRequest $request, Affiliate $affiliate)
    {
        // Delete the affiliate
        $affiliate->delete();

        return $this->returnNoContent();
    }
}
