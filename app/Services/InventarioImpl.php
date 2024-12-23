<?php

namespace App\ERP\Services;

use App\ERP\Contracts\InventarioService;
use Illuminate\Http\Request;
use App\Models\cminvent;
use App\Models\cmdetinv;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventarioServiceImpl implements InventarioService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Método principal para ejecutar la operación de inventario.
     */
    public function run()
    {
        $data = $this->context->all();

        // Validar datos recibidos
        $this->validarDatos($data);

        // Actualizar la base de datos en una transacción
        $this->actualizarInventario($data);

        // Retornar respuesta de éxito
        return response()->json([
            'message' => 'Inventario actualizado con éxito',
            'numeroDocumento' => $data['numeroDocumento']
        ]);
    }

    /**
     * Validar los datos de entrada.
     */
    private function validarDatos($data)
    {
        $validator = Validator::make($data, [
            'numeroDocumento' => 'required|string',
            'bodega' => 'required|string',
            'fechaCierre' => 'required|date',
            'usuario' => 'required|string',
            'documentoDetalle' => 'required|array',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.descripcion' => 'required|string',
            'documentoDetalle.*.cantidad' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            Log::error('Error en validación de datos', ['errors' => $validator->errors()]);
            throw new \InvalidArgumentException('Datos inválidos para la operación de inventario.');
        }
    }

    /**
     * Ejecutar la actualización del inventario en una transacción.
     */
    private function actualizarInventario($data)
    {
        DB::beginTransaction();

        try {
            Log::info('Inicio de la operación de actualización en la base de datos.', ['numeroDocumento' => $data['numeroDocumento']]);

            // Actualizar tabla cminvent
            $this->actualizarCminvent($data);

            // Actualizar tabla cmdetinv
            $this->actualizarCmdetinv($data);

            DB::commit();
            Log::info('Operación de inventario completada exitosamente.', ['numeroDocumento' => $data['numeroDocumento']]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en la operación de inventario: ' . $e->getMessage());
            throw new \RuntimeException('Error al actualizar el inventario.');
        }
    }

    /**
     * Actualizar la tabla cminvent.
     */
    private function actualizarCminvent($data)
    {
        cminvent::updateOrCreate(
            ['inv_numgui' => $data['numeroDocumento']],
            [
                'inv_bodega' => $data['bodega'],
                'inv_fechai' => $data['fechaCierre'],
                'inv_codusu' => $data['usuario'],
            ]
        );

        Log::info('Actualización en cminvent completada.', ['numeroDocumento' => $data['numeroDocumento']]);
    }

    /**
     * Actualizar la tabla cmdetinv en masa.
     */
    private function actualizarCmdetinv($data)
    {
        $detalles = collect($data['documentoDetalle'])->map(function ($detalle) use ($data) {
            return [
                'inv_numgui' => $data['numeroDocumento'],
                'inv_produc' => $detalle['codigoProducto'],
                'inv_descri' => $detalle['descripcion'],
                'inv_cantid' => $detalle['cantidad'],
            ];
        });

        cmdetinv::upsert($detalles->toArray(), ['inv_numgui', 'inv_produc']);

        Log::info('Actualización en cmdetinv completada.', ['totalRegistros' => count($detalles)]);
    }
}
