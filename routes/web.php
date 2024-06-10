<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Volt::route('/', 'projects.index')->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');

    Route::prefix('config')->name('config.')->group(function () {
        Volt::route('/general', 'config.general')->name('general');
    });

    Route::prefix('languages')->name('languages.')->group(function () {
        Volt::route('/', 'languages.index')->name('index');
        Volt::route('/{languageId}/edit', 'languages.edit')->name('edit');
        Volt::route('/extensions', 'languages.extensions')->name('extensions');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Volt::route('/new', 'projects.create')->name('new');
        Volt::route('/{projectId}/analysis', 'projects.analysis')->name('analysis');
        Volt::route('/{projectId}/export', 'projects.export')->name('export');
        Volt::route('/{projectId}/vulnerabilities', 'projects.vulnerabilities')->name('vulnerabilities');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {
    Volt::route('/history', 'pages.history')->name('history');

    Route::prefix('config')->name('config.')->group(function () {
        Volt::route('/users', 'config.users.index')->name('users');
    });
});



require __DIR__.'/auth.php';
