<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $kelas = Kelas::query()
            ->with(['jurusan', 'waliKelas'])
            ->withCount('students')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('tahun_ajaran', 'like', "%{$search}%");
            }))
            ->when($request->jurusan_id, fn ($q, $id) => $q->where('jurusan_id', $id))
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $jurusanList = Jurusan::orderBy('kode')->get();

        return view('kelas.index', compact('kelas', 'jurusanList'));
    }

    public function create(): View
    {
        return view('kelas.create', $this->formData());
    }

    public function store(StoreKelasRequest $request): RedirectResponse
    {
        Kelas::create($request->validated());

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        return view('kelas.edit', array_merge(['kelas' => $kelas], $this->formData()));
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->students()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
        }

        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'jurusanList' => Jurusan::orderBy('kode')->get(),
            'waliKelasList' => User::where('role', UserRole::WaliKelas)->where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
