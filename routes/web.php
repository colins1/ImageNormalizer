<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImageController::class, 'index'])->name('images.index');

Route::post('/process', [ImageController::class, 'process'])->name('images.process');

Route::get('/batches/{token}/download', [ImageController::class, 'download'])->name('batches.download');