<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case RECEIVED = 'received';
    case VERIFICATION = 'verification';
    case CLARIFICATION_REQUESTED = 'clarification_requested';
    case DISPATCHED = 'dispatched';
    case IN_HANDLING = 'in_handling';
    case RESOLVED = 'resolved';
    case DUPLICATE = 'duplicate';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Laporan Diterima',
            self::VERIFICATION => 'Verifikasi Awal',
            self::CLARIFICATION_REQUESTED => 'Klarifikasi Diminta',
            self::DISPATCHED => 'Didisposisikan',
            self::IN_HANDLING => 'Dalam Penanganan',
            self::RESOLVED => 'Selesai / Ditangani',
            self::DUPLICATE => 'Duplikat',
            self::INVALID => 'Tidak Valid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RECEIVED => 'gray',
            self::VERIFICATION => 'info',
            self::CLARIFICATION_REQUESTED => 'warning',
            self::DISPATCHED => 'indigo',
            self::IN_HANDLING => 'purple',
            self::RESOLVED => 'success',
            self::DUPLICATE => 'amber',
            self::INVALID => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::RESOLVED, self::DUPLICATE, self::INVALID], true);
    }
}
