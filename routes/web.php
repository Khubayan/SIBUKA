<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CaseTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->isOrangTua()) {
        return redirect()->route('portal.index');
    }

    if ($user->isSiswa()) {
        return redirect()->route('siswa.index');
    }

    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::middleware('role:admin,guru_bk')->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,guru_bk,wali_kelas')
        ->name('dashboard');

    Route::middleware('role:admin,guru_bk,wali_kelas')->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('cases', [CaseController::class, 'index'])->name('cases.index');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/student/{student}', [ReportController::class, 'student'])->name('reports.student');
        Route::get('reports/class/{kelas}', [ReportController::class, 'classReport'])->name('reports.class');
    });

    // Static paths must be registered before {model} routes (e.g. /students/create).
    Route::middleware('role:admin,guru_bk')->group(function () {
        Route::get('students/template/download', [StudentController::class, 'downloadTemplate'])->name('students.template');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');

        Route::get('cases/create', [CaseController::class, 'create'])->name('cases.create');
        Route::post('cases', [CaseController::class, 'store'])->name('cases.store');

        Route::get('case-types', [CaseTypeController::class, 'index'])->name('case-types.index');

        Route::get('reports/school', [ReportController::class, 'school'])->name('reports.school');
    });

    Route::middleware('role:admin,guru_bk,wali_kelas')->group(function () {
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::get('cases/{caseRecord}', [CaseController::class, 'show'])->name('cases.show');
    });

    Route::middleware('role:admin,guru_bk')->group(function () {
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

        Route::get('cases/{caseRecord}/edit', [CaseController::class, 'edit'])->name('cases.edit');
        Route::put('cases/{caseRecord}', [CaseController::class, 'update'])->name('cases.update');
        Route::delete('cases/{caseRecord}', [CaseController::class, 'destroy'])->name('cases.destroy');
        Route::patch('cases/{caseRecord}/confirm', [CaseController::class, 'confirm'])->name('cases.confirm');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('jurusan', JurusanController::class)->except(['show']);
        Route::resource('kelas', KelasController::class)->except(['show'])->parameters(['kelas' => 'kelas']);
        Route::resource('case-types', CaseTypeController::class)->except(['index', 'show']);
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('role:orang_tua')->prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [PortalController::class, 'index'])->name('index');
        Route::get('/children/{student}', [PortalController::class, 'show'])->name('child');
        Route::get('/children/{student}/report', [PortalController::class, 'report'])->name('child.report');
        Route::get('/notifications', [PortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [PortalController::class, 'markAsRead'])->name('notifications.read');
    });

    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [StudentPortalController::class, 'index'])->name('index');
        Route::get('/biodata', [StudentPortalController::class, 'editBiodata'])->name('biodata.edit');
        Route::put('/biodata', [StudentPortalController::class, 'updateBiodata'])->name('biodata.update');
        Route::get('/pelanggaran', [StudentPortalController::class, 'violations'])->name('pelanggaran');
        Route::get('/poin', [StudentPortalController::class, 'points'])->name('poin');
    });
});
