<?php

namespace App\Listeners;

use App\Actions\Resumes\CreateResume;
use App\Actions\Resumes\SaveResumeContent;
use App\Enums\ActivityEvent;
use App\Enums\ResumeLocale;
use App\Http\Controllers\Resumes\GuestResumeController;
use App\Models\ActivityLog;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Saves the CV a visitor typed in the try-out editor to the account they just
 * created or signed in to, then sends them back to it. Sign-up logs the new
 * user in, so this one listener covers both.
 */
class ClaimGuestResume
{
    public function __construct(
        private Request $request,
        private CreateResume $createResume,
        private SaveResumeContent $saveContent,
    ) {}

    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || ! $this->request->hasSession()) {
            return;
        }

        $draft = $this->request->session()->pull(GuestResumeController::SESSION_KEY);

        if (! is_array($draft)) {
            return;
        }

        // Fields left empty in the draft fall back to the account, like a new CV.
        $draft['personal']['full_name'] ??= $user->name;
        $draft['personal']['email'] ??= $user->email;

        $template = CvTemplate::query()->where('slug', $draft['template'] ?? null)->first()
            ?? CvTemplate::query()->where('slug', CvTemplate::DEFAULT_SLUG)->firstOrFail();

        $resume = $this->createResume->handle(
            $user,
            $draft['title'] ?? 'CV Baru',
            $template,
            ResumeLocale::tryFrom($draft['locale'] ?? '') ?? ResumeLocale::Indonesian,
        );
        $resume = $this->saveContent->handle($resume, $draft);

        ActivityLog::record($user, ActivityEvent::ResumeCreated, $resume);

        $this->request->session()->put('url.intended', route('resumes.edit', $resume));
        Inertia::flash('toast', ['type' => 'success', 'message' => 'CV dari mode coba sudah tersimpan di akun Anda.']);
    }
}
