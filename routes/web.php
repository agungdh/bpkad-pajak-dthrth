<?php

use App\Http\Controllers\SkpdController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth'])->group(function () {
    Route::post('/skpd/datatable', [SkpdController::class, 'datatable']);
    Route::resource('/skpd', SkpdController::class);

    Route::view('examples/simple-tables', 'pages.examples.simple-tables')->name('examples.simple-tables');
});

// S3 Signed URL Routes
Route::middleware(['auth'])->prefix('s3')->name('s3.')->group(function () {
    Route::post('upload-url', [App\Http\Controllers\S3Controller::class, 'uploadUrl'])->name('upload-url');
    Route::post('view-url', [App\Http\Controllers\S3Controller::class, 'viewUrl'])->name('view-url');
});
