<?php

use Illuminate\Support\Facades\Route;

// S3 Signed URL Routes
Route::middleware(['auth'])->prefix('s3')->name('s3.')->group(function () {
    Route::post('upload-url', [App\Http\Controllers\S3Controller::class, 'uploadUrl'])->name('upload-url');
    Route::post('view-url', [App\Http\Controllers\S3Controller::class, 'viewUrl'])->name('view-url');
});
