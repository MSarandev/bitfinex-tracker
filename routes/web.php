<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/', static function () {
        return view('dashboard');
    });

    Route::get("/profile", [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch("/profile", [ProfileController::class, 'update'])->name('profile.update');
    Route::delete("/profile", [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', static function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
});

require_once __DIR__.'/auth.php';
