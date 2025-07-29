<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::prefix('admin/')->name('admin.')->group(function () {
        Route::prefix('psychometrics')->name('psychometrics.')->group(function () {
            Route::get('/', Admin\Psychometrics\Testtype\Index::class)->name('index'); // List category
            Route::get('/create', Admin\Psychometrics\Testtype\Create::class)->name('create'); // Create category
        });
    });
});

require __DIR__ . '/auth.php';
