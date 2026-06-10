<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('Profile Avatar Upload', function () {
    beforeEach(function () {
        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can upload an avatar', function () {
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertRedirect(route('profile.edit'));

        $this->user->refresh();

        expect($this->user->avatar)->not->toBeNull();
        expect($this->user->avatar)->toMatch('/^avatars\/.+\.webp$/');

        Storage::disk('public')->assertExists($this->user->avatar);
    });

    it('rejects files larger than 5MB', function () {
        $file = UploadedFile::fake()->image('avatar.jpg')->size(6000);

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertSessionHasErrors('avatar');
    });

    it('rejects non-image files', function () {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertSessionHasErrors('avatar');
    });

    it('rejects invalid image types', function () {
        $file = UploadedFile::fake()->image('avatar.gif');

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertSessionHasErrors('avatar');
    });

    it('requires authentication', function () {
        auth()->logout();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertRedirect(route('login'));
    });

    it('resizes avatar to 400x400 and converts to webp', function () {
        $file = UploadedFile::fake()->image('avatar.jpg', 800, 600);

        $this->post(route('profile.avatar'), [
            'avatar' => $file,
        ])->assertRedirect(route('profile.edit'));

        $this->user->refresh();

        $path = Storage::disk('public')->path($this->user->avatar);
        [$width, $height] = getimagesize($path);

        expect($width)->toBe(400);
        expect($height)->toBe(400);
        expect(mime_content_type($path))->toBe('image/webp');
    });

    it('deletes old avatar when uploading new one', function () {
        $oldFile = UploadedFile::fake()->image('old.jpg', 200, 200);
        $this->post(route('profile.avatar'), ['avatar' => $oldFile]);

        $this->user->refresh();
        $oldAvatar = $this->user->avatar;

        $newFile = UploadedFile::fake()->image('new.jpg', 200, 200);
        $this->post(route('profile.avatar'), ['avatar' => $newFile]);

        $this->user->refresh();
        expect($this->user->avatar)->not->toBe($oldAvatar);

        Storage::disk('public')->assertMissing($oldAvatar);
    });
});
