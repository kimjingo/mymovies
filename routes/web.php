<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserMediaController;
use App\Http\Controllers\MediaPoolController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [UserMediaController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('invitations', InvitationController::class)->only(['index', 'create', 'store']);

    Route::resource('user-media', UserMediaController::class);
    Route::get('/my-media/liked', [UserMediaController::class, 'liked'])->name('user-media.liked');
    Route::get('/my-media/public', [UserMediaController::class, 'myPublic'])->name('user-media.public');
    Route::post('/user-media/import', [UserMediaController::class, 'import'])->name('user-media.import');

    Route::get('/media-pool/search', [MediaPoolController::class, 'search'])->name('media-pool.search');

    Route::post('/likes/{userMedia}/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::get('/likes/{userMedia}/likers', [LikeController::class, 'likers'])->name('likes.likers');
});

require __DIR__.'/auth.php';
