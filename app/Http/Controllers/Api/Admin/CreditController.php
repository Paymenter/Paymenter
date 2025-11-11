<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Credits\CreateCreditRequest;
use App\Http\Requests\Api\Admin\Credits\DeleteCreditRequest;
use App\Http\Requests\Api\Admin\Credits\GetCreditRequest;
use App\Http\Requests\Api\Admin\Credits\GetCreditsRequest;
use App\Http\Requests\Api\Admin\Credits\UpdateCreditRequest;
use App\Http\Resources\CreditResource;
use App\Models\Credit;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Credits', weight: 4)]
class CreditController extends ApiController
{
    protected const INCLUDES = [
        'user',
    ];

    /**
     * List Credits
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetCreditsRequest $request)
    {
        // Fetch credits with pagination
        $credits = QueryBuilder::for(Credit::class)
            ->allowedFilters(['id', 'currency_code', 'user_id'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'currency_code'])
            ->simplePaginate(request('per_page', 15));

        // Return the credits as a JSON response
        return CreditResource::collection($credits);
    }

    /**
     * Create a new credit
     */
    public function store(CreateCreditRequest $request)
    {
        // Validate and create the credit
        $credit = Credit::create($request->validated());

        // Return the created credit as a JSON response
        return new CreditResource($credit);
    }

    /**
     * Show a specific credit
     */
    public function show(GetCreditRequest $request, Credit $credit)
    {
        $credit = QueryBuilder::for(Credit::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($credit->id);

        // Return the credit as a JSON response
        return new CreditResource($credit);
    }

    /**
     * Update a specific credit
     */
    public function update(UpdateCreditRequest $request, Credit $credit)
    {
        // Validate and update the credit
        $credit->update($request->validated());

        // Return the updated credit as a JSON response
        return new CreditResource($credit);
    }

    /**
     * Delete a specific credit
     */
    public function destroy(DeleteCreditRequest $request, Credit $credit)
    {
        // Delete the credit
        $credit->delete();

        return $this->returnNoContent();
    }
}
