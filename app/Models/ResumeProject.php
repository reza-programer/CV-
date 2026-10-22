<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
