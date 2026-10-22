<?php

namespace App\Enums;

/**
 * Language of the generated document (section headings, month names,
 * "Present"/"Sekarang"). Independent of the application UI language.
 */
enum ResumeLocale: string
{
    case Indonesian = 'id';
    case English = 'en';
}
