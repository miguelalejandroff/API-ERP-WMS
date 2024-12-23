<?php

namespace App\ERP\Adapters\Ajustes;

use App\ERP\Contracts\AjustePositivoService;
use App\Http\Controllers\AjustePositivoController;
use App\ERP\Handler\SaldoBodegaAjusPos;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AjustePositivo implements AjustePositivoService
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

            //Verifica si la respuesta contiene un error
            if ($response->status() !== 200) {
                throw new Exception($response->getData()->message);
            }


            $this->saldoBodegaAjusPos($this->context);

            DB::commit();

            return response()->json(["message" => "Proceso de Ajuste Positivo completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            // Visualización de error resumida
            $errorMessage = sprintf(
                "Error en el proceso de Ajuste Positivo: %s. Datos de la solicitud: %s",
                $e->getMessage(),
                json_encode($this->context)
            );
            Log::error($errorMessage);
            return response()->json(["message" => "Error en el proceso de Ajuste Positivo: " . $e->getMessage()], 500);
        }
    }
    public function getController()
    {
        // Ajusta según tus necesidades
        return app(AjustePositivoController::class);
    }

    private function saldoBodegaAjusPos($context)
    {
        try {
            $handler = new SaldoBodegaAjusPos();
            $handler->handle($context); // Asegúrate de tener la instancia correcta de $context
            Log::info('DespachoTransito', ['message' => 'SaldoBodegaAjusPos ejecutado con éxito.']);
        } catch (Exception $e) {
            Log::error('DespachoTransito', ['message' => 'Error al ejecutar SaldoBodegaAjusPos: ' . $e->getMessage()]);
        }
    }

    protected function buildRequest()
    {
        $ajustePositivoArray = json_decode(json_encode($this->context->ajustePositivo), true);

        $requestArray = [
            'numeroDocumento' => $ajustePositivoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $ajustePositivoArray['fechaRecepcionWMS'] ?? null,
            'usuario' => $ajustePositivoArray['usuario'] ?? null,
            'documentoDetalle' => $ajustePositivoArray['documentoDetalle'] ?? []
        ];

        return new Request($requestArray);
    }
}
