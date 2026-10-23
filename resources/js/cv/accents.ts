import type { AccentColor } from './types';

/**
 * Accent palette for the coloured templates (App\Enums\ResumeAccent).
 * Every shade keeps at least 7:1 contrast with white, so it works both as
 * heading text on paper and as a band behind white text, and still reads as
 * a dark grey when printed in black and white.
 */
export const ACCENTS: Record<AccentColor, { label: string; hex: string }> = {
    navy: { label: 'Biru dongker', hex: '#1e3a5f' },
    teal: { label: 'Hijau toska', hex: '#0f5e63' },
    forest: { label: 'Hijau hutan', hex: '#2f5d3a' },
    burgundy: { label: 'Merah marun', hex: '#7a1f2b' },
    plum: { label: 'Ungu', hex: '#4b3b7a' },
    charcoal: { label: 'Abu arang', hex: '#374151' },
};

export const ACCENT_KEYS = Object.keys(ACCENTS) as AccentColor[];

export function accentHex(key: string): string {
    return (
        (ACCENTS as Record<string, { hex: string }>)[key]?.hex ??
        ACCENTS.navy.hex
    );
}
