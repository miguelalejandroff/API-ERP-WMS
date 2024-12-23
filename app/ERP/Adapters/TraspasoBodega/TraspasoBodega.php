<?php

namespace App\ERP\Adapters\TraspasoBodega;

use App\ERP\Contracts\TraspasoBodegaService;
use App\Http\Controllers\TraspasoBodegaController;
use App\ERP\Handler\SaldoBodegaCanje;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TraspasoBodega implements TraspasoBodegaService
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
            // Utiliza TuControladorInventario para actualizar la base de datos
            $controller = $this->getController();
            $controller->actualizarDesdeWMS($this->buildRequest());

            $this->saldoBodegaCanje($this->context);

            DB::commit();

            return response()->json(["message" => "Proceso de Traspaso de Bodega completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function getController()
    {
        // Ajusta según tus necesidades
        return app(TraspasoBodegaController::class);
    }

    private function saldoBodegaCanje($context)
    {
        try {
            $handler = new SaldoBodegaCanje();
            $handler->handle($context); // Asegúrate de tener la instancia correcta de $context
            Log::info('DespachoTransito', ['message' => 'SaldoBodegaCanje ejecutado con éxito.']);
        } catch (Exception $e) {
            Log::error('DespachoTransito', ['message' => 'Error al ejecutar SaldoBodegaCanje: ' . $e->getMessage()]);
            // Agrega más detalles si es necesario
        }
    }
    
    

    protected function buildRequest()
    {
        $request = new Request([
            'numeroDocumento' => $this->context->traspasoBodega->numeroDocumento,
            'fechaRecepcionWMS' => $this->context->traspasoBodega->fechaRecepcionWMS,
            'usuario' => $this->context->traspasoBodega->usuario,
            'documentoDetalle' => $this->context->traspasoBodega->documentoDetalle,
        ]);

        $traspasoBodegaArray = json_decode(json_encode($this->context->traspasoBodega), true);
    
        return $request->merge($traspasoBodegaArray);
    }
}
