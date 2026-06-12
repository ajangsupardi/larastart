<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Laravel\Facades\Image;

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
                ->with('creator')
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
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $filename = 'avatars/'.uniqid().'.webp';
            $image = Image::decodeSplFileInfo($request->file('avatar'));
            $image->cover(400, 400);
            Storage::disk('public')->put($filename, (string) $image->encodeUsingFileExtension('webp', quality: 80));
            $data['avatar'] = $filename;
        }

        $user = User::create([...$data, 'created_by' => auth()->id()]);
        $user->roles()->attach(Role::where('slug', 'user')->first());

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
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar_url,
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

    public function updateAvatar(Request $request, User $user)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $filename = 'avatars/'.uniqid().'.webp';
        $disk = Storage::disk('public');

        $image = Image::decodeSplFileInfo($request->file('avatar'));
        $image->cover(400, 400);

        $disk->put($filename, (string) $image->encodeUsingFileExtension('webp', quality: 80));

        if ($user->avatar !== null) {
            $disk->delete($user->avatar);
        }

        $user->update(['avatar' => $filename]);

        return to_route('users.index')
            ->with('success', 'Avatar updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->is_system) {
            return back()->withErrors(['error' => 'System users cannot be deleted.']);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return to_route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
