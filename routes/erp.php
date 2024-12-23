<?php

use App\ERP\EndpointERP;
use Illuminate\Support\Facades\Route;

Route::prefix('ERP')->middleware(['tokenWMS', 'LogRequest'])->group(function () {
    Route::post('ConfirmarOrdenEntrada', [EndpointERP::class, 'confirmarOrdenEntrada']);
    Route::post('ConfirmarOrdenEntrada2', [EndpointERP::class, 'confirmarOrdenEntrada2']);
    Route::post('ConfirmarTraspasoBodega', [EndpointERP::class, 'confirmarTraspasoBodega']);
    Route::post('ConfirmarAjustePositivo', [EndpointERP::class, 'confirmarAjustePositivo']);
    Route::post('ConfirmarAjusteNegativo', [EndpointERP::class, 'confirmarAjusteNegativo']);
    Route::post('ConfirmarInventario', [EndpointERP::class, 'confirmarInventario']);
    Route::post('ConfirmarOrdenSalida', [EndpointERP::class, 'confirmarOrdenSalida']);
    Route::post('ConfirmarCancelarDocumento', [EndpointERP::class, 'confirmarCancelarDocumento']);
    Route::get('StockDisponible', [App\Http\Controllers\WMSController::class, 'getStockDisponible']);
});
