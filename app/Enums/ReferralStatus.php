<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case IN_SERVICE = 'in_service';
    case COMPLETED = 'completed';
    case DECLINED = 'declined';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::SENT => 'Dikirim ke Lembaga',
            self::ACCEPTED => 'Diterima Lembaga',
            self::IN_SERVICE => 'Dalam Pelayanan',
            self::COMPLETED => 'Selesai',
            self::DECLINED => 'Ditolak Lembaga',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'warning',
            self::ACCEPTED, self::IN_SERVICE => 'info',
            self::COMPLETED => 'success',
            self::DECLINED, self::CANCELLED => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::DECLINED, self::CANCELLED], true);
    }
}
