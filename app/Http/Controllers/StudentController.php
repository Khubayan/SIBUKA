<?php

namespace App\Http\Controllers;

use App\Enums\CaseCategory;
use App\Enums\StudentStatus;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Kelas;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::query()
            ->with(['kelas.jurusan'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->when($request->kelas_id, fn ($q, $id) => $q->where('kelas_id', $id))
            ->when($request->jurusan_id, function ($q, $jurusanId) {
                $q->whereHas('kelas', fn ($q) => $q->where('jurusan_id', $jurusanId));
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when(auth()->user()->isWaliKelas(), function ($q) {
                $kelasIds = auth()->user()->kelasWali()->pluck('id');
                $q->whereIn('kelas_id', $kelasIds);
            })
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $kelasList = Kelas::with('jurusan')->orderBy('nama')->get();
        $jurusanList = \App\Models\Jurusan::orderBy('nama')->get();
        $canManage = auth()->user()->isAdmin() || auth()->user()->isGuruBk();

        return view('students.index', compact('students', 'kelasList', 'jurusanList', 'canManage'));
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);

        return view('students.create', $this->formData());
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->studentService->store($request->validated(), $request->file('foto'));

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $student->load(['kelas.jurusan', 'cases.caseType']);

        $stats = [
            'pelanggaran' => $student->cases()->where('kategori', CaseCategory::Pelanggaran)->count(),
            'prestasi' => $student->cases()->where('kategori', CaseCategory::Prestasi)->count(),
        ];

        $cases = $student->cases()->with('caseType')->latest('tanggal_kejadian')->limit(20)->get();
        $canManage = auth()->user()->can('update', $student);

        return view('students.show', compact('student', 'stats', 'cases', 'canManage'));
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);

        return view('students.edit', array_merge(['student' => $student], $this->formData()));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->studentService->update($student, $request->validated(), $request->file('foto'));

        return redirect()->route('students.show', $student)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorize('import', Student::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        try {
            $count = $this->studentService->importFromCsv($request->file('file'));

            return back()->with('success', "{$count} data siswa berhasil diimport.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadTemplate(): StreamedResponse
    {
        $this->authorize('import', Student::class);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-siswa.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'kelas', 'alamat', 'no_hp']);
            fputcsv($handle, ['2024001', '0051234567', 'I Made Example', 'L', '2008-05-15', 'XII RPL 1', 'Denpasar', '08123456789']);
            fclose($handle);
        }, Response::HTTP_OK, $headers);
    }

    private function formData(): array
    {
        return [
            'kelasList' => Kelas::with('jurusan')->orderBy('nama')->get(),
            'statusList' => StudentStatus::cases(),
        ];
    }
}
