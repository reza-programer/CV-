<?php

namespace App\Models;

use App\Enums\ExperienceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
