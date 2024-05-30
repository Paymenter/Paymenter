<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Utils\Permissions;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Get all roles.
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        return view('admin.roles.index');
    }

    /**
     * Edit role.
     */
    public function edit(Role $role): \Illuminate\Contracts\View\View
    {
        $permissions = Permissions::$flags;

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Create role.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        $permissions = Permissions::$flags;

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store role.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);
        $permissions = $request->permissions;
        $permissions[] = 'ADMINISTRATOR';
        Role::create([
            'name' => $request->name,
            'permissions' => Permissions::create($permissions),
        ]);

        return redirect()->route('admin.roles')->with('success', 'Role created successfully');
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);
        $permissions = $request->permissions;
        $permissions[] = 'ADMINISTRATOR';
        $role->update([
            'name' => $request->name,
            'permissions' => Permissions::create($permissions),
        ]);

        return redirect()->route('admin.roles')->with('success', 'Role updated successfully');
    }
}
