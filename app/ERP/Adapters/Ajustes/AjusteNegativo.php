<?php

namespace App\ERP\Adapters\Ajustes;

use App\ERP\Contracts\AjusteNegativoService;
use App\Http\Controllers\AjusteNegativoController;
use App\ERP\Handler\SaldoBodegaAjusNe;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AjusteNegativo implements AjusteNegativoService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function run()
    {
        DB::beginTransaction();

        try {
            // Usa el controlador para actualizar la base de datos
            $controller = $this->getController();
            $response = $controller->actualizarDesdeWMS($this->buildRequest());

            // Verifica si la respuesta contiene un error
            if ($response->status() !== 200) {
                throw new Exception($response->getData()->message);
            }

            $this->saldoBodegaAjusNe($this->context);

            DB::commit();

            return response()->json(["message" => "Proceso de Ajuste Negativo completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            // Visualización de error resumida
            $errorMessage = sprintf(
                "Error en el proceso de Ajuste Negativo: %s. Datos de la solicitud: %s",
                $e->getMessage(),
                json_encode($this->context)
            );
            Log::error($errorMessage);
            return response()->json(["message" => "Error en el proceso de Ajuste Negativo: " . $e->getMessage()], 500);
        }
    }

    public function getController()
    {
        // Ajusta según tus necesidades
        return app(AjusteNegativoController::class);
    }

    private function saldoBodegaAjusNe($context)
    {
        try {
            $handler = new SaldoBodegaAjusNe();
            $handler->handle($context); // Asegúrate de tener la instancia correcta de $context
            Log::info('DespachoTransito', ['message' => 'SaldoBodegaAjusNe ejecutado con éxito.']);
        } catch (Exception $e) {
            Log::error('DespachoTransito', ['message' => 'Error al ejecutar SaldoBodegaAjusNe: ' . $e->getMessage()]);
        }
    }

    protected function buildRequest()
    {
        $ajusteNegativoArray = json_decode(json_encode($this->context->ajusteNegativo), true);

        $requestArray = [
            'numeroDocumento' => $ajusteNegativoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $ajusteNegativoArray['fechaRecepcionWMS'] ?? null,
            'usuario' => $ajusteNegativoArray['usuario'] ?? null,
            'documentoDetalle' => $ajusteNegativoArray['documentoDetalle'] ?? []
        ];

        return new Request($requestArray);
    }
}
