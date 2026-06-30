<?php

use App\Http\Controllers\FreesoundSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
});

Route::prefix('freesound')->group(function () {
    Route::get('/', [FreesoundSearchController::class, 'index'])->name('freesound.index');
    Route::get('/search', [FreesoundSearchController::class, 'search'])->name('freesound.search');
    Route::get('/sounds/{sound}', [FreesoundSearchController::class, 'show'])->name('freesound.show');
    Route::post('/soundboards', [FreesoundSearchController::class, 'saveSoundBoard'])->name('freesound.soundboards.store');
    Route::delete('/freesound/soundboards/{soundBoard}', [FreesoundSearchController::class, 'destroySoundBoard'])
        ->name('freesound.soundboards.destroy');
});
