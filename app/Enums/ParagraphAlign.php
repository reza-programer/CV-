<?php

namespace App\Enums;

/**
 * Alignment of the CV's running text: the profile paragraph and bullet
 * points. Headings, dates and lists keep their template alignment.
 */
enum ParagraphAlign: string
{
    case Left = 'left';
    case Justify = 'justify';
}
