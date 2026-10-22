<?php

namespace App\Actions\Resumes;

use App\Enums\ResumeLocale;
use App\Enums\ResumeSection;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Models\User;

class CreateResume
{
    /**
     * Start a CV from the account's own name and email; every other field is
     * left empty for the user to fill in.
     */
    public function handle(User $user, string $title, CvTemplate $template, ResumeLocale $locale): Resume
    {
        return $user->resumes()->create([
            'title' => $title,
            'cv_template_id' => $template->id,
            'locale' => $locale,
            'section_order' => ResumeSection::defaultOrder(),
            'full_name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
