<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\PclController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware('auth')->group(function () {

    Route::get('/', [MainController::class, 'index'])->middleware('check.role');
    Route::get('/desa/{id}', [MainController::class, 'getVillage']);
    Route::get('/bs/{id}', [MainController::class, 'getBs']);
    Route::get('/sample/{id}', [MainController::class, 'getSample']);

    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['middleware' => ['role:adminprov']], function () {
        Route::get('/adminprov', [MainController::class, 'index']);
    });
    Route::group(['middleware' => ['role:adminkab']], function () {
        Route::get('/adminkab', [MainController::class, 'index']);
    });
    Route::group(['middleware' => ['role:pml|pcl']], function () {
        Route::get('/petugas', [PclController::class, 'index']);
        Route::get('/petugas/create', [PclController::class, 'create']);
        Route::patch('/petugas/edit/{id}', [PclController::class, 'update']);
        Route::patch('/petugas/edit/sample/{id}', [PclController::class, 'updateSample']);
    });
});

require __DIR__ . '/auth.php';
