<?php

namespace App\ERP\Adapters\Inventario;

use App\ERP\Contracts\InventarioService;
use App\Http\Controllers\InventarioController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolicitudInventario implements InventarioService
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

            DB::commit();

            return response()->json(["message" => "Proceso de Inventario completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function getController()
    {
        // Ajusta según tus necesidades
        return app(InventarioController::class);
    }

    protected function buildRequest()
    {
        $request = new Request([
            'numeroDocumento' => $this->context->inventario->numeroDocumento,
            'fechaCierre' => $this->context->inventario->fechaCierre,
            'Bodega' => $this->context->inventario->Bodega,
            'usuario' => $this->context->inventario->usuario,
            'documentoDetalle' => $this->context->inventario->documentoDetalle,
        ]);
    
        // Convertir el objeto stdClass a un array asociativo
        $inventarioArray = json_decode(json_encode($this->context->inventario), true);
    
        return $request->merge($inventarioArray);
    }
    
}
