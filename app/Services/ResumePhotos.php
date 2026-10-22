<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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

    public function store(Resume $resume, UploadedFile $file): string
    {
        $image = $this->load($file);
        $image = $this->orient($image, $file);
        $jpeg = $this->cropToJpeg($image);

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

    private function load(UploadedFile $file): \GdImage
    {
        $contents = file_get_contents($file->getRealPath());
        $image = $contents === false ? false : @imagecreatefromstring($contents);

        if ($image === false) {
            throw new RuntimeException('Foto tidak dapat dibaca.');
        }

        return $image;
    }

    /**
     * Phones store portrait shots rotated with an EXIF flag; apply it before
     * the metadata is dropped.
     */
    private function orient(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data') || $file->getMimeType() !== 'image/jpeg') {
            return $image;
        }

        $orientation = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated === false ? $image : $rotated;
    }

    /**
     * Centre-crop to 3:4 and scale to the output size.
     */
    private function cropToJpeg(\GdImage $source): string
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $targetRatio = self::WIDTH / self::HEIGHT;

        if ($width / $height > $targetRatio) {
            $cropHeight = $height;
            $cropWidth = (int) round($height * $targetRatio);
            $x = (int) round(($width - $cropWidth) / 2);
            $y = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = (int) round($width / $targetRatio);
            $x = 0;
            // Keep the upper part of tall photos, where the face usually is.
            $y = (int) round(($height - $cropHeight) * 0.2);
        }

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagefill($canvas, 0, 0, (int) imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, $x, $y, self::WIDTH, self::HEIGHT, $cropWidth, $cropHeight);

        ob_start();
        imagejpeg($canvas, null, 88);

        return (string) ob_get_clean();
    }
}
