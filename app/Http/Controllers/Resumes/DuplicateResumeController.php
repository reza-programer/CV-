<?php

namespace App\Http\Controllers\Resumes;

use App\Actions\Resumes\DuplicateResume;
use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class DuplicateResumeController extends Controller
{
    public function __invoke(Resume $resume, DuplicateResume $duplicateResume): RedirectResponse
    {
        $copy = $duplicateResume->handle($resume);

        Inertia::flash('toast', ['type' => 'success', 'message' => "CV diduplikasi sebagai \"{$copy->title}\"."]);

        return to_route('dashboard');
    }
}
