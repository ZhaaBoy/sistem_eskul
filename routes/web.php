<?php

use App\Http\Controllers\Admin\CoachController as AdminCoachController;
use App\Http\Controllers\Admin\ExtracurricularController as AdminExtracurricularController;
use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\UserAccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Coach\AssessmentController as CoachAssessmentController;
use App\Http\Controllers\Coach\AttendanceController as CoachAttendanceController;
use App\Http\Controllers\Coach\RegistrationController as CoachRegistrationController;
use App\Http\Controllers\Coach\ReportController as CoachReportController;
use App\Http\Controllers\Coach\ScheduleController as CoachScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guardian\RegistrationController as ParentRegistrationController;
use App\Http\Controllers\Guardian\ReportController as ParentReportController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\ExtracurricularController as StudentExtracurricularController;
use App\Http\Controllers\Student\ReportController as StudentReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::post('/accounts/{account}/toggle-status', [UserAccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
        Route::post('/accounts/{account}/reset-password', [UserAccountController::class, 'resetPassword'])->name('accounts.reset-password');
        Route::resource('accounts', UserAccountController::class)->except(['show', 'destroy']);

        Route::resource('students', AdminStudentController::class)->except(['show']);
        Route::resource('parents', AdminParentController::class)->except(['show']);
        Route::resource('coaches', AdminCoachController::class)->except(['show']);
        Route::resource('extracurriculars', AdminExtracurricularController::class)->except(['show']);
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    });

    Route::prefix('siswa')->name('student.')->middleware('role:siswa')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
        Route::get('/eskul', [StudentExtracurricularController::class, 'index'])->name('extracurriculars.index');
        Route::get('/eskul/{extracurricular}', [StudentExtracurricularController::class, 'show'])->name('extracurriculars.show');
        Route::post('/eskul/{extracurricular}/daftar', [StudentExtracurricularController::class, 'register'])->name('extracurriculars.register');
        Route::get('/absensi', [StudentAttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/absensi/{schedule}', [StudentAttendanceController::class, 'store'])->name('attendances.store');
        Route::get('/laporan', [StudentReportController::class, 'index'])->name('reports.index');
    });

    Route::prefix('orang-tua')->name('parent.')->middleware('role:orang_tua')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'parent'])->name('dashboard');
        Route::get('/validasi-pendaftaran', [ParentRegistrationController::class, 'index'])->name('registrations.index');
        Route::post('/validasi-pendaftaran/{registration}/approve', [ParentRegistrationController::class, 'approve'])->name('registrations.approve');
        Route::post('/validasi-pendaftaran/{registration}/reject', [ParentRegistrationController::class, 'reject'])->name('registrations.reject');
        Route::get('/laporan-anak', [ParentReportController::class, 'index'])->name('reports.index');
    });

    Route::prefix('pembina')->name('coach.')->middleware('role:pembina')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'coach'])->name('dashboard');
        Route::get('/validasi-pendaftaran', [CoachRegistrationController::class, 'index'])->name('registrations.index');
        Route::post('/validasi-pendaftaran/{registration}/approve', [CoachRegistrationController::class, 'approve'])->name('registrations.approve');
        Route::post('/validasi-pendaftaran/{registration}/reject', [CoachRegistrationController::class, 'reject'])->name('registrations.reject');
        Route::resource('schedules', CoachScheduleController::class)->except(['show']);
        Route::get('/absensi', [CoachAttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/absensi/{attendance}/approve', [CoachAttendanceController::class, 'approve'])->name('attendances.approve');
        Route::post('/absensi/{attendance}/reject', [CoachAttendanceController::class, 'reject'])->name('attendances.reject');
        Route::resource('assessments', CoachAssessmentController::class)->except(['show']);
        Route::get('/laporan', [CoachReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/cetak', [CoachReportController::class, 'print'])->name('reports.print');
    });
});
