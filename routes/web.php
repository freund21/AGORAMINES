<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Admin\ElectionManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\ElectionList;
use App\Livewire\ElectionResults;
use App\Livewire\VerifyVote;
use App\Livewire\VotingForm;
use Illuminate\Support\Facades\Route;

// BASE LARAVEL + PROYECTO:
// Este archivo es PHP + Laravel.
// - PHP: sintaxis del archivo, clases, llamadas a métodos.
// - Laravel: Route::get/post, middleware, name, group, prefix.
// - LIVEWIRE: cuando una ruta apunta a una clase de app/Livewire.
// - PROYECTO: las URLs concretas y que pantalla abre cada una.

// PROYECTO + BASE LARAVEL:
// Login clásico (no Livewire): formulario HTTP normal hacia AuthController.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// BASE LARAVEL:
// Grupo protegido por auth: solo usuarios autenticados.
Route::middleware('auth')->group(function () {
    // LIVEWIRE + PROYECTO:
    // Estas 4 rutas renderizan componentes Livewire propios del proyecto.
    Route::get('/', ElectionList::class)->name('elections.index');
    Route::get('/elections/{election}/vote', VotingForm::class)->name('elections.vote');
    Route::get('/elections/{election}/results', ElectionResults::class)->name('elections.results');
    Route::get('/verify', VerifyVote::class)->name('verify');

    // PROYECTO + BASE LARAVEL:
    // Subgrupo admin: requiere middleware admin propio y prefijo /admin de Laravel.
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserManager::class)->name('users');
        Route::get('/elections', ElectionManager::class)->name('elections');
    });
});
