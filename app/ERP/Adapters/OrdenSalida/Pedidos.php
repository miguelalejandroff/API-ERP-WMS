<?php

namespace App\ERP\Adapters\OrdenSalida;

use App\ERP\Contracts\OrdenSalidaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class Pedidos implements OrdenSalidaService
{
    protected $handlers = [];
    protected $context;

    /**
     * Constructor con contexto y manejadores opcionales.
     *
     * @param mixed $context
     * @param array $handlers
     */
    public function __construct($context, array $handlers = [])
    {
        $this->context = $context;
        $this->handlers = $handlers;
    }

    /**
     * Ejecuta el proceso de generación de pedidos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            Log::info('Inicio del proceso de pedidos', ['context' => $this->context]);

            // Ejecutar la cadena de manejadores
            foreach ($this->handlers as $handler) {
                Log::info('Ejecutando manejador', ['handler' => get_class($handler)]);
                $handler->execute($this->context);
            }

            DB::commit();
            Log::info('Proceso de pedidos completado exitosamente');

            return response()->json([
                "success" => true,
                "message" => "Proceso de Pedidos sin Problemas"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en el proceso de pedidos', [
                'error' => $e->getMessage(),
                'context' => $this->context
            ]);

            return response()->json([
                "success" => false,
                "message" => "Error en el proceso de Pedidos: " . $e->getMessage()
            ], 500);
        }
    }
}

//Preguntar a Cesar si es solo la generacion de GuiaDespacho
