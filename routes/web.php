<?php

use App\Http\Controllers\CheckInController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('fit-tracker', [CheckInController::class, 'index'])->name('fit-tracker.index');
    Route::get('fit-tracker/check-in', [CheckInController::class, 'create'])->name('check-ins.create');
    Route::post('fit-tracker/check-ins', [CheckInController::class, 'store'])->name('check-ins.store');
});

require __DIR__.'/settings.php';
