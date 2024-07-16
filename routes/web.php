<?php

// use Illuminate\Support\Facades\Route;
// use Keyur\Prikedcd\Http\Controllers\PrikedcdController;

// Route::get('/prikedcd', [PrikedcdController::class, 'index']);
use Illuminate\Support\Facades\Route;
use Keyur\Prikedcd\Http\Controllers\PrikedcdController;

// Route::get('/prikedcd', [PrikedcdController::class, 'index']);
Route::get('/prikedcd_scan', [PrikedcdController::class, 'scan'])->name('scan');
Route::get('/prikedcd_model', [PrikedcdController::class, 'model_scan'])->name('models_scan');

Route::view('/dead-code-detector', 'prikedcd::admin.dashboard')->name('controller_scan');
Route::view('/dead-code-detector/model', 'prikedcd::admin.model')->name('model_scan');
Route::view('/dead-code-detector/app', 'prikedcd::admin.app')->name('app_scan');

