<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Users\GetUserRequest;
use App\Http\Requests\Api\Admin\Users\GetUsersRequest;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Dedoc\Scramble\Attributes\QueryParameter;

class UserController extends ApiController
{
    protected const INCLUDES = [
        'properties',
        'orders',
        'services',
        'invoices',
        'tickets',
        'credits',
    ];

    /**
     * List Users
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetUsersRequest $request)
    {
        // Fetch users with pagination
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['first_name', 'last_name', 'email'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'first_name', 'last_name', 'email', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        // Return the users as a JSON response
        return UserResource::collection($users);
    }

    /**
     * Show a specific user
     */
    public function show(GetUserRequest $request, User $user)
    {
        $user = QueryBuilder::for(User::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($user->id);

        // Return the user as a JSON response
        return new UserResource($user);
    }
}
