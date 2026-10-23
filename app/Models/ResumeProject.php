<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $resume_id
 * @property string|null $name
 * @property string|null $role
 * @property string|null $url
 * @property string|null $start_date
 * @property string|null $end_date
 * @property bool $is_current
 * @property list<string>|null $highlights
 * @property int $sort_order
 */
#[Fillable(['name', 'role', 'url', 'start_date', 'end_date', 'is_current', 'highlights', 'sort_order'])]
class ResumeProject extends Model
{
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
