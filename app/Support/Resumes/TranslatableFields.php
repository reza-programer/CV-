<?php

namespace App\Support\Resumes;

/**
 * Picks the free text of a CV payload that changes with the document
 * language. Proper nouns stay as the user wrote them: names, employers,
 * institutions, project and certification names, locations, links, dates
 * and grades are never sent for translation.
 */
class TranslatableFields
{
    /**
     * Translatable keys of each list entry; "highlights" holds bullet points.
     *
     * @var array<string, list<string>>
     */
    private const LISTS = [
        'experiences' => ['position', 'highlights'],
        'activities' => ['position', 'highlights'],
        'educations' => ['degree', 'field_of_study', 'highlights'],
        'projects' => ['role', 'highlights'],
        'skill_groups' => ['category', 'skills'],
        'languages' => ['name', 'proficiency'],
    ];

    /**
     * @param  array<string, mixed>  $payload  Output of ResumePayload::make()
     * @return array<string, string> Dot path => text, in document order (e.g. "experiences.0.highlights.1").
     */
    public static function extract(array $payload): array
    {
        $fields = [];

        self::add($fields, 'personal.headline', $payload['personal']['headline'] ?? null);
        self::add($fields, 'summary', $payload['summary'] ?? null);

        foreach (self::LISTS as $list => $keys) {
            foreach (self::items($payload[$list] ?? null) as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                foreach ($keys as $key) {
                    if ($key !== 'highlights') {
                        self::add($fields, "{$list}.{$index}.{$key}", $item[$key] ?? null);

                        continue;
                    }

                    foreach (self::items($item['highlights'] ?? null) as $line => $text) {
                        self::add($fields, "{$list}.{$index}.highlights.{$line}", $text);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, string>  $fields
     */
    private static function add(array &$fields, string $path, mixed $value): void
    {
        if (is_string($value) && trim($value) !== '') {
            $fields[$path] = $value;
        }
    }

    /**
     * @return list<mixed>
     */
    private static function items(mixed $value): array
    {
        return is_array($value) ? array_values($value) : [];
    }
}
