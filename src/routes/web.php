<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\CustomRegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminCorrectionController;
use App\Http\Middleware\AdminMiddleware;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/register', [CustomRegisteredUserController::class, 'store'])
    ->middleware(['guest'])
    ->name('custom.register');

Route::middleware('auth')->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    Route::post('/attendance/toggleWork', [AttendanceController::class, 'toggleWork'])
        ->name('attendance.toggleWork');

    Route::post('/attendance/toggle-break', [AttendanceController::class, 'toggleBreak'])
        ->name('attendance.toggleBreak');

    Route::post('/attendance/correction', [AttendanceController::class, 'submitCorrection'])
        ->name('attendance.submitCorrection');

    Route::get('/attendance/{id}/correction/create', [AttendanceController::class, 'create'])
        ->name('correction.create');

    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'correctionsList'])
        ->name('attendance_corrections.index');

    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('attendance.index');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました。');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/attendance/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', AdminMiddleware::class])->group(function () {

        Route::get('attendance/list', [AdminAttendanceController::class, 'list'])->name('attendance.list');

        Route::get('attendance/detail', [AdminAttendanceController::class, 'detail'])->name('attendance.detail');

        Route::post('attendance/update', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        Route::get('attendance/staff/{id}', [AdminAttendanceController::class, 'staffMonthly'])->name('attendance.staff');

        Route::get('attendance/staff/{id}/csv', [AdminAttendanceController::class, 'staffMonthlyCsv'])->name('attendance.csv');

        Route::get('staff/list', [AdminStaffController::class, 'list'])->name('staff.list');

        Route::get('stamp_correction_request/list', [AdminCorrectionController::class, 'list'])
            ->name('correction.list');

        Route::get('stamp_correction_request/approve/{id}', [AdminCorrectionController::class, 'showApprove'])
            ->name('correction.approve.show');

        Route::post('stamp_correction_request/approve/{id}', [AdminCorrectionController::class, 'approve'])
            ->name('correction.approve');
    });
});