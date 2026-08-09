<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Policies\ReportPolicy;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        abort_unless(app(ReportPolicy::class)->viewAny($request->user()), 403);

        $user = $request->user();
        $canSchoolReport = app(ReportPolicy::class)->school($user);

        $students = Student::with('kelas')
            ->when($user->isWaliKelas(), function ($q) use ($user) {
                $kelasIds = $user->kelasWali()->pluck('id');
                $q->whereIn('kelas_id', $kelasIds);
            })
            ->orderBy('nama')
            ->get();

        $kelasList = Kelas::with('jurusan')
            ->when($user->isWaliKelas(), function ($q) use ($user) {
                $q->whereIn('id', $user->kelasWali()->pluck('id'));
            })
            ->orderBy('nama')
            ->get();

        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('reports.index', compact('students', 'kelasList', 'jurusanList', 'canSchoolReport'));
    }

    public function student(Request $request, Student $student): Response|StreamedResponse
    {
        abort_unless(app(ReportPolicy::class)->student($request->user(), $student), 403);

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'format' => ['required', 'in:pdf,csv'],
        ]);

        $student->load('kelas.jurusan');
        $cases = $this->reportService->getStudentCases(
            $student,
            $request->date_from,
            $request->date_to
        );

        if ($request->format === 'csv') {
            return $this->reportService->streamStudentCsv($student, $cases);
        }

        return Pdf::loadView('reports.pdf.student', compact('student', 'cases'))
            ->download("laporan-siswa-{$student->nis}.pdf");
    }

    public function classReport(Request $request, Kelas $kelas): Response|StreamedResponse
    {
        abort_unless(app(ReportPolicy::class)->classReport($request->user(), $kelas), 403);

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'kategori' => ['nullable', 'in:pelanggaran,prestasi'],
            'format' => ['required', 'in:pdf,csv'],
        ]);

        $kelas->load('jurusan');
        $cases = $this->reportService->getClassCases(
            $kelas,
            $request->date_from,
            $request->date_to,
            $request->kategori
        );

        if ($request->format === 'csv') {
            return $this->reportService->streamClassCsv($kelas, $cases);
        }

        return Pdf::loadView('reports.pdf.class', compact('kelas', 'cases'))
            ->download('laporan-kelas-'.str_replace(' ', '-', strtolower($kelas->nama)).'.pdf');
    }

    public function school(Request $request): Response|StreamedResponse
    {
        abort_unless(app(ReportPolicy::class)->school($request->user()), 403);

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'jurusan_id' => ['nullable', 'exists:jurusan,id'],
            'tingkat' => ['nullable', 'in:X,XI,XII'],
            'kategori' => ['nullable', 'in:pelanggaran,prestasi'],
            'format' => ['required', 'in:pdf,csv'],
        ]);

        $cases = $this->reportService->getSchoolCases(
            $request->date_from,
            $request->date_to,
            $request->integer('jurusan_id') ?: null,
            $request->tingkat,
            $request->kategori
        );

        if ($request->format === 'csv') {
            return $this->reportService->streamSchoolCsv($cases);
        }

        return Pdf::loadView('reports.pdf.school', compact('cases'))
            ->download('laporan-sekolah-'.now()->format('Y-m-d').'.pdf');
    }
}
