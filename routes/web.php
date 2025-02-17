<?php

use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\ERP\EndpointERP;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WMSController;

use App\Http\Controllers\developer;
use App\Http\Controllers\Logs\Log;
use App\Http\Controllers\Test;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\InventarioController;
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


Route::resource('tracking', TrackingController::class);

/**
 * Grupo de rutas para ingresar o crear documentos en el WMS
 */
Route::prefix('WMS')->middleware('tokenWMS', 'LogRequest')->group(function () {

    Route::post('CreateItem', [EndpointWMS::class, 'createItem']);

    Route::post('CreateItemClase', [EndpointWMS::class, 'createItemClase']);

    Route::post('CreateItemCodigoBarra', [EndpointWMS::class, 'createItemCodigoBarra']);

    Route::post('CreateCliente', [EndpointWMS::class, 'createCliente']);

    Route::post('CreateProveedor', [EndpointWMS::class, 'createProveedor']);

    Route::post('CreateOrdenEntrada', [EndpointWMS::class, 'createOrdenEntrada']);

    Route::post('CreateOrdenSalida', [EndpointWMS::class, 'createOrdenSalida']);

    Route::post('CreateOrdenSalidaDocumentoFiscal', [EndpointWMS::class, 'createOrdenSalidaDocumentoFiscal']);

    Route::post('CreateOrdenSalidaCambioEstado', [EndpointWMS::class, 'createOrdenSalidaCambioEstado']);

    Route::post('CreateOrdenEntradaCambioEstado', function (Request $request) {
        return response()->json($request, 200, []);
    });
});
/**
 * Grupo de rutas para ingresar o crear documentos en el ERP
 */
Route::prefix('ERP')->middleware('tokenWMS', 'LogRequest')->group(function () {

    Route::post('ConfirmarOrdenEntrada', [EndpointERP::class, 'confirmarOrdenEntrada']);

    Route::post('ConfirmarOrdenEntrada2', [EndpointERP::class, 'confirmarOrdenEntrada2']);

    Route::post('ConfirmarTraspasoBodega', [EndpointERP::class, 'confirmarTraspasoBodega']);

    Route::post('ConfirmarAjustePositivo', [EndpointERP::class, 'confirmarAjustePositivo']);

    Route::post('ConfirmarAjusteNegativo', [EndpointERP::class, 'confirmarAjusteNegativo']);

    Route::post('ConfirmarInventario', [EndpointERP::class, 'confirmarInventario']);

    Route::post('ConfirmarOrdenSalida', [EndpointERP::class, 'confirmarOrdenSalida']);

    Route::post('ConfirmarCancelarDocumento', [EndpointERP::class, 'confirmarCancelarDocumento']);

    Route::get('StockDisponible', [WMSController::class, 'getStockDisponible']);



});




/**
 * Grupo de rutas para desarrollar en la base 157
 */
Route::prefix('dev')->group(function () {
    //Route::get('pdf',[''])
    Route::get('developer', [developer::class, 'dev'])->name('developer');
    Route::post('guiaCompra', [GuiaCompra::class, '__construct'])->name('guiaCompra');
    Route::post('guiaRecepcion', [GuiaRecepcion::class, '__construct'])->name('guiaRecepcion');
    Route::post('OrdenCompraRecepcion', [OrdenCompraRecepcion::class, '__construct']);
    Route::get('info', function () {
        phpinfo();
    })->name('developer');
});
