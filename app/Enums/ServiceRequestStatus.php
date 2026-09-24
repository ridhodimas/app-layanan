<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case SUBMITTED = 'submitted';
    case DOCUMENT_CHECK = 'document_check';
    case REVISION_REQUESTED = 'revision_requested';
    case DATA_VERIFICATION = 'data_verification';
    case ELIGIBILITY_VERIFICATION = 'eligibility_verification';
    case VERIFICATION = 'verification';
    case ASSESSMENT = 'assessment';
    case AWAITING_APPROVAL = 'awaiting_approval';
    case RECOMMENDATION_ISSUED = 'recommendation_issued';
    case PROPOSED_TO_MINISTRY = 'proposed_to_ministry';
    case MINISTRY_APPROVED = 'ministry_approved';
    case MINISTRY_REJECTED = 'ministry_rejected';
    case REACTIVATED = 'reactivated';
    case ISSUED = 'issued';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Diajukan',
            self::DOCUMENT_CHECK => 'Pemeriksaan Berkas',
            self::REVISION_REQUESTED => 'Perlu Perbaikan',
            self::DATA_VERIFICATION => 'Verifikasi Data',
            self::ELIGIBILITY_VERIFICATION => 'Verifikasi Kelayakan',
            self::VERIFICATION => 'Verifikasi',
            self::ASSESSMENT => 'Assessment',
            self::AWAITING_APPROVAL => 'Menunggu Persetujuan',
            self::RECOMMENDATION_ISSUED => 'Rekomendasi Diterbitkan',
            self::PROPOSED_TO_MINISTRY => 'Diusulkan ke Kemensos',
            self::MINISTRY_APPROVED => 'Disetujui Kemensos',
            self::MINISTRY_REJECTED => 'Ditolak Kemensos',
            self::REACTIVATED => 'Telah Diaktifkan',
            self::ISSUED => 'Diterbitkan',
            self::IN_PROCESS => 'Dalam Proses',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'gray',
            self::DOCUMENT_CHECK, self::VERIFICATION, self::DATA_VERIFICATION, self::ELIGIBILITY_VERIFICATION => 'info',
            self::REVISION_REQUESTED => 'warning',
            self::ASSESSMENT, self::IN_PROCESS => 'purple',
            self::AWAITING_APPROVAL, self::PROPOSED_TO_MINISTRY => 'amber',
            self::RECOMMENDATION_ISSUED, self::MINISTRY_APPROVED, self::ISSUED, self::REACTIVATED => 'primary',
            self::COMPLETED => 'success',
            self::REJECTED, self::MINISTRY_REJECTED => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::REJECTED, self::MINISTRY_REJECTED], true);
    }
}
