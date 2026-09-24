<?php

namespace App\Enums;

enum ServiceTypeHandler: string
{
    case GENERIC = 'generic';
    case DTSEN = 'dtsen';
    case PBI = 'pbi';

    public function label(): string
    {
        return match ($this) {
            self::GENERIC => 'Layanan Umum',
            self::DTSEN => 'Surat Keterangan DTSEN',
            self::PBI => 'Reaktivasi KIS / PBI-JK',
        };
    }
}
