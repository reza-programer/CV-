<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'description', 'is_active', 'is_recommended', 'sort_order'])]
class CvTemplate extends Model
{
    /**
     * Slugs that have a layout implementation in resources/js/cv/templates.
     */
    public const SLUGS = [
        'classic', 'compact', 'modern', 'professional', 'executive', 'accent', 'banner', 'block',
        'portrait', 'timeline', 'gutter', 'minimal', 'sidebar', 'sidebar-light', 'two-column', 'split',
        'monogram', 'numbered', 'leader', 'bold', 'ribbon', 'corporate', 'tabbed', 'elegant', 'magazine', 'folio',
        'lumen', 'heritage', 'skyline', 'prestige', 'wave', 'geo', 'orbit', 'fresh', 'contrast', 'label',
        'noir', 'slate', 'pastel', 'atelier', 'frame',
        // Catalogue templates assembled from style traits (templates/kit).
        'clean', 'simple', 'formal', 'academic', 'crisp', 'nordic', 'office', 'ledger', 'concise',
        'serif', 'swiss', 'outline', 'bar', 'centered', 'graphite', 'sided', 'tidy', 'plain',
        'azure', 'horizon', 'stripe', 'corner', 'sunrise', 'ink', 'vista', 'marker', 'emblem',
        'pulse', 'crest', 'harbor', 'bloom', 'spark', 'linear', 'metro', 'canvas', 'aurora',
        'sage', 'royal', 'zenith', 'ember', 'coral', 'tide', 'mint', 'ridge',
        'harmony', 'summit', 'balance', 'ivory', 'duo', 'cobalt', 'terra', 'mosaic',
        'crystal', 'quartz', 'sierra', 'pixel', 'lotus', 'vertex', 'echo',
    ];

    public const DEFAULT_SLUG = 'classic';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
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
     * @return array{slug: string, name: string, description: string|null, recommended: bool}
     */
    public function toPublicArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'recommended' => (bool) $this->is_recommended,
        ];
    }
}
