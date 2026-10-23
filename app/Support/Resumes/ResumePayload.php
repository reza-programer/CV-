<?php

namespace App\Support\Resumes;

use App\Enums\ParagraphAlign;
use App\Enums\ResumeAccent;
use App\Enums\ResumeSection;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Models\ResumeCertification;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\ResumeLanguage;
use App\Models\ResumeProject;
use App\Models\ResumeSkillGroup;

/**
 * Builds the document payload shared by the editor, the live preview and the
 * PDF renderer. The shape mirrors `ResumeData` in resources/js/cv/types.ts and
 * the validation rules in UpdateResumeRequest, so a payload can round-trip
 * through the editor unchanged.
 */
class ResumePayload
{
    /**
     * @return array<string, mixed>
     */
    public static function make(Resume $resume, ?string $photoUrl): array
    {
        $resume->loadMissing(Resume::contentRelations());

        return [
            'id' => $resume->id,
            'title' => $resume->title,
            'template' => $resume->template->slug ?? CvTemplate::DEFAULT_SLUG,
            'locale' => $resume->locale->value,
            'paragraph_align' => ($resume->paragraph_align ?? ParagraphAlign::Left)->value,
            'accent_color' => ($resume->accent_color ?? ResumeAccent::Navy)->value,
            'section_order' => ResumeSection::normalizeOrder($resume->section_order),
            'personal' => [
                'full_name' => (string) $resume->full_name,
                'headline' => (string) $resume->headline,
                'email' => (string) $resume->email,
                'phone' => (string) $resume->phone,
                'location' => (string) $resume->location,
                'website' => (string) $resume->website,
                'linkedin' => (string) $resume->linkedin,
            ],
            'photo_url' => $photoUrl,
            'summary' => (string) $resume->summary,
            'experiences' => $resume->workExperiences->map(fn (ResumeExperience $item) => self::experience($item))->all(),
            'activities' => $resume->activities->map(fn (ResumeExperience $item) => [
                'type' => $item->type->value,
                ...self::experience($item),
            ])->all(),
            'educations' => $resume->educations->map(fn (ResumeEducation $item) => [
                'institution' => (string) $item->institution,
                'degree' => (string) $item->degree,
                'field_of_study' => (string) $item->field_of_study,
                'location' => (string) $item->location,
                'start_date' => (string) $item->start_date,
                'end_date' => (string) $item->end_date,
                'is_current' => $item->is_current,
                'gpa' => (string) $item->gpa,
                'highlights' => self::highlights($item->highlights),
            ])->all(),
            'projects' => $resume->projects->map(fn (ResumeProject $item) => [
                'name' => (string) $item->name,
                'role' => (string) $item->role,
                'url' => (string) $item->url,
                'start_date' => (string) $item->start_date,
                'end_date' => (string) $item->end_date,
                'is_current' => $item->is_current,
                'highlights' => self::highlights($item->highlights),
            ])->all(),
            'skill_groups' => $resume->skillGroups->map(fn (ResumeSkillGroup $item) => [
                'category' => (string) $item->category,
                'skills' => (string) $item->skills,
            ])->all(),
            'certifications' => $resume->certifications->map(fn (ResumeCertification $item) => [
                'name' => (string) $item->name,
                'issuer' => (string) $item->issuer,
                'issued_at' => (string) $item->issued_at,
                'expires_at' => (string) $item->expires_at,
                'credential_id' => (string) $item->credential_id,
                'url' => (string) $item->url,
            ])->all(),
            'languages' => $resume->languages->map(fn (ResumeLanguage $item) => [
                'name' => (string) $item->name,
                'proficiency' => (string) $item->proficiency,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function experience(ResumeExperience $item): array
    {
        return [
            'position' => (string) $item->position,
            'organization' => (string) $item->organization,
            'location' => (string) $item->location,
            'start_date' => (string) $item->start_date,
            'end_date' => (string) $item->end_date,
            'is_current' => $item->is_current,
            'highlights' => self::highlights($item->highlights),
        ];
    }

    /**
     * @param  array<mixed>|null  $highlights
     * @return list<string>
     */
    private static function highlights(?array $highlights): array
    {
        return array_values(array_map('strval', array_filter($highlights ?? [], fn ($line) => is_string($line) && $line !== '')));
    }
}
