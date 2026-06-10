<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = RoleResource::collection(
            Role::query()
                ->when(! auth()->user()->hasPermission('roles', 'read'), fn ($query) => $query
                    ->where('created_by', auth()->id()))
                ->when(request('search'), fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->withCount('users')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        );

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => request()->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'resources' => config('permissions.resources'),
            'actions' => config('permissions.actions'),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'description' => $request->description,
            'permissions' => $request->permissions,
            'created_by' => auth()->id(),
        ]);

        return to_route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions,
            ],
            'resources' => config('permissions.resources'),
            'actions' => config('permissions.actions'),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'description' => $request->description,
            'permissions' => $request->permissions,
        ]);

        return to_route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return to_route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
