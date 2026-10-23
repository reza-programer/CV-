<?php

namespace App\Enums;

/**
 * Accent colours for the coloured templates. Stored by name; the hex values
 * live in resources/js/cv/accents.ts. Every shade is dark enough for white
 * text on it and for coloured headings on white paper.
 */
enum ResumeAccent: string
{
    case Navy = 'navy';
    case Teal = 'teal';
    case Forest = 'forest';
    case Burgundy = 'burgundy';
    case Plum = 'plum';
    case Charcoal = 'charcoal';
}
