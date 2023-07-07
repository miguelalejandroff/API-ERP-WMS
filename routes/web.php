<?php

use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\ERP\EndpointERP;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\developer;
use App\Http\Controllers\Logs\Log;
use App\Http\Controllers\Test;
use App\Libs\GuiaCompra;
use App\Libs\GuiaRecepcion;
use App\Libs\WMS;
use App\WMS\EndpointWMS;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});

/**
 * Grupo de rutas para ingresar o crear documentos en el WMS
 */
Route::prefix('WMS')->middleware('database:informix')->group(function () {

    //Route::get('developer', [developer::class, 'dev'])->name('developer');
    Route::post('CreateOrdenEntrada', [EndpointWMS::class, 'createOrdenEntrada']);

    Route::post('CreateOrdenEntradaCambioEstado', function (Request $request) {
        return response()->json($request, 200, []);
    });
    Route::post('CreateItem', [EndpointWMS::class, 'createItem']);
});
/**
 * Grupo de rutas para ingresar o crear documentos en el ERP
 */
Route::prefix('ERP')->middleware('tokenWMS', 'database:informix_dev_157')->group(function () {

    /*Route::post('CreateOrdenEntrada', function (Request $request) {
        Log::append('CreateOrdenEntrdaERP', "{$request}");
        return response()->json($request, 200, []);
    });*/
    Route::post('CreateOrdenEntrada', [EndpointERP::class, 'createOrdenEntrada']);

    Route::post('CreateOrdenSalida', [EndpointERP::class, 'createOrdenSalida']);
});

/**
 * Grupo de rutas para realizar pruebas
 */
Route::prefix('test')->middleware('database:informix_dev_157')->group(function () {

    Route::get('CreateOrdenEntrada', [Test::class, 'createOrdenEntrada']);
});

/**
 * Grupo de rutas para desarrollar en la base 157
 */
Route::prefix('dev')->middleware('database:informix_dev')->group(function () {
    //Route::get('pdf',[''])
    Route::get('developer', [developer::class, 'dev'])->name('developer');
    Route::post('guiaCompra', [GuiaCompra::class, '__construct'])->name('guiaCompra');
    Route::post('guiaRecepcion', [GuiaRecepcion::class, '__construct'])->name('guiaRecepcion');
    Route::post('OrdenCompraRecepcion', [OrdenCompraRecepcion::class, '__construct']);
});
