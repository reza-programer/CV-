<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $resume_id
 * @property string|null $institution
 * @property string|null $degree
 * @property string|null $field_of_study
 * @property string|null $location
 * @property string|null $start_date
 * @property string|null $end_date
 * @property bool $is_current
 * @property string|null $gpa
 * @property list<string>|null $highlights
 * @property int $sort_order
 */
#[Fillable(['institution', 'degree', 'field_of_study', 'location', 'start_date', 'end_date', 'is_current', 'gpa', 'highlights', 'sort_order'])]
class ResumeEducation extends Model
{
    /**
     * "education" is uncountable to the pluralizer, so name the table explicitly.
     */
    protected $table = 'resume_educations';

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'highlights' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Resume, $this>
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
