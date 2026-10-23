<?php

namespace App\Http\Controllers\Resumes;

use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Services\ResumePhotos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumePhotoController extends Controller
{
    public function __construct(private ResumePhotos $photos) {}

    /**
     * Versioned URL so the preview refreshes when the photo is replaced.
     */
    public static function urlFor(Resume $resume): ?string
    {
        if ($resume->photo_path === null) {
            return null;
        }

        return route('resumes.photo.show', [
            'resume' => $resume,
            'v' => substr(sha1($resume->photo_path), 0, 10),
        ], absolute: false);
    }

    public function show(Resume $resume): StreamedResponse
    {
        abort_if($resume->photo_path === null || ! $this->photos->disk()->exists($resume->photo_path), 404);

        return $this->photos->disk()->response($resume->photo_path, null, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=31536000, immutable',
        ]);
    }

    public function store(Request $request, Resume $resume): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=200,min_height=200,max_width=8000,max_height=8000'],
        ], [
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'photo.dimensions' => 'Foto minimal 200 × 200 piksel.',
        ], [
            'photo' => 'foto',
        ]);

        try {
            $this->photos->store($resume, $request->file('photo'));
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages(['photo' => $exception->getMessage()]);
        }

        ActivityLog::record($request->user(), ActivityEvent::PhotoUploaded, $resume);

        return response()->json([
            'photo_url' => self::urlFor($resume->refresh()),
        ]);
    }

    public function destroy(Resume $resume): JsonResponse
    {
        $this->photos->remove($resume);

        return response()->json(['photo_url' => null]);
    }
}
