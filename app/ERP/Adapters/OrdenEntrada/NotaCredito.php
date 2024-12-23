<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\Enums\SaldoBodegaEnum;
use App\ERP\Build\Adapter;
use App\ERP\Contracts\ERPOrdenEntradaService;
use App\Libs\SaldoBodega;
use App\Logs\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class NotaCredito extends Adapter implements ERPOrdenEntradaService
{
    /**
     * Ejecuta el proceso de recepción de Nota de Crédito.
     *
     * @param object $recepcion
     * @param array $recepcionDetalle
     * @param string $trackingId
     * @return JsonResponse
     */
    public function run($recepcion, $recepcionDetalle, $trackingId): JsonResponse
    {
        // Validar entrada
        $this->validateInput($recepcion, $recepcionDetalle);

        DB::beginTransaction();

        try {
            Log::info('/NotaCredito - Inicio del proceso', ['trackingId' => $trackingId]);

            // Procesar cada detalle de recepción
            foreach ($recepcionDetalle as $row) {
                $this->procesarSaldoBodega($recepcion, $row);
            }

            DB::commit();

            Log::info('/NotaCredito - Proceso completado', ['trackingId' => $trackingId]);

            return $this->successResponse("Nota de crédito procesada exitosamente", $trackingId);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('/NotaCredito - Error', [
                'trackingId' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse("Error al procesar la nota de crédito: " . $e->getMessage(), $trackingId);
        }
    }

    /**
     * Valida los datos de entrada.
     *
     * @param object $recepcion
     * @param array $recepcionDetalle
     * @throws Exception
     */
    private function validateInput($recepcion, $recepcionDetalle): void
    {
        if (empty($recepcion) || empty($recepcionDetalle)) {
            throw new Exception("Datos de recepción inválidos o incompletos.");
        }

        if (empty($recepcion->bodegaDestino)) {
            throw new Exception("El campo 'bodegaDestino' es obligatorio.");
        }
    }

    /**
     * Procesa el saldo de bodega.
     *
     * @param object $recepcion
     * @param object $row
     * @throws Exception
     */
    private function procesarSaldoBodega($recepcion, $row): void
    {
        new SaldoBodega(
            $recepcion->bodegaDestino,
            $row->codigoProducto,
            $row->cantidadRecepcionada,
            SaldoBodegaEnum::INCREMENT,
            function ($message) {
                throw new Exception($message);
            }
        );
    }

    /**
     * Genera una respuesta de éxito.
     *
     * @param string $message
     * @param string $trackingId
     * @return JsonResponse
     */
    private function successResponse(string $message, string $trackingId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'trackingId' => $trackingId
        ], 200);
    }

    /**
     * Genera una respuesta de error.
     *
     * @param string $message
     * @param string $trackingId
     * @return JsonResponse
     */
    private function errorResponse(string $message, string $trackingId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'trackingId' => $trackingId
        ], 500);
    }
}
