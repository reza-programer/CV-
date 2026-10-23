<?php

namespace App\Http\Controllers\Resumes;

use App\Enums\ActivityEvent;
use App\Enums\ResumeLocale;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Services\ResumeTranslator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Returns translation suggestions only. The editor lists them for review and
 * editing; they reach the CV through the normal autosave once applied.
 */
class ResumeTranslateController extends Controller
{
    public function __invoke(Request $request, Resume $resume, ResumeTranslator $translator): JsonResponse
    {
        abort_unless($translator->enabled(), 404);

        $data = $request->validate([
            'target' => ['required', Rule::in(array_column(ResumeLocale::cases(), 'value'))],
        ], [
            'target.required' => 'Pilih bahasa tujuan terjemahan.',
            'target.in' => 'Bahasa tujuan harus Bahasa Indonesia atau English.',
        ]);

        try {
            $items = $translator->translate($resume, ResumeLocale::from($data['target']));
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        ActivityLog::record($request->user(), ActivityEvent::Translated, $resume, ['locale' => $data['target']]);

        return response()->json(['items' => $items]);
    }
}
