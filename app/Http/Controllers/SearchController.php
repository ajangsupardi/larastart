<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $query = request('q', '');

        $results = collect();

        if (strlen($query) >= 2) {
            $user = auth()->user();
            $hasUserRead = $user->hasPermission('users', 'read');
            $hasUserEdit = $user->hasPermission('users', 'update');
            $hasRoleRead = $user->hasPermission('roles', 'read');
            $hasRoleEdit = $user->hasPermission('roles', 'update');

            if ($hasUserRead) {
                $users = User::query()
                    ->where(function ($q) use ($query) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($query).'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($query).'%']);
                    })
                    ->limit(5)
                    ->get()
                    ->map(fn ($foundUser) => [
                        'label' => $foundUser->name,
                        'description' => $foundUser->email,
                        'href' => $hasUserEdit
                            ? route('users.edit', $foundUser)
                            : route('users.index'),
                        'type' => 'user',
                    ]);

                $results = $results->concat($users);
            }

            if ($hasRoleRead) {
                $roles = Role::query()
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($query).'%'])
                    ->limit(5)
                    ->get()
                    ->map(fn ($role) => [
                        'label' => $role->name,
                        'description' => $role->description ?? '',
                        'href' => $hasRoleEdit
                            ? route('roles.edit', $role)
                            : route('roles.index'),
                        'type' => 'role',
                    ]);

                $results = $results->concat($roles);
            }
        }

        return response()->json(['results' => $results]);
    }
}
