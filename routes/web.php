<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminKabController;
use App\Http\Controllers\EdcodController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PclController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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
    Route::get('/petugas/data/{id?}', [MainController::class, 'getPetugasData']);
    Route::get('/rekap-ganti-sample', [MainController::class, 'getRekapSampleChange']);

    Route::get('/bsedcod/{kec}', [MainController::class, 'getBsEdcod']);
    Route::post('/bsedcod/{id}', [MainController::class, 'saveEdcod']);

    Route::group(['middleware' => ['role:adminprov|adminkab']], function () {
        // Route::get('/generate', [ReportController::class, 'generate']);

        Route::get('/report/petugas', [ReportController::class, 'reportByPetugas']);
        Route::get('/report/petugas/{id}', [ReportController::class, 'reportDetailPetugas']);
        Route::get('/report/kab', [ReportController::class, 'reportKab']);
        Route::get('/report/kec/{kodekab}', [ReportController::class, 'reportKec']);
        Route::get('/report/bs/{kodekec}', [ReportController::class, 'reportBs']);
        Route::get('/report/ruta/{kodebs}', [ReportController::class, 'reportRuta']);

        Route::get('/adminkab', [ReportController::class, 'index']);
        Route::get('/adminprov', [ReportController::class, 'index']);
        
        Route::get('/users/data', [UserController::class, 'getData']);
        Route::resource('users', UserController::class);
        Route::get('/download', [ReportController::class, 'showDownload']);
        Route::post('/download', [ReportController::class, 'download']);

        //editing coding route
        Route::get('/edcod/input', [EdcodController::class, 'input']);

    });
    Route::group(['middleware' => ['role:pml|pcl']], function () {
        Route::get('/petugas', [PclController::class, 'index']);
        Route::get('/petugas/create', [PclController::class, 'create']);
        Route::patch('/petugas/edit/{id}', [PclController::class, 'update']);
        Route::patch('/petugas/edit/sample/{id}', [PclController::class, 'updateSample']);
    });
});

require __DIR__ . '/auth.php';
