<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user()->load('roles');

        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar_url,
                'roles' => $user->roles->pluck('name'),
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        if (isset($data['password']) && ! blank($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return to_route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $user = $request->user();

        $filename = 'avatars/'.uniqid().'.webp';

        $disk = Storage::disk('public');

        $image = Image::decodeSplFileInfo($request->file('avatar'));
        $image->cover(400, 400);

        $disk->put($filename, (string) $image->encodeUsingFileExtension('webp', quality: 80));

        if ($user->avatar !== null) {
            $disk->delete($user->avatar);
        }

        $user->update(['avatar' => $filename]);

        return to_route('profile.edit')
            ->with('success', 'Avatar updated successfully.');
    }
}
