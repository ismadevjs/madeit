<?php

use App\Http\Controllers\FileController;
use App\Models\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

