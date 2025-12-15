<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Auth routes
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Artwork routes
Route::get('/', [ArtworkController::class, 'index'])->name('artworks.index');

// ✅ UBAH INI - Untuk HTML view
Route::get('/artworks/{id}', [ArtworkController::class, 'show'])->name('artworks.show');

// ✅ TAMBAH INI - Untuk API/JSON (optional, kalau masih butuh)
Route::get('/artworks/{id}/api', [ArtworkController::class, 'view'])->name('artworks.api');

Route::post('/artworks', [ArtworkController::class, 'store'])->name('artworks.store');

Route::get('/artworks/{id}/view', [ArtworkController::class, 'view'])->name('artworks.view');
Route::put('/artworks/{id}', [ArtworkController::class, 'update'])->name('artworks.update');
Route::delete('/artworks/{id}', [ArtworkController::class, 'destroy'])->name('artworks.destroy');

// Bookmark routes
Route::get('/bookmarks', [BookmarkController::class, 'index']);
Route::post('/bookmarks', [BookmarkController::class, 'store']);
Route::delete('/bookmarks/{artworkId}', [BookmarkController::class, 'destroy']);
Route::delete('/bookmarks/clear-all', [BookmarkController::class, 'clearAll']);

// Comment routes
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('/comments/{artworkId}', [CommentController::class, 'getByArtwork'])->name('comments.by-artwork');
// Like routes
Route::post('/likes', [LikeController::class, 'store'])->name('likes.store');
Route::delete('/likes/{id}', [LikeController::class, 'destroy'])->name('likes.destroy');

// User routes
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

// Report route
Route::post('/reports', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');