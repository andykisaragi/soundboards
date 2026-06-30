<?php

use App\Http\Controllers\FreesoundSearchController;
use Illuminate\Support\Facades\Route;


Route::get('/', [FreesoundSearchController::class, 'index'])->name('freesound.index');
Route::get('/search', [FreesoundSearchController::class, 'search'])->name('freesound.search');
Route::post('/soundboards', [FreesoundSearchController::class, 'saveSoundBoard'])->name('freesound.soundboards.store');
Route::delete('/soundboards/{soundBoard}', [FreesoundSearchController::class, 'destroySoundBoard'])
    ->name('freesound.soundboards.destroy');

