<?php

use App\WMS\EndpointWMS;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('WMS')->middleware(['tokenWMS', 'LogRequest'])->group(function () {
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
