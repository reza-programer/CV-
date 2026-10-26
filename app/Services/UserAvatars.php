<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Account photos shown in the user menu. Like CV photos they stay on the
 * private disk, are served only to their owner, and are re-encoded on upload.
 */
class UserAvatars
{
    /** Square, sharp enough for the profile page on a high-density screen. */
    private const SIZE = 320;

    public function __construct(private PhotoEncoder $encoder) {}

    /**
     * Versioned URL so the menu refreshes when the photo is replaced.
     */
    public static function urlFor(User $user): ?string
    {
        if ($user->avatar_path === null) {
            return null;
        }

        return route('avatar.show', ['v' => substr(sha1($user->avatar_path), 0, 10)], absolute: false);
    }

    public function store(User $user, UploadedFile $file): string
    {
        $jpeg = $this->encoder->toJpeg($file, self::SIZE, self::SIZE);

        $path = sprintf('avatars/%d/%s.jpg', $user->id, Str::random(32));
        $this->disk()->put($path, $jpeg);

        $this->delete($user->avatar_path);
        $user->forceFill(['avatar_path' => $path])->save();

        return $path;
    }

    public function remove(User $user): void
    {
        $this->delete($user->avatar_path);
        $user->forceFill(['avatar_path' => null])->save();
    }

    public function delete(?string $path): void
    {
        if ($path !== null && $this->disk()->exists($path)) {
            $this->disk()->delete($path);
        }
    }

    public function disk(): Filesystem
    {
        return Storage::disk('local');
    }
}
