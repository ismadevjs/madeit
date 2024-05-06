<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;


Route::get('providers', [ProviderController::class, 'providers']);
Route::get('/files', [FileController::class, 'files']);
Route::prefix('upload')->group(function() {
    Route::post('image', [FileController::class, 'uploadImage']);
    Route::post('video', [FileController::class, 'uploadVideo']);
});

