<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

const PROFILE_PATH = "/profile";

Route::get('/', static function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/', static function () {
        return view('dashboard');
    });

    Route::get(PROFILE_PATH, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch(PROFILE_PATH, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete(PROFILE_PATH, [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', static function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
});

require_once __DIR__.'/auth.php';
