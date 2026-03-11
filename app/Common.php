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

if (! function_exists('field_error')) {
    function field_error(array $errors, string $field): ?string
    {
        return $errors[$field] ?? null;
    }
}

if (! function_exists('field_error_class')) {
    function field_error_class(array $errors, string $field): string
    {
        return isset($errors[$field]) ? 'panel-input-error' : '';
    }
}

if (! function_exists('format_indo_date')) {
    function format_indo_date(\DateTimeInterface|string|null $value, bool $withTime = false): string
    {
        if ($value === null || trim((string) $value) === '') {
            return '-';
        }

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        try {
            $timezone = new \DateTimeZone(config('App')->appTimezone ?? 'Asia/Jakarta');

            $date = $value instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($value)->setTimezone($timezone)
                : new \DateTimeImmutable((string) $value, $timezone);
        } catch (\Throwable) {
            return trim((string) $value);
        }

        $formatted = sprintf(
            '%02d %s %04d',
            (int) $date->format('d'),
            $months[(int) $date->format('n')] ?? $date->format('M'),
            (int) $date->format('Y')
        );

        if (! $withTime) {
            return $formatted;
        }

        return $formatted . ' ' . $date->format('H:i');
    }
}

if (! function_exists('loan_status_label')) {
    function loan_status_label(string $status): string
    {
        return match ($status) {
            'borrowed' => 'Dipinjam',
            'overdue' => 'Terlambat',
            'returned' => 'Dikembalikan',
            'lost' => 'Hilang',
            default => ucfirst($status),
        };
    }
}

if (! function_exists('fine_status_label')) {
    function fine_status_label(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Belum Lunas',
            'partial' => 'Cicil',
            'paid' => 'Lunas',
            'open' => 'Menunggu Penggantian',
            'resolved' => 'Selesai',
            default => ucfirst($status),
        };
    }
}

if (! function_exists('fine_type_label')) {
    function fine_type_label(string $type): string
    {
        return match ($type) {
            'late' => 'Keterlambatan',
            'damage' => 'Kerusakan Buku',
            'lost' => 'Kehilangan Buku',
            default => ucfirst($type),
        };
    }
}

if (! function_exists('loan_condition_label')) {
    function loan_condition_label(?string $condition): string
    {
        return match ($condition) {
            'good', null, '' => 'Baik',
            'damaged' => 'Rusak',
            'lost' => 'Hilang',
            default => ucfirst((string) $condition),
        };
    }
}

if (! function_exists('library_brand_name')) {
    function library_brand_name(): string
    {
        return 'Perpustakaan GKKA-I Banjarmasin';
    }
}

if (! function_exists('church_name')) {
    function church_name(): string
    {
        return 'GKKA INDONESIA Jemaat Banjarmasin';
    }
}

if (! function_exists('church_address')) {
    function church_address(): string
    {
        return 'Jl. Veteran No. 85 RT. 11, Kelurahan Melayu, Banjarmasin Tengah, Kalimantan Selatan, 70234.';
    }
}

if (! function_exists('library_logo_url')) {
    function library_logo_url(): string
    {
        return base_url('logo.png');
    }
}

if (! function_exists('library_meta_description')) {
    function library_meta_description(): string
    {
        return library_brand_name() . ' - Sistem perpustakaan digital untuk ' . church_name() . '.';
    }
}
