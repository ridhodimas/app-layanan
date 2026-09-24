<?php

namespace App\Enums;

enum NumberSequencePrefix: string
{
    case DTSEN = 'DTSEN';
    case PBI = 'PBI';
    case ADU = 'ADU';
    case RHS = 'RHS';
    case RJK = 'RJK';

    public function label(): string
    {
        return match ($this) {
            self::DTSEN => 'Surat Keterangan DTSEN',
            self::PBI => 'Reaktivasi KIS/PBI-JK',
            self::ADU => 'Pengaduan Sosial',
            self::RHS => 'Kasus Rehabilitasi Sosial',
            self::RJK => 'Rujukan Rehabilitasi',
        };
    }
}
