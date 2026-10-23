<?php

namespace App\Models;

use App\Enums\ExperienceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Work, internship or organisation experience; `type` tells them apart.
 *
 * @property int $id
 * @property int $resume_id
 * @property ExperienceType $type
 * @property string|null $position
 * @property string|null $organization
 * @property string|null $location
 * @property string|null $start_date
 * @property string|null $end_date
 * @property bool $is_current
 * @property list<string>|null $highlights
 * @property int $sort_order
 */
#[Fillable(['type', 'position', 'organization', 'location', 'start_date', 'end_date', 'is_current', 'highlights', 'sort_order'])]
class ResumeExperience extends Model
{
    protected function casts(): array
    {
        return [
            'type' => ExperienceType::class,
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
