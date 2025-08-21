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
            Route::get('/', Admin\Psychometrics\Testtype\Index::class)->name('types.index'); // List category
            Route::get('/create', Admin\Psychometrics\Testtype\Create::class)->name('types.create'); // Create category

            // Tests
            Route::get('/tests', Admin\Psychometrics\Test\Index::class)->name('tests.index');
            Route::get('/tests/create', Admin\Psychometrics\Test\Create::class)->name('tests.create');
            Route::get('/tests/{test}/edit', Admin\Psychometrics\Test\Edit::class)->name('tests.edit');

            // Test Competencies
            Route::get('/tests/{test}/competencies', Admin\Psychometrics\Competency\Index::class)
                ->name('tests.competencies.index');
        });
    });
});

require __DIR__ . '/auth.php';
