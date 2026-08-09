<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJurusanRequest;
use App\Http\Requests\UpdateJurusanRequest;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $jurusan = Jurusan::query()
            ->withCount('kelas')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            }))
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('jurusan.index', compact('jurusan'));
    }

    public function create(): View
    {
        return view('jurusan.create');
    }

    public function store(StoreJurusanRequest $request): RedirectResponse
    {
        Jurusan::create($request->validated());

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan): View
    {
        return view('jurusan.edit', compact('jurusan'));
    }

    public function update(UpdateJurusanRequest $request, Jurusan $jurusan): RedirectResponse
    {
        $jurusan->update($request->validated());

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        if ($jurusan->kelas()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki kelas.');
        }

        $jurusan->delete();

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
