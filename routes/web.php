<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // AdminLTE Examples
    Route::view('examples/simple-tables', 'pages.examples.simple-tables')->name('examples.simple-tables');
});

// S3 Signed URL Routes
Route::middleware(['auth'])->prefix('s3')->name('s3.')->group(function () {
    Route::post('upload-url', [App\Http\Controllers\S3Controller::class, 'uploadUrl'])->name('upload-url');
    Route::post('view-url', [App\Http\Controllers\S3Controller::class, 'viewUrl'])->name('view-url');
});
