<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Profile photos are personal data: they live on the private disk, are only
 * served through an authorised route, and are re-encoded on upload so EXIF
 * metadata (GPS location, device) never ends up in a CV.
 */
class ResumePhotos
{
    /**
     * Output size in pixels. 3:4 portrait matches the frame every template
     * reserves for the photo (pas foto 3x4), at roughly 300 dpi.
     */
    private const WIDTH = 450;

    private const HEIGHT = 600;

    public function __construct(private PhotoEncoder $encoder) {}

    public function store(Resume $resume, UploadedFile $file): string
    {
        $jpeg = $this->encoder->toJpeg($file, self::WIDTH, self::HEIGHT);

        $path = sprintf('resume-photos/%d/%s.jpg', $resume->id, Str::random(32));
        $this->disk()->put($path, $jpeg);

        $this->delete($resume->photo_path);
        $resume->update(['photo_path' => $path]);

        return $path;
    }

    public function remove(Resume $resume): void
    {
        $this->delete($resume->photo_path);
        $resume->update(['photo_path' => null]);
    }

    public function copy(string $path, Resume $target): ?string
    {
        if (! $this->disk()->exists($path)) {
            return null;
        }

        $newPath = sprintf('resume-photos/%d/%s.jpg', $target->id, Str::random(32));
        $this->disk()->copy($path, $newPath);

        return $newPath;
    }

    public function delete(?string $path): void
    {
        if ($path !== null && $this->disk()->exists($path)) {
            $this->disk()->delete($path);
        }
    }

    /**
     * Inline the photo for the PDF renderer, which loads the document from a
     * temporary file and cannot reach authenticated routes.
     */
    public function dataUri(Resume $resume): ?string
    {
        if ($resume->photo_path === null || ! $this->disk()->exists($resume->photo_path)) {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode((string) $this->disk()->get($resume->photo_path));
    }

    public function disk(): Filesystem
    {
        return Storage::disk('local');
    }
}
