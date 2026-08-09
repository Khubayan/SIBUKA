<?php

namespace App\Http\Controllers;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseRecord;
use App\Models\CaseType;
use App\Models\Kelas;
use App\Models\Student;
use App\Services\CaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function __construct(
        protected CaseService $caseService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CaseRecord::class);

        $cases = CaseRecord::query()
            ->with(['student.kelas', 'caseType', 'recorder'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhereHas('student', fn ($q) => $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%"));
                });
            })
            ->when($request->student_id, fn ($q, $id) => $q->where('student_id', $id))
            ->when($request->kelas_id, function ($q, $kelasId) {
                $q->whereHas('student', fn ($q) => $q->where('kelas_id', $kelasId));
            })
            ->when($request->kategori, fn ($q, $kat) => $q->where('kategori', $kat))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->tanggal_from, fn ($q, $date) => $q->whereDate('tanggal_kejadian', '>=', $date))
            ->when($request->tanggal_to, fn ($q, $date) => $q->whereDate('tanggal_kejadian', '<=', $date))
            ->when(auth()->user()->isWaliKelas(), function ($q) {
                $studentIds = Student::query()
                    ->whereIn('kelas_id', auth()->user()->kelasWali()->pluck('id'))
                    ->pluck('id');
                $q->whereIn('student_id', $studentIds);
            })
            ->latest('tanggal_kejadian')
            ->paginate(20)
            ->withQueryString();

        $kelasList = Kelas::with('jurusan')
            ->when(auth()->user()->isWaliKelas(), fn ($q) => $q->whereIn('id', auth()->user()->kelasWali()->pluck('id')))
            ->orderBy('nama')
            ->get();
        $students = Student::with('kelas')
            ->when(auth()->user()->isWaliKelas(), function ($q) {
                $q->whereIn('kelas_id', auth()->user()->kelasWali()->pluck('id'));
            })
            ->orderBy('nama')
            ->get();
        $canManage = auth()->user()->isAdmin() || auth()->user()->isGuruBk();

        return view('cases.index', compact('cases', 'kelasList', 'students', 'canManage'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', CaseRecord::class);

        return view('cases.create', $this->formData($request->integer('student_id') ?: null));
    }

    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $case = $this->caseService->create(
            $request->validated(),
            $request->file('bukti')
        );

        return redirect()->route('cases.show', $case)
            ->with('success', 'Kasus berhasil disimpan.');
    }

    public function show(CaseRecord $caseRecord): View
    {
        $this->authorize('view', $caseRecord);

        $caseRecord->load(['student.kelas.jurusan', 'caseType', 'recorder']);
        $canManage = auth()->user()->can('update', $caseRecord);

        return view('cases.show', ['case' => $caseRecord, 'canManage' => $canManage]);
    }

    public function edit(CaseRecord $caseRecord): View
    {
        $this->authorize('update', $caseRecord);

        return view('cases.edit', array_merge(
            ['case' => $caseRecord],
            $this->formData($caseRecord->student_id)
        ));
    }

    public function update(UpdateCaseRequest $request, CaseRecord $caseRecord): RedirectResponse
    {
        $this->caseService->update(
            $caseRecord,
            $request->validated(),
            $request->file('bukti')
        );

        return redirect()->route('cases.show', $caseRecord)
            ->with('success', 'Kasus berhasil diperbarui.');
    }

    public function destroy(CaseRecord $caseRecord): RedirectResponse
    {
        $this->authorize('delete', $caseRecord);

        $this->caseService->delete($caseRecord);

        return redirect()->route('cases.index')
            ->with('success', 'Kasus berhasil dihapus.');
    }

    public function confirm(CaseRecord $caseRecord): RedirectResponse
    {
        $this->authorize('confirm', $caseRecord);

        $this->caseService->confirm($caseRecord);

        return redirect()->route('cases.show', $caseRecord)
            ->with('success', 'Kasus berhasil dikonfirmasi. Poin siswa telah diperbarui.');
    }

    private function formData(?int $selectedStudentId = null): array
    {
        return [
            'students' => Student::with('kelas')->orderBy('nama')->get(),
            'caseTypes' => CaseType::active()->orderBy('kode')->get(),
            'categories' => CaseCategory::cases(),
            'statuses' => [CaseStatus::Draft, CaseStatus::Confirmed],
            'selectedStudentId' => $selectedStudentId,
        ];
    }
}
