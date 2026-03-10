<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('rupiah')) {
    function rupiah(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

if (! function_exists('person_initials')) {
    function person_initials(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return '--';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : '--';
    }
}
