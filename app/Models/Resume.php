<?php

namespace App\Models;

use App\Enums\ExperienceType;
use App\Enums\ParagraphAlign;
use App\Enums\ResumeAccent;
use App\Enums\ResumeLocale;
use App\Services\ResumePhotos;
use Database\Factories\ResumeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title', 'locale', 'paragraph_align', 'accent_color', 'cv_template_id', 'section_order',
    'full_name', 'headline', 'email', 'phone', 'location', 'website', 'linkedin',
    'summary', 'photo_path',
])]
class Resume extends Model
{
    /** @use HasFactory<ResumeFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleted(fn (Resume $resume) => app(ResumePhotos::class)->delete($resume->photo_path));
    }

    protected function casts(): array
    {
        return [
            'locale' => ResumeLocale::class,
            'paragraph_align' => ParagraphAlign::class,
            'accent_color' => ResumeAccent::class,
            'section_order' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<CvTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CvTemplate::class, 'cv_template_id');
    }

    /**
     * Every experience row regardless of type.
     *
     * @return HasMany<ResumeExperience, $this>
     */
    public function allExperiences(): HasMany
    {
        return $this->hasMany(ResumeExperience::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ResumeExperience, $this>
     */
    public function workExperiences(): HasMany
    {
        return $this->allExperiences()->where('type', ExperienceType::Work);
    }

    /**
     * Internships and organisational roles.
     *
     * @return HasMany<ResumeExperience, $this>
     */
    public function activities(): HasMany
    {
        return $this->allExperiences()->whereIn('type', ExperienceType::activityValues());
    }

    /**
     * @return HasMany<ResumeEducation, $this>
     */
    public function educations(): HasMany
    {
        return $this->hasMany(ResumeEducation::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ResumeProject, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(ResumeProject::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ResumeSkillGroup, $this>
     */
    public function skillGroups(): HasMany
    {
        return $this->hasMany(ResumeSkillGroup::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ResumeCertification, $this>
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(ResumeCertification::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ResumeLanguage, $this>
     */
    public function languages(): HasMany
    {
        return $this->hasMany(ResumeLanguage::class)->orderBy('sort_order');
    }

    /**
     * Relations needed to build the full document payload.
     *
     * @return list<string>
     */
    public static function contentRelations(): array
    {
        return ['template', 'workExperiences', 'activities', 'educations', 'projects', 'skillGroups', 'certifications', 'languages'];
    }
}
