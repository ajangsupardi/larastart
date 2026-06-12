<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrashController extends Controller
{
    public function users(Request $request): Response
    {
        $search = $request->get('search');

        $users = User::query()
            ->onlyTrashed()
            ->when($search, fn ($query, $search) => $query
                ->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($search).'%']);
                }))
            ->with('roles')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Trash/Users', [
            'users' => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function roles(Request $request): Response
    {
        $search = $request->get('search');

        $roles = Role::query()
            ->onlyTrashed()
            ->when($search, fn ($query, $search) => $query
                ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
            ->withCount('users')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Trash/Roles', [
            'roles' => $roles,
            'filters' => $request->only('search'),
        ]);
    }

    public function restoreUser(User $user)
    {
        $user->restore();

        return to_route('trash.users')
            ->with('success', 'User restored successfully.');
    }

    public function forceDeleteUser(User $user)
    {
        $user->forceDelete();

        return to_route('trash.users')
            ->with('success', 'User permanently deleted.');
    }

    public function restoreRole(Role $role)
    {
        $role->restore();

        return to_route('trash.roles')
            ->with('success', 'Role restored successfully.');
    }

    public function forceDeleteRole(Role $role)
    {
        $role->forceDelete();

        return to_route('trash.roles')
            ->with('success', 'Role permanently deleted.');
    }
}
