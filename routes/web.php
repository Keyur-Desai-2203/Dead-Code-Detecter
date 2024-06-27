<?php

// use Illuminate\Support\Facades\Route;
// use Keyur\Prikedcd\Http\Controllers\PrikedcdController;

// Route::get('/prikedcd', [PrikedcdController::class, 'index']);
use Illuminate\Support\Facades\Route;
use Keyur\Prikedcd\Http\Controllers\PrikedcdController;

Route::get('/prikedcd', [PrikedcdController::class, 'index']);
Route::get('/prikedcd_scan', [PrikedcdController::class, 'scan']);

