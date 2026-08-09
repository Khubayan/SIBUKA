<?php

namespace App\Http\Controllers;

use App\Enums\CaseCategory;
use App\Http\Requests\StoreCaseTypeRequest;
use App\Http\Requests\UpdateCaseTypeRequest;
use App\Models\CaseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseTypeController extends Controller
{
    public function index(Request $request): View
    {
        $caseTypes = CaseType::query()
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            }))
            ->when($request->kategori, fn ($q, $kat) => $q->where('kategori', $kat))
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        $canManage = auth()->user()->isAdmin();

        return view('case-types.index', compact('caseTypes', 'canManage'));
    }

    public function create(): View
    {
        return view('case-types.create', [
            'categories' => CaseCategory::cases(),
        ]);
    }

    public function store(StoreCaseTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        CaseType::create($data);

        return redirect()->route('case-types.index')
            ->with('success', 'Jenis kasus berhasil ditambahkan.');
    }

    public function edit(CaseType $caseType): View
    {
        return view('case-types.edit', [
            'caseType' => $caseType,
            'categories' => CaseCategory::cases(),
        ]);
    }

    public function update(UpdateCaseTypeRequest $request, CaseType $caseType): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $caseType->update($data);

        return redirect()->route('case-types.index')
            ->with('success', 'Jenis kasus berhasil diperbarui.');
    }

    public function destroy(CaseType $caseType): RedirectResponse
    {
        if ($caseType->cases()->exists()) {
            return back()->with('error', 'Jenis kasus tidak dapat dihapus karena sudah digunakan.');
        }

        $caseType->delete();

        return redirect()->route('case-types.index')
            ->with('success', 'Jenis kasus berhasil dihapus.');
    }
}
