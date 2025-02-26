<?php

// routes/web.php

use App\Http\Controllers\TestScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

// Rute Export dan Import
use App\Exports\UsersExport;
use App\Exports\TestSchedulesExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Group routes with middleware jwt
Route::middleware(['jwt.auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {

        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');



    });

    Route::middleware(['role:admin,engineer'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/fetch-notifications', [NotificationController::class, 'fetchNotifications'])->name('fetch.notifications');
        Route::get('/count-notifications', [NotificationController::class, 'countNotifications'])->name('count.notifications');
        Route::get('/delete-all-notifications', [NotificationController::class, 'deleteAllNotifications'])->name('delete.notifications');


        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/test_schedules/create', [TestScheduleController::class, 'create'])->name('test_schedules.create');
        Route::post('/test_schedules', [TestScheduleController::class, 'store'])->name('test_schedules.store');
        Route::resource('test_schedules', TestScheduleController::class);
        Route::get('/test-schedules', [TestScheduleController::class, 'index'])->name('test_schedules.index');
        Route::post('/test_schedules/{id}', [TestScheduleController::class, 'update'])->name('test_schedules.update');    
        Route::get('test_schedules/{id}', [TestScheduleController::class, 'show'])->name('test_schedules.show');
        Route::get('export-test-schedules', function () {
            return Excel::download(new TestSchedulesExport, 'test_schedules.xlsx');
        })->name('test_schedules.export');
        Route::get('/schedule_archive/export-pdf', [ArchiveController::class, 'exportPDF'])->name('schedule_archive.export_pdf');


        Route::get('/schedule_archive', [ArchiveController::class, 'index'])->name('schedule_archive.index');


        Route::get('/schedule_archive', [ArchiveController::class, 'index'])->name('schedule_archive.index');
    });
});
