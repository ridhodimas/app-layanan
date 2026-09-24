<?php

namespace App\Enums;

enum ReferralInstitutionType: string
{
    case PANTI = 'panti';
    case BALAI = 'balai';
    case RS = 'RS';
    case LKS = 'LKS';

    public function label(): string
    {
        return match ($this) {
            self::PANTI => 'Panti Sosial',
            self::BALAI => 'Balai Rehabilitasi',
            self::RS => 'Rumah Sakit / Faskes',
            self::LKS => 'Lembaga Kesejahteraan Sosial (LKS)',
        };
    }
}
