<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Users\CreateUserRequest;
use App\Http\Requests\Api\Admin\Users\DeleteUserRequest;
use App\Http\Requests\Api\Admin\Users\GetUserRequest;
use App\Http\Requests\Api\Admin\Users\GetUsersRequest;
use App\Http\Requests\Api\Admin\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Users', weight: 1)]
class UserController extends ApiController
{
    protected const INCLUDES = [
        'properties',
        'orders',
        'services',
        'invoices',
        'tickets',
        'credits',
        'role',
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
     * Create a new user
     */
    public function store(CreateUserRequest $request)
    {
        // Validate and create the user
        $user = User::create($request->validated());

        // Return the created user as a JSON response
        return new UserResource($user);
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

    /**
     * Update a specific user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Validate and update the user
        $user->update($request->validated());

        // Return the updated user as a JSON response
        return new UserResource($user);
    }

    /**
     * Delete a specific user
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        // Delete the user
        $user->delete();

        return $this->returnNoContent();
    }
}
