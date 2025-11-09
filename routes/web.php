<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerKostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/kosts', [KostController::class, 'index'])->name('kosts.index');
Route::get('/kost/{kost}', [KostController::class, 'show'])->name('kosts.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/owner/dashboard', OwnerDashboardController::class)->name('owner.dashboard');

    Route::post('owner/kosts/bulk-action', [OwnerKostController::class, 'bulkAction'])->name('owner.kosts.bulk');

    Route::resource('owner/kosts', OwnerKostController::class)
        ->names('owner.kosts')
        ->except(['show']);

    Route::get('/owner/bookings', [BookingController::class, 'ownerIndex'])->name('owner.bookings.index');
    Route::post('/owner/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('owner.bookings.status');
    Route::get('/owner/bookings/export', [BookingController::class, 'export'])->name('owner.bookings.export');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'tenantIndex'])->name('bookings.index');
    Route::post('/kost/{kost}/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

Route::fallback(function () {
    $acceptsHtml = str_contains(request()->header('accept', ''), 'text/html');

    if ($acceptsHtml && ! request()->expectsJson()) {
        session()->flash('error', 'Halaman tidak ditemukan.');

        return response()->view('errors.404', [], 404);
    }

    return response()->json(['message' => 'Not Found'], 404);
});
