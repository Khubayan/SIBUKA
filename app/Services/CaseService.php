<?php

namespace App\Services;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Events\CaseConfirmed;
use App\Models\CaseRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CaseService
{
    public function __construct(
        protected PointService $pointService
    ) {}

    public function create(array $data, ?UploadedFile $bukti = null): CaseRecord
    {
        return DB::transaction(function () use ($data, $bukti) {
            if ($bukti) {
                $data['bukti_path'] = $bukti->store('bukti-kasus', 'public');
            }

            $data['recorded_by'] = auth()->id();
            $data['kategori'] = $this->resolveCategory($data['kategori']);
            $data['status'] = $this->resolveStatus($data['status']);
            $data['poin'] = $this->pointService->normalizePoint(
                $data['kategori'],
                (int) $data['poin']
            );

            $case = CaseRecord::create($data);
            $case->load('student');

            if ($case->status === CaseStatus::Confirmed) {
                $this->pointService->applyCasePoints($case);
                event(new CaseConfirmed($case));
            }

            return $case;
        });
    }

    public function confirm(CaseRecord $case): CaseRecord
    {
        return DB::transaction(function () use ($case) {
            $case->update(['status' => CaseStatus::Confirmed]);
            $case = $case->fresh(['student']);

            $this->pointService->applyCasePoints($case);
            event(new CaseConfirmed($case));

            return $case;
        });
    }

    public function update(CaseRecord $case, array $data, ?UploadedFile $bukti = null): CaseRecord
    {
        return DB::transaction(function () use ($case, $data, $bukti) {
            $wasConfirmed = $case->isConfirmed();

            if ($wasConfirmed) {
                $this->pointService->rollbackCasePoints($case);
            }

            if ($bukti) {
                $data['bukti_path'] = $bukti->store('bukti-kasus', 'public');
            }

            if (isset($data['kategori'])) {
                $data['kategori'] = $this->resolveCategory($data['kategori']);
            }

            if (isset($data['status'])) {
                $data['status'] = $this->resolveStatus($data['status']);
            }

            if (isset($data['poin'], $data['kategori'])) {
                $data['poin'] = $this->pointService->normalizePoint(
                    $data['kategori'],
                    (int) $data['poin']
                );
            } elseif (isset($data['poin']) && $case->kategori) {
                $data['poin'] = $this->pointService->normalizePoint(
                    $case->kategori,
                    (int) $data['poin']
                );
            }

            $case->update($data);
            $case = $case->fresh(['student']);

            $becameConfirmed = ! $wasConfirmed && $case->isConfirmed();

            if ($case->isConfirmed()) {
                $this->pointService->applyCasePoints($case);

                if ($becameConfirmed) {
                    event(new CaseConfirmed($case));
                }
            }

            return $case;
        });
    }

    public function delete(CaseRecord $case): void
    {
        DB::transaction(function () use ($case) {
            if ($case->isConfirmed()) {
                $case->loadMissing('student');
                $this->pointService->rollbackCasePoints($case);
            }

            $case->delete();
        });
    }

    protected function resolveCategory(CaseCategory|string $kategori): CaseCategory
    {
        return $kategori instanceof CaseCategory
            ? $kategori
            : CaseCategory::from($kategori);
    }

    protected function resolveStatus(CaseStatus|string $status): CaseStatus
    {
        return $status instanceof CaseStatus
            ? $status
            : CaseStatus::from($status);
    }
}
