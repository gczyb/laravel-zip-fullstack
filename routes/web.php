<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CityController;

Route::get('/', [CityController::class, 'index'])->name('home');


Auth::routes(['verify' => true]);
Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

Route::get('cities/search', [CityController::class, 'search'])->name('cities.search');
Route::get('cities/export/csv', [CityController::class, 'exportCsv'])->name('cities.export.csv');
Route::get('cities/export/pdf', [CityController::class, 'exportPdf'])->name('cities.export.pdf');
Route::post('cities/send-email', [CityController::class, 'sendEmail'])->name('cities.sendEmail');
Route::get('cities', [CityController::class, 'index'])->name('cities.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cities', CityController::class)->except(['index', 'show']);
});