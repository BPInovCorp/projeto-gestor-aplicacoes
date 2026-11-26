<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarEventActionController;
use App\Http\Controllers\CalendarEventTypeController;
use App\Http\Controllers\ContactRoleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\VatRateController;
use App\Http\Controllers\ViesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('painel', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('utilizadores', UserController::class)->parameters(['utilizadores' => 'user']);
    
    Route::resource('entidades', \App\Http\Controllers\EntityController::class)->names('entities');
    Route::post('vies/validar', ViesController::class)->name('vies.validar');

    Route::middleware(['can:configuracoes.gerir'])->prefix('configuracoes')->name('settings.')->group(function () {
        Route::resource('paises', CountryController::class)->names('countries')->except(['create', 'show', 'edit']);
        Route::resource('funcoes-contacto', ContactRoleController::class)->names('contact-roles')->except(['create', 'show', 'edit']);
        Route::resource('taxas-iva', VatRateController::class)->names('vat-rates')->except(['create', 'show', 'edit']);
        Route::resource('tipos-calendario', CalendarEventTypeController::class)->names('calendar-event-types')->except(['create', 'show', 'edit']);
        Route::resource('acoes-calendario', CalendarEventActionController::class)->names('calendar-event-actions')->except(['create', 'show', 'edit']);
    });
});

require __DIR__.'/settings.php';
