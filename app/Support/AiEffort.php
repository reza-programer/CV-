<?php

namespace App\Support;

/**
 * The model effort from config, limited to the values the API accepts.
 * Anything unexpected falls back to "low", the cheapest and quickest.
 */
final class AiEffort
{
    /**
     * @return 'low'|'medium'|'high'|'xhigh'|'max'
     */
    public static function configured(): string
    {
        return match (config('cvforge.ai.effort')) {
            'medium' => 'medium',
            'high' => 'high',
            'xhigh' => 'xhigh',
            'max' => 'max',
            default => 'low',
        };
    }
}
