<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaestroProductoController;
use App\Http\Controllers\TrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

// Rutas de productos
Route::get('/productos', [MaestroProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/descargar', [MaestroProductoController::class, 'descargarCSV'])->name('productos.descargar');

// Rutas RESTful para el tracking
Route::resource('tracking', TrackingController::class);

