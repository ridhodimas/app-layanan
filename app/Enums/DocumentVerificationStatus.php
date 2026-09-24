<?php

namespace App\Enums;

enum DocumentVerificationStatus: string
{
    case PENDING = 'pending';
    case VALID = 'valid';
    case REVISION_NEEDED = 'revision_needed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VALID => 'Valid / Lengkap',
            self::REVISION_NEEDED => 'Perlu Perbaikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::VALID => 'success',
            self::REVISION_NEEDED => 'warning',
        };
    }
}
