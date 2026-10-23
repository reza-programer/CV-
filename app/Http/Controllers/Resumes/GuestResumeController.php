<?php

namespace App\Http\Controllers\Resumes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resumes\SaveGuestResumeRequest;
use App\Models\CvTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The try-out editor for visitors without an account. Their CV is kept in the
 * session only; ClaimGuestResume turns it into a real CV once they sign up or
 * log in, so nothing typed here has to be typed again.
 */
class GuestResumeController extends Controller
{
    public const SESSION_KEY = 'guest_resume';

    public function show(Request $request): Response
    {
        $templates = CvTemplate::query()->active()->get();

        return Inertia::render('resumes/Try', [
            'draft' => $request->session()->get(self::SESSION_KEY),
            'templates' => array_values($templates->map->toPublicArray()->all()),
            // A template picked in the landing page gallery.
            'template' => $templates->firstWhere('slug', $request->string('template')->value())?->slug,
        ]);
    }

    public function update(SaveGuestResumeRequest $request): JsonResponse
    {
        $request->session()->put(self::SESSION_KEY, $request->validated());

        return response()->json([
            'saved_at' => now()->toIso8601String(),
        ]);
    }
}
