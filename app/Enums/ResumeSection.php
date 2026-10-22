<?php

namespace App\Enums;

/**
 * Output sections of a CV, in the default reading order. The editor lets the
 * user reorder these; the stored order is always normalised to contain every
 * case exactly once.
 */
enum ResumeSection: string
{
    case Summary = 'summary';
    case Experience = 'experience';
    case Internship = 'internship';
    case Organization = 'organization';
    case Education = 'education';
    case Projects = 'projects';
    case Skills = 'skills';
    case Certifications = 'certifications';
    case Languages = 'languages';

    /**
     * @return list<string>
     */
    public static function defaultOrder(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Keep known keys in the given order, drop unknown or duplicate keys and
     * append any missing sections at the end.
     *
     * @param  array<mixed>|null  $order
     * @return list<string>
     */
    public static function normalizeOrder(?array $order): array
    {
        $known = self::defaultOrder();
        $result = [];

        foreach ($order ?? [] as $key) {
            if (is_string($key) && in_array($key, $known, true) && ! in_array($key, $result, true)) {
                $result[] = $key;
            }
        }

        foreach ($known as $key) {
            if (! in_array($key, $result, true)) {
                $result[] = $key;
            }
        }

        return $result;
    }
}
