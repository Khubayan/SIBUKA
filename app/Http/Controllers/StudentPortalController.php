<?php

namespace App\Http\Controllers;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Http\Requests\UpdateStudentBiodataRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(): View
    {
        $student = $this->resolveStudent();

        $student->load('kelas.jurusan');

        $recentPelanggaran = $student->cases()
            ->with('caseType')
            ->where('status', CaseStatus::Confirmed)
            ->where('kategori', CaseCategory::Pelanggaran)
            ->latest('tanggal_kejadian')
            ->limit(5)
            ->get();

        $thresholds = config('sibuka.point_thresholds');

        return view('siswa.index', compact('student', 'recentPelanggaran', 'thresholds'));
    }

    public function editBiodata(): View
    {
        $student = $this->resolveStudent();
        $student->load('kelas.jurusan');

        return view('siswa.biodata', compact('student'));
    }

    public function updateBiodata(UpdateStudentBiodataRequest $request): RedirectResponse
    {
        $student = $this->resolveStudent();

        $this->studentService->updateBiodata(
            $student,
            $request->validated(),
            $request->file('foto')
        );

        return redirect()->route('siswa.biodata.edit')
            ->with('success', 'Biodata berhasil diperbarui.');
    }

    public function violations(): View
    {
        $student = $this->resolveStudent();

        $cases = $student->cases()
            ->with('caseType')
            ->where('status', CaseStatus::Confirmed)
            ->where('kategori', CaseCategory::Pelanggaran)
            ->latest('tanggal_kejadian')
            ->paginate(15);

        return view('siswa.pelanggaran', compact('student', 'cases'));
    }

    public function points(): View
    {
        $student = $this->resolveStudent();
        $student->load('kelas.jurusan');

        $pointLogs = $student->pointLogs()
            ->with('caseRecord')
            ->latest('created_at')
            ->limit(20)
            ->get();

        $thresholds = config('sibuka.point_thresholds');

        $prestasiTotal = $student->cases()
            ->where('status', CaseStatus::Confirmed)
            ->where('kategori', CaseCategory::Prestasi)
            ->sum('poin');

        $pelanggaranTotal = $student->cases()
            ->where('status', CaseStatus::Confirmed)
            ->where('kategori', CaseCategory::Pelanggaran)
            ->sum('poin');

        return view('siswa.poin', compact(
            'student',
            'pointLogs',
            'thresholds',
            'prestasiTotal',
            'pelanggaranTotal'
        ));
    }

    private function resolveStudent(): Student
    {
        $student = auth()->user()->studentProfile;

        if (! $student) {
            abort(403, 'Akun Anda belum terhubung ke data siswa. Hubungi administrator.');
        }

        return $student;
    }
}
