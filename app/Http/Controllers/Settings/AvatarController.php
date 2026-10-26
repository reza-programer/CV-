<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\UserAvatars;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The signed-in user's own account photo. There is no route to anyone
 * else's, so the photo is never visible to other users.
 */
class AvatarController extends Controller
{
    public function __construct(private UserAvatars $avatars) {}

    public function show(Request $request): StreamedResponse
    {
        $path = $request->user()->avatar_path;
        abort_if($path === null || ! $this->avatars->disk()->exists($path), 404);

        return $this->avatars->disk()->response($path, null, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=31536000, immutable',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=100,min_height=100,max_width=8000,max_height=8000'],
        ], [
            'avatar.max' => 'Ukuran foto maksimal 5 MB.',
            'avatar.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'avatar.dimensions' => 'Foto minimal 100 × 100 piksel.',
        ], [
            'avatar' => 'foto',
        ]);

        try {
            $this->avatars->store($request->user(), $request->file('avatar'));
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['avatar' => $exception->getMessage()]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Foto profil diperbarui.']);

        return to_route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->avatars->remove($request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Foto profil dihapus.']);

        return to_route('profile.edit');
    }
}
