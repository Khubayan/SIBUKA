<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $this->syncRoleProfile($user, $request);
        });

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load(['teacher', 'parentProfile.students', 'studentProfile']);

        return view('users.edit', array_merge(['user' => $user], $this->formData($user)));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->boolean('is_active', true),
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $user->update($data);

            $this->syncRoleProfile($user, $request);
        });

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dinonaktifkan.');
    }

    private function syncRoleProfile(User $user, Request $request): void
    {
        if ($user->role === UserRole::WaliKelas) {
            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                ['nama' => $user->name, 'nip' => $request->nip]
            );
        }

        if ($user->role === UserRole::OrangTua) {
            $parent = ParentModel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'no_hp' => $request->no_hp,
                    'hubungan' => $request->hubungan ?? 'wali',
                ]
            );

            if ($request->has('student_ids')) {
                $parent->students()->sync($request->student_ids ?? []);
            }
        }

        if ($user->role === UserRole::Siswa) {
            Student::where('user_id', $user->id)->update(['user_id' => null]);

            if ($request->student_id) {
                Student::where('id', $request->student_id)->update(['user_id' => $user->id]);
            }
        } else {
            Student::where('user_id', $user->id)->update(['user_id' => null]);
        }
    }

    private function formData(?User $user = null): array
    {
        return [
            'roles' => UserRole::cases(),
            'students' => Student::with('kelas')->orderBy('nama')->get(),
            'selectedStudentIds' => $user?->parentProfile?->students->pluck('id')->toArray() ?? [],
            'selectedStudentId' => $user?->studentProfile?->id,
        ];
    }
}
