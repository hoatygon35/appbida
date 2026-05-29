<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\PlayTableController;
use App\Http\Controllers\Admin\DebtController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Manager\ClubController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'manager') {
        return redirect()->route('manager.clubs.index');
    } elseif ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('play-tables.index');
    }
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 1. Quản lý (Manager)
    Route::middleware('role:manager')->group(function () {
        Route::get('/manager/clubs', [ClubController::class, 'index'])->name('manager.clubs.index');
        Route::post('/manager/clubs', [ClubController::class, 'store'])->name('manager.clubs.store');
        Route::post('/manager/clubs/update', [ClubController::class, 'update'])->name('manager.clubs.update');
        Route::post('/manager/clubs/delete', [ClubController::class, 'destroy'])->name('manager.clubs.destroy');
        Route::post('/manager/clubs/extend', [ClubController::class, 'extendExpiry'])->name('manager.clubs.extend');
        Route::post('/manager/clubs/admins', [ClubController::class, 'storeAdmin'])->name('manager.clubs.admins.store');
        Route::post('/manager/clubs/admins/update', [ClubController::class, 'updateAdmin'])->name('manager.clubs.admins.update');
        Route::post('/manager/clubs/admins/delete', [ClubController::class, 'destroyAdmin'])->name('manager.clubs.admins.destroy');
    });

    // 2. Admin Chi nhánh
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/staff', [AdminDashboardController::class, 'storeStaff'])->name('admin.staff.store');
        Route::put('/admin/staff/{id}', [AdminDashboardController::class, 'updateStaff'])->name('admin.staff.update');
        Route::delete('/admin/staff/{id}', [AdminDashboardController::class, 'destroyStaff'])->name('admin.staff.destroy');
        Route::post('/admin/tables', [AdminDashboardController::class, 'storeTable'])->name('admin.tables.store');
        Route::post('/admin/tables/update', [AdminDashboardController::class, 'updateTable'])->name('admin.tables.update');
        Route::post('/admin/tables/delete', [AdminDashboardController::class, 'destroyTable'])->name('admin.tables.destroy');
        Route::post('/admin/services', [AdminDashboardController::class, 'storeService'])->name('admin.services.store');
        Route::post('/admin/services/update', [AdminDashboardController::class, 'updateService'])->name('admin.services.update');
        Route::post('/admin/services/delete', [AdminDashboardController::class, 'destroyService'])->name('admin.services.destroy');
        Route::post('/admin/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
        Route::post('/admin/notes', [AdminDashboardController::class, 'storeNote'])->name('admin.notes.store');
        Route::post('/admin/notes/delete', [AdminDashboardController::class, 'destroyNote'])->name('admin.notes.destroy');
    });

    // 3. Admin & Nhân viên (Sơ đồ bàn chơi & công nợ)
    Route::middleware('role:user,admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/play-tables', [PlayTableController::class, 'index'])->name('play-tables.index');
        Route::post('/play-tables/open', [PlayTableController::class, 'openTable'])->name('play-tables.open');
        Route::post('/play-tables/add-service', [PlayTableController::class, 'addService'])->name('play-tables.add-service');
        Route::post('/play-tables/update-service', [PlayTableController::class, 'updateServiceQuantity'])->name('play-tables.update-service');
        Route::post('/play-tables/delete-service', [PlayTableController::class, 'deleteService'])->name('play-tables.delete-service');
        Route::post('/play-tables/change-table', [PlayTableController::class, 'changeTable'])->name('play-tables.change-table');
        Route::post('/play-tables/checkout', [PlayTableController::class, 'checkout'])->name('play-tables.checkout');
        Route::post('/play-tables/sync-services', [PlayTableController::class, 'syncServices'])->name('play-tables.sync-services');
        
        // In hóa đơn
        Route::get('/invoices/{invoice}/print', function (Invoice $invoice) {
            $invoice->load('items', 'table', 'employee', 'club');
            return view('invoices.print', compact('invoice'));
        })->name('invoices.print');

        // Công nợ
        Route::get('/admin/debts', [DebtController::class, 'index'])->name('admin.debts.index');
        Route::post('/admin/debts', [DebtController::class, 'store'])->name('admin.debts.store');
        Route::post('/admin/debts/pay', [DebtController::class, 'pay'])->name('admin.debts.pay');
    });
});

require __DIR__.'/auth.php';
