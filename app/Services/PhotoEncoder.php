<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Turns an uploaded photo into a centre-cropped JPEG of a fixed size. The
 * image is re-encoded, so EXIF metadata (GPS location, device) is dropped
 * once its rotation flag has been applied.
 */
class PhotoEncoder
{
    /**
     * @param  positive-int  $width
     * @param  positive-int  $height
     */
    public function toJpeg(UploadedFile $file, int $width, int $height): string
    {
        $image = $this->orient($this->load($file), $file);

        return $this->crop($image, $width, $height);
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

        $exif = @exif_read_data($file->getRealPath());
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated === false ? $image : $rotated;
    }

    /**
     * Centre-crop to the target ratio and scale to the output size.
     *
     * @param  positive-int  $targetWidth
     * @param  positive-int  $targetHeight
     */
    private function crop(\GdImage $source, int $targetWidth, int $targetHeight): string
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $targetRatio = $targetWidth / $targetHeight;

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

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($canvas, 0, 0, (int) imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, $x, $y, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

        ob_start();
        imagejpeg($canvas, null, 88);

        return (string) ob_get_clean();
    }
}
