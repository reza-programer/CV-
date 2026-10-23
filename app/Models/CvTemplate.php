<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'description', 'is_active', 'sort_order'])]
class CvTemplate extends Model
{
    /**
     * Slugs that have a layout implementation in resources/js/cv/templates.
     */
    public const SLUGS = [
        'classic', 'compact', 'modern', 'professional', 'executive', 'accent', 'banner', 'block',
        'portrait', 'timeline', 'gutter', 'minimal', 'sidebar', 'sidebar-light', 'two-column', 'split',
        'monogram', 'numbered', 'leader', 'bold', 'ribbon', 'corporate', 'tabbed', 'elegant', 'magazine', 'folio',
    ];

    public const DEFAULT_SLUG = 'classic';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<Resume, $this>
     */
    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }

    /**
     * @param  Builder<CvTemplate>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return array{slug: string, name: string, description: string|null}
     */
    public function toPublicArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
