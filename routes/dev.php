<?php

use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\Libs\GuiaCompra;
use App\Libs\GuiaRecepcion;
use Illuminate\Support\Facades\Route;

Route::prefix('dev')->group(function () {
    Route::post('guiaCompra', [GuiaCompra::class, '__construct'])->name('guiaCompra');
    Route::post('guiaRecepcion', [GuiaRecepcion::class, '__construct'])->name('guiaRecepcion');
    Route::post('OrdenCompraRecepcion', [OrdenCompraRecepcion::class, '__construct']);
    Route::get('info', function () {
        phpinfo();
    })->name('developer');
});
