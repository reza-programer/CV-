<?php

namespace App\Models;

use App\Enums\ActivityEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One thing a user did, shown on the admin dashboard. Meta holds technical
 * details only (template, language, assistant task), never CV content.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $resume_id
 * @property ActivityEvent $event
 * @property array<string, string>|null $meta
 * @property Carbon $created_at
 */
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /** Autosave runs every few seconds; one "edited" entry per resume per window is enough. */
    public const EDIT_WINDOW_MINUTES = 30;

    protected $fillable = ['user_id', 'resume_id', 'event', 'meta'];

    protected function casts(): array
    {
        return [
            'event' => ActivityEvent::class,
            'meta' => 'array',
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
     * @return BelongsTo<Resume, $this>
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    /**
     * @param  array<string, string>  $meta
     */
    public static function record(User $user, ActivityEvent $event, ?Resume $resume = null, array $meta = []): self
    {
        if ($resume !== null && ! isset($meta['template'])) {
            $meta['template'] = $resume->template->slug ?? '';
        }

        return static::query()->create([
            'user_id' => $user->id,
            'resume_id' => $resume?->id,
            'event' => $event,
            'meta' => array_filter($meta, fn ($value) => $value !== '') ?: null,
        ]);
    }

    /** @var array<string, string>|null */
    private static ?array $templateNames = null;

    /** Log number as shown in the admin, e.g. "000123". */
    public function code(): string
    {
        return sprintf('%06d', $this->id);
    }

    /** The CV the action was on, e.g. "CV-00031"; null after the CV is deleted. */
    public function reference(): ?string
    {
        return $this->resume_id === null ? null : sprintf('CV-%05d', $this->resume_id);
    }

    public function templateName(): ?string
    {
        $slug = $this->meta['template'] ?? null;

        if ($slug === null) {
            return null;
        }

        self::$templateNames ??= CvTemplate::query()->pluck('name', 'slug')->all();

        return self::$templateNames[$slug] ?? $slug;
    }

    /** Document language or assistant task, when the action has one. */
    public function note(): ?string
    {
        $meta = $this->meta ?? [];

        if (isset($meta['task'])) {
            return match ($meta['task']) {
                'summary' => 'Profil singkat',
                'highlights' => 'Poin pengalaman',
                'keywords' => 'Kata kunci lowongan',
                default => $meta['task'],
            };
        }

        if (isset($meta['locale'])) {
            return $meta['locale'] === 'en' ? 'English' : 'Bahasa Indonesia';
        }

        return null;
    }

    /**
     * Everything above in one line, e.g. "CV-00012 · Professional ATS · English".
     */
    public function detail(): string
    {
        $parts = array_filter([$this->reference(), $this->templateName(), $this->note()]);

        return $parts === [] ? '—' : implode(' · ', $parts);
    }

    /**
     * Records an edit unless the same resume was already marked as edited
     * within the edit window.
     */
    public static function recordEdit(User $user, Resume $resume): void
    {
        $recent = static::query()
            ->where('resume_id', $resume->id)
            ->where('event', ActivityEvent::ResumeEdited)
            ->where('created_at', '>=', now()->subMinutes(self::EDIT_WINDOW_MINUTES))
            ->exists();

        if (! $recent) {
            static::record($user, ActivityEvent::ResumeEdited, $resume);
        }
    }
}
