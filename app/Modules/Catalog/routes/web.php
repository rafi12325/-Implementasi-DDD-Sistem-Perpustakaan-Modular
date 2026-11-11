<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Controllers\BookController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
});

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
});
