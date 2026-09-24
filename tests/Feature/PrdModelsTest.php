<?php

namespace Tests\Feature;

use App\Enums\ComplaintStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\MinistryDecision;
use App\Enums\NumberSequencePrefix;
use App\Enums\PbiReason;
use App\Enums\PublishStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Enums\ReferralStatus;
use App\Enums\ServiceRequestStatus;
use App\Enums\ServiceTypeHandler;
use App\Models\Approval;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\District;
use App\Models\DownloadableForm;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\PageVisit;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\SearchLog;
use App\Models\ServiceRequirement;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrdModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_prd_models_and_enums_can_be_created_and_queried(): void
    {
        // 1. WorkUnit, District, Village
        $workUnit = WorkUnit::create(['name' => 'Bidang Perlindungan dan Jaminan Sosial']);
        $this->assertDatabaseHas('work_units', ['name' => 'Bidang Perlindungan dan Jaminan Sosial']);

        $district = District::create(['code' => '35.05.01', 'name' => 'Kanigoro']);
        $village = Village::create([
            'district_id' => $district->id,
            'code' => '35.05.01.2001',
            'name' => 'Kanigoro',
        ]);
        $this->assertEquals($district->id, $village->district->id);

        // 2. User
        $user = User::create([
            'name' => 'Petugas Pelayanan',
            'email' => 'petugas@blitar.go.id',
            'password' => 'secret123',
            'phone' => '081234567890',
            'nik' => '3505010101900001',
            'work_unit_id' => $workUnit->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
            'is_active' => true,
        ]);
        $this->assertEquals($workUnit->id, $user->workUnit->id);
        $this->assertEquals($district->id, $user->district->id);
        $this->assertEquals($village->id, $user->village->id);

        // 3. ServiceType & Requirements
        $serviceType = ServiceType::create([
            'code' => 'DTSEN',
            'name' => 'Surat Keterangan DTSEN',
            'category' => 'Bansos',
            'description' => 'Penerbitan SK DTSEN',
            'handler' => ServiceTypeHandler::DTSEN,
            'needs_assessment' => false,
            'sla_days' => 3,
            'is_active' => true,
        ]);
        $requirement = ServiceRequirement::create([
            'service_type_id' => $serviceType->id,
            'name' => 'KTP Pemohon',
            'is_mandatory' => true,
            'allowed_mimes' => 'pdf,jpg,png',
            'sort_order' => 1,
        ]);
        $this->assertCount(1, $serviceType->requirements);

        // 4. NumberSequence
        $requestNumber = NumberSequence::nextFormattedNumber(NumberSequencePrefix::DTSEN);
        $this->assertStringStartsWith('DTSEN-', $requestNumber);

        // 5. ServiceRequest
        $serviceRequest = ServiceRequest::create([
            'request_number' => $requestNumber,
            'service_type_id' => $serviceType->id,
            'submitter_id' => $user->id,
            'applicant_name' => 'Budi Santoso',
            'applicant_nik' => '3505010101900001',
            'family_card_number' => '3505010101900002',
            'address' => 'Jl. Merdeka No. 1',
            'village_id' => $village->id,
            'phone' => '081234567890',
            'submitted_at' => now(),
            'officer_id' => $user->id,
            'work_unit_id' => $workUnit->id,
            'status' => ServiceRequestStatus::SUBMITTED,
            'is_priority' => false,
        ]);
        $this->assertEquals(ServiceRequestStatus::SUBMITTED, $serviceRequest->status);

        // 6. ServiceRequestDocument
        $document = ServiceRequestDocument::create([
            'service_request_id' => $serviceRequest->id,
            'service_requirement_id' => $requirement->id,
            'file_path' => 'documents/ktp.pdf',
            'original_name' => 'ktp.pdf',
            'verification_status' => DocumentVerificationStatus::VALID,
        ]);
        $this->assertEquals(DocumentVerificationStatus::VALID, $document->verification_status);

        // 7. DtsenPurpose & DtsenCertificate
        $purpose = DtsenPurpose::create([
            'code' => 'spmb',
            'name' => 'SPMB Jalur Afirmasi',
            'max_decile' => 5,
            'validity_days' => 30,
            'is_active' => true,
        ]);
        $certificate = DtsenCertificate::create([
            'service_request_id' => $serviceRequest->id,
            'dtsen_purpose_id' => $purpose->id,
            'purpose_description' => 'Untuk syarat pendaftaran sekolah',
            'subject_name' => 'Anak Budi',
            'subject_nik' => '3505010101150001',
            'relationship_to_applicant' => 'Anak Kandung',
            'is_registered' => true,
            'decile' => 2,
            'checked_at' => now(),
            'checker_id' => $user->id,
            'certificate_number' => '400.9/001/409.105/2026',
            'issued_at' => now(),
            'valid_until' => now()->addDays(30),
            'signer_id' => $user->id,
            'file_path' => 'certificates/cert-001.pdf',
            'verification_code' => 'DTSEN-ABC123XYZ',
        ]);
        $this->assertEquals($serviceRequest->id, $certificate->serviceRequest->id);

        // 8. Approvals (morph)
        $approval = Approval::create([
            'approvable_type' => DtsenCertificate::class,
            'approvable_id' => $certificate->id,
            'step' => 1,
            'approver_id' => $user->id,
            'decision' => 'approved',
            'notes' => 'Disetujui dan diparaf',
            'decided_at' => now(),
        ]);
        $this->assertCount(1, $certificate->approvals);

        // 9. ComplaintCategory & Complaint
        $complaintCategory = ComplaintCategory::create(['name' => 'Lansia Terlantar']);
        $complaintNumber = NumberSequence::nextFormattedNumber(NumberSequencePrefix::ADU);
        $complaint = Complaint::create([
            'complaint_number' => $complaintNumber,
            'complaint_category_id' => $complaintCategory->id,
            'reporter_id' => $user->id,
            'reporter_name' => 'Warga Pelapor',
            'reporter_phone' => '08987654321',
            'location_detail' => 'Dusun Krajan RT 01 RW 02',
            'village_id' => $village->id,
            'description' => 'Ada lansia terlantar membutuhkan penanganan',
            'reported_at' => now(),
            'officer_id' => $user->id,
            'status' => ComplaintStatus::RECEIVED,
        ]);
        $this->assertEquals(ComplaintStatus::RECEIVED, $complaint->status);

        // 10. Rehabilitation Clients, Case, Assessment, Referral, Monitoring
        $clientCategory = ClientCategory::create(['name' => 'Lansia']);
        $client = Client::create([
            'name' => 'Mbah Sutini',
            'client_category_id' => $clientCategory->id,
            'nik' => '3505015001500001',
            'birth_date' => '1950-01-01',
            'gender' => 'P',
            'address' => 'Dusun Krajan',
            'village_id' => $village->id,
        ]);

        $caseNumber = NumberSequence::nextFormattedNumber(NumberSequencePrefix::RHS);
        $case = RehabilitationCase::create([
            'case_number' => $caseNumber,
            'client_id' => $client->id,
            'complaint_id' => $complaint->id,
            'officer_id' => $user->id,
            'handling_type' => RehabilitationHandlingType::REFERRAL,
            'status' => RehabilitationCaseStatus::RECEIVED,
            'received_at' => now(),
        ]);

        $assessment = Assessment::create([
            'rehabilitation_case_id' => $case->id,
            'officer_id' => $user->id,
            'assessment_date' => now()->toDateString(),
            'result' => 'Kondisi fisik lemah tanpa keluarga',
            'service_needs' => 'Perawatan panti lansia',
            'recommendation' => 'Rujuk ke panti werda',
            'needs_referral' => true,
        ]);

        $institution = ReferralInstitution::create([
            'name' => 'Panti Werdha Blitar',
            'type' => 'panti',
            'address' => 'Jl. Veteran Blitar',
            'contact' => '0342-123456',
        ]);

        $referralNumber = NumberSequence::nextFormattedNumber(NumberSequencePrefix::RJK);
        $referral = Referral::create([
            'referral_number' => $referralNumber,
            'rehabilitation_case_id' => $case->id,
            'assessment_id' => $assessment->id,
            'referral_institution_id' => $institution->id,
            'officer_id' => $user->id,
            'referral_date' => now()->toDateString(),
            'status' => ReferralStatus::DRAFT,
        ]);

        $monitoring = MonitoringRecord::create([
            'rehabilitation_case_id' => $case->id,
            'referral_id' => $referral->id,
            'officer_id' => $user->id,
            'monitoring_date' => now()->toDateString(),
            'progress' => 'Klien telah diterima dengan baik di panti',
        ]);
        $this->assertEquals($case->id, $monitoring->rehabilitationCase->id);

        // 11. Status History & Disposition (polymorphic)
        $statusHistory = StatusHistory::create([
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequest->id,
            'from_status' => null,
            'to_status' => ServiceRequestStatus::SUBMITTED->value,
            'notes' => 'Pengajuan berhasil dibuat oleh pemohon',
            'user_id' => $user->id,
        ]);
        $this->assertCount(1, $serviceRequest->statusHistories);

        $disposition = Disposition::create([
            'dispositionable_type' => ServiceRequest::class,
            'dispositionable_id' => $serviceRequest->id,
            'from_user_id' => $user->id,
            'to_work_unit_id' => $workUnit->id,
            'to_user_id' => $user->id,
            'instructions' => 'Segera verifikasi data di SIKS-NG',
            'disposed_at' => now(),
        ]);
        $this->assertCount(1, $serviceRequest->dispositions);

        // 12. Information Pages, FAQs, Forms, Visits, SearchLog
        $infoPage = InformationPage::create([
            'title' => 'Panduan Layanan DTSEN',
            'slug' => 'panduan-layanan-dtsen',
            'category' => 'program',
            'service_type_id' => $serviceType->id,
            'description' => 'Informasi pengurusan SK DTSEN',
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
            'manager_id' => $user->id,
        ]);

        $form = DownloadableForm::create([
            'information_page_id' => $infoPage->id,
            'name' => 'Formulir Permohonan',
            'file_path' => 'forms/f-01.pdf',
            'version' => '1.0',
            'is_current' => true,
        ]);

        $faq = Faq::create([
            'information_page_id' => $infoPage->id,
            'question' => 'Berapa lama proses penerbitan?',
            'answer' => 'Maksimal 3 hari kerja setelah berkas lengkap.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $visit = PageVisit::create([
            'information_page_id' => $infoPage->id,
            'visit_date' => now()->toDateString(),
            'visit_count' => 10,
        ]);

        $searchLog = SearchLog::create([
            'keyword' => 'DTSEN',
            'result_count' => 5,
            'searched_at' => now(),
        ]);

        $this->assertDatabaseHas('information_pages', ['slug' => 'panduan-layanan-dtsen']);
        $this->assertDatabaseHas('downloadable_forms', ['version' => '1.0']);
        $this->assertDatabaseHas('faqs', ['sort_order' => 1]);
        $this->assertDatabaseHas('page_visits', ['visit_count' => 10]);
        $this->assertDatabaseHas('search_logs', ['keyword' => 'DTSEN']);
    }
}
