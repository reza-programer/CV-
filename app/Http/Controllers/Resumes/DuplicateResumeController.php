<?php

namespace App\Http\Controllers\Resumes;

use App\Actions\Resumes\DuplicateResume;
use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DuplicateResumeController extends Controller
{
    public function __invoke(Request $request, Resume $resume, DuplicateResume $duplicateResume): RedirectResponse
    {
        $copy = $duplicateResume->handle($resume);

        ActivityLog::record($request->user(), ActivityEvent::ResumeDuplicated, $copy);

        Inertia::flash('toast', ['type' => 'success', 'message' => "CV diduplikasi sebagai \"{$copy->title}\"."]);

        return to_route('dashboard');
    }
}
