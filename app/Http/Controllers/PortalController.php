<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Models\Student;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PortalController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent
            ? $parent->students()->with('kelas')->get()
            : collect();

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('portal.index', compact('children', 'unreadCount'));
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $student->load('kelas.jurusan');

        $cases = $student->cases()
            ->with('caseType')
            ->where('status', CaseStatus::Confirmed)
            ->latest('tanggal_kejadian')
            ->paginate(15);

        return view('portal.show', compact('student', 'cases'));
    }

    public function notifications(): View
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('portal.notifications', compact('notifications'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function report(Request $request, Student $student): Response
    {
        $this->authorize('view', $student);

        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $student->load('kelas.jurusan');
        $cases = $this->reportService->getStudentCases(
            $student,
            $request->date_from,
            $request->date_to
        );

        return Pdf::loadView('reports.pdf.student', compact('student', 'cases'))
            ->download("laporan-anak-{$student->nis}.pdf");
    }
}
