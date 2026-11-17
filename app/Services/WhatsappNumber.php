<?php

namespace App\Services;

use Closure;

class WhatsappNumber
{
    public static function validationRule(): Closure
    {
        return static function ($attribute, $value, $fail): void {
            if (! is_string($value) || ! self::normalize($value)) {
                $fail('Masukkan nomor WhatsApp Indonesia yang valid, misalnya 081234567890.');
            }
        };
    }

    public static function normalize(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', trim($number ?? ''));

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return preg_match('/^628[0-9]{7,12}$/D', $digits) === 1 ? $digits : null;
    }

    public static function chatUrl(?string $number, string $message): ?string
    {
        $normalized = self::normalize($number);

        if (! $normalized) {
            return null;
        }

        return 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }
}
