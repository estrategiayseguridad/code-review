<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Auth::routes();

Route::middleware(['auth', 'verified'])->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
    });

    Route::prefix('languages')->name('languages.')->group(function () {
        Volt::route('/', 'languages.index')->name('index');
        Volt::route('/create', 'languages.create')->name('create');
        Volt::route('/{languageId}/edit', 'languages.edit')->name('edit');
    });

    Route::name('extensions.')->group(function () {
        Volt::route('/extensions/{extensionId}/edit', 'extensions.edit')->name('edit');
        Volt::route('/languages/{languageId}/extensions/create', 'extensions.create')->name('create');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Volt::route('/', 'projects.index')->name('index');
        Volt::route('/new', 'projects.create')->name('new');
        Volt::route('/{projectId}/analysis', 'projects.analysis')->name('analysis');
    });

    Route::group(['middleware' => ['role:admin']], function () {

        // Route::resources([
        //     'permissions' => PermissionController::class,
        //     'roles' => RoleController::class,
        //     'users' => UserController::class
        // ]);

        Route::prefix('roles')->name('roles.')->group(function () {
            Volt::route('/', 'roles.index')->name('index');
            Volt::route('/new', 'roles.create')->name('new');
            Volt::route('/{roleId}/edit', 'roles.edit')->name('edit');
        });

        Route::controller(PermissionController::class)
            ->prefix('permissions')
            ->name('permissions.')
            ->group(function () {
                Route::delete('/{permissionId}/delete', 'destroy')->name('delete');
            });

        Route::controller(RoleController::class)
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                Route::delete('/{roleId}/delete', 'destroy')->name('delete');
                Route::get('/{roleId}/give-permissions', 'addPermissionToRole')->name('give-permissions');
                Route::put('/{roleId}/give-permissions', 'givePermissionToRole')->name('give-permissions');
            });

        Route::controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::delete('/{userId}/delete', 'destroy')->name('delete');
            });

    });

});

Route::group(['middleware' => ['role:super-admin|admin']], function () {




});
