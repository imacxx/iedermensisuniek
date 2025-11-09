<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!(admin|filament|api|sanctum|up|storage)).*$');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Editor routes
    Route::get('/editor/{slug}', function ($slug) {
        return view('editor', compact('slug'));
    })->name('editor');
});

require __DIR__.'/auth.php';
