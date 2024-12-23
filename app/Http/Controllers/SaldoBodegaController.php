<?php

namespace App\Http\Controllers;

use App\Models\cmsalbod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaldoBodegaController extends Controller
{
    /**
     * Crear o actualizar el saldo de bodega.
     *
     * @param string $codigoProducto
     * @param int $bodegaDestino
     * @param float $cantidad
     * @param int $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearSaldoBodega($codigoProducto, $bodegaDestino, $cantidad, $year)
    {
        try {
            // Validar parámetros de entrada
            if (empty($codigoProducto) || empty($bodegaDestino) || $cantidad < 0 || $year < 2000) {
                Log::warning('Parámetros inválidos para crearSaldoBodega', [
                    'codigoProducto' => $codigoProducto,
                    'bodegaDestino' => $bodegaDestino,
                    'cantidad' => $cantidad,
                    'year' => $year,
                ]);

                return response()->json(['error' => 'Parámetros inválidos'], 400);
            }

            // Verificar si existe el saldoBodega
            $saldoExistente = cmsalbod::where([
                'bod_ano' => $year,
                'bod_produc' => $codigoProducto,
                'bod_bodega' => $bodegaDestino,
            ])->first();

            if ($saldoExistente) {
                Log::info('Saldo de bodega existente encontrado', [
                    'codigoProducto' => $codigoProducto,
                    'bodegaDestino' => $bodegaDestino,
                    'year' => $year,
                ]);
            }

            // Actualizar o crear el registro
            $saldo = cmsalbod::reate(
                [
                    'bod_ano' => $year,
                    'bod_produc' => $codigoProducto,
                    'bod_bodega' => $bodegaDestino,
                ],
                [
                    'bod_salini' => $saldoExistente->bod_salini ?? 0,
                    'bod_stockb' => $cantidad,
                    'bod_stolog' => $cantidad,
                    'bod_storep' => $saldoExistente->bod_storep ?? 0,
                    'bod_stomax' => $saldoExistente->bod_stomax ?? 0,
                ]
            );

            Log::info('Saldo de bodega creado o actualizado', [
                'codigoProducto' => $codigoProducto,
                'bodegaDestino' => $bodegaDestino,
                'cantidad' => $cantidad,
                'year' => $year,
                'registro' => $saldo,
            ]);

            return response()->json([
                'message' => 'Saldo de bodega actualizado correctamente',
                'data' => $saldo
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al crear o actualizar saldo de bodega', [
                'error_message' => $e->getMessage(),
                'codigoProducto' => $codigoProducto,
                'bodegaDestino' => $bodegaDestino,
                'cantidad' => $cantidad,
                'year' => $year,
            ]);

            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
