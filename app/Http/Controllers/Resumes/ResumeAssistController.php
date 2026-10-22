<?php

namespace App\Http\Controllers\Resumes;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Services\ResumeAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Returns suggestions only. The editor shows them for review and the user
 * decides what to keep; saving still goes through the normal update route.
 */
class ResumeAssistController extends Controller
{
    public function __invoke(Request $request, Resume $resume, ResumeAssistant $assistant): JsonResponse
    {
        abort_unless($assistant->enabled(), 404);

        $data = $request->validate([
            'task' => ['required', Rule::in(['summary', 'highlights', 'keywords'])],
            'job_description' => ['nullable', 'string', 'max:8000', 'required_if:task,keywords'],
            'entry' => ['required_if:task,highlights', 'array'],
            'entry.position' => ['nullable', 'string', 'max:150'],
            'entry.organization' => ['nullable', 'string', 'max:150'],
            'entry.highlights' => ['required_if:task,highlights', 'array', 'min:1', 'max:12'],
            'entry.highlights.*' => ['nullable', 'string', 'max:600'],
        ], [
            'job_description.required_if' => 'Tempel deskripsi lowongan terlebih dahulu.',
            'entry.highlights.required_if' => 'Tulis minimal satu poin untuk diperbaiki.',
            'entry.highlights.min' => 'Tulis minimal satu poin untuk diperbaiki.',
        ], [
            'job_description' => 'deskripsi lowongan',
        ]);

        $jobDescription = $data['job_description'] ?? null;

        try {
            $result = match ($data['task']) {
                'summary' => $assistant->improveSummary($resume, $jobDescription),
                'highlights' => $assistant->rewriteHighlights($resume, $data['entry'], $jobDescription),
                'keywords' => $assistant->matchKeywords($resume, (string) $jobDescription),
            };
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        return response()->json($result);
    }
}
