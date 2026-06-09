<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $search = request('search');

        $users = UserResource::collection(
            User::query()
                ->when(! auth()->user()->hasPermission('users', 'read'), fn ($query) => $query
                    ->where(function ($q) {
                        $q->where('created_by', auth()->id())
                            ->orWhere('id', auth()->id());
                    }))
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->orderBy('created_at', 'desc')
                ->with('roles')
                ->paginate(10)
                ->withQueryString()
        );

        $hasReadPermission = auth()->user()->hasPermission('users', 'read');

        $statsQuery = User::query();
        if (! $hasReadPermission) {
            $statsQuery->where(function ($q) {
                $q->where('created_by', auth()->id())
                    ->orWhere('id', auth()->id());
            });
        }

        $total = $statsQuery->count();
        $verified = (clone $statsQuery)->whereNotNull('email_verified_at')->count();
        $unverified = $total - $verified;

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => request()->only('search'),
            'stats' => [
                'total' => ['label' => 'Total Users', 'value' => $total, 'trend' => 0],
                'verified' => ['label' => 'Verified', 'value' => $verified, 'trend' => 0],
                'unverified' => ['label' => 'Unverified', 'value' => $unverified, 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request)
    {
        User::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (! isset($data['password']) || blank($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return to_route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return to_route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
