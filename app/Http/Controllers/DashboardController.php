<?php

namespace App\Http\Controllers;

use App\Enums\CaseCategory;
use App\Models\CaseRecord;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $studentQuery = $this->scopedStudentQuery();
        $caseQuery = $this->scopedCaseQuery();

        $stats = [
            'total_siswa' => (clone $studentQuery)->active()->count(),
            'pelanggaran_bulan_ini' => (clone $caseQuery)
                ->where('kategori', CaseCategory::Pelanggaran)
                ->whereMonth('tanggal_kejadian', now()->month)
                ->whereYear('tanggal_kejadian', now()->year)
                ->count(),
            'prestasi_bulan_ini' => (clone $caseQuery)
                ->where('kategori', CaseCategory::Prestasi)
                ->whereMonth('tanggal_kejadian', now()->month)
                ->whereYear('tanggal_kejadian', now()->year)
                ->count(),
            'perlu_perhatian' => (clone $studentQuery)
                ->where('total_poin', '<=', config('sibuka.point_thresholds.warning'))
                ->count(),
        ];

        $kasusTerbaru = (clone $caseQuery)
            ->with(['student', 'caseType'])
            ->latest()
            ->limit(10)
            ->get();

        $trenBulanan = $this->reportService->monthlyTrend($caseQuery);
        $isWaliKelas = $user->isWaliKelas();

        return view('dashboard.index', compact('stats', 'kasusTerbaru', 'trenBulanan', 'isWaliKelas'));
    }

    protected function scopedStudentQuery()
    {
        $query = Student::query();

        if (auth()->user()->isWaliKelas()) {
            $kelasIds = auth()->user()->kelasWali()->pluck('id');
            $query->whereIn('kelas_id', $kelasIds);
        }

        return $query;
    }

    protected function scopedCaseQuery()
    {
        $query = CaseRecord::query();

        if (auth()->user()->isWaliKelas()) {
            $studentIds = $this->scopedStudentQuery()->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }

        return $query;
    }
}
