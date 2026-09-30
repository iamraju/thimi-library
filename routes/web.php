<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/export', [BookController::class, 'export'])->name('books.export');

    Route::middleware('role:superadmin,librarian')->group(function () {
        Route::resource('books', BookController::class)->except(['index', 'show']);
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('publishers', PublisherController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/settings.php';
