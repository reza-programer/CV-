<?php

namespace App\Enums;

enum ExperienceType: string
{
    case Work = 'work';
    case Internship = 'internship';
    case Organization = 'organization';

    /**
     * Types the editor groups under "Magang & Organisasi".
     *
     * @return list<string>
     */
    public static function activityValues(): array
    {
        return [self::Internship->value, self::Organization->value];
    }
}
