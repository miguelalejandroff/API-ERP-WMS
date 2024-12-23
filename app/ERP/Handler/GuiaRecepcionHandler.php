<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Clase GuiaRecepcionHandler
 * Maneja la creación de guías de recepción basadas en la información de recepción WMS.
 */
class GuiaRecepcionHandler
{
    private $bodegaOrigen = 29;
    private $sucursalOrigen = 1;

    /**
     * Maneja el proceso de creación de guía de recepción.
     *
     * @param OrdenEntradaContext $context
     * @return void
     */
    public function handle(OrdenEntradaContext $context): void
    {
        Log::info('GuiaRecepcionHandler iniciado.');

        DB::beginTransaction();

        try {
            // Cargar información del documento principal
            $this->guardarDocumento($context);

            // Guardar detalles del documento
            $this->guardarDetalles($context);

            $context->guiaRecepcion->enviaWms = true;

            Log::info('GuiaRecepcionHandler', [
                'message' => 'Guía de recepción creada con éxito',
                'documento' => $context->guiaRecepcion->getDocumento()
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en GuiaRecepcionHandler', ['message' => $e->getMessage()]);
            throw $e;
        }

        Log::info('GuiaRecepcionHandler finalizado.');
    }

    /**
     * Guarda los datos del documento principal.
     *
     * @param OrdenEntradaContext $context
     */
    private function guardarDocumento(OrdenEntradaContext $context): void
    {
        $context->solicitudRecepcion->cargarDocumento($context->recepcionWms->getDocumento('numeroDocumento'));

        $context->guiaRecepcion->guardarDocumento(function ($documento) use ($context) {
            $datos = [
                'gui_numero' => $context->solicitudRecepcion->getDocumento('gui_numero'),
                'gui_tipgui' => $context->solicitudRecepcion->tipoDocumento,
                'gui_fechag' => $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d'),
                'gui_ordcom' => $context->solicitudRecepcion->getDocumento('gui_ordcom'),
                'gui_numrut' => $context->solicitudRecepcion->getDocumento('gui_numrut'),
                'gui_digrut' => $context->solicitudRecepcion->getDocumento('gui_digrut'),
                'gui_subcta' => $context->solicitudRecepcion->getDocumento('gui_subcta'),
                'gui_nombre' => $context->solicitudRecepcion->getDocumento('gui_nombre'),
                'gui_guipro' => $context->solicitudRecepcion->getDocumento('gui_guipro') ?? 0,
                'gui_facpro' => $context->solicitudRecepcion->getDocumento('gui_facpro') ?? 0,
                'gui_facals' => $context->solicitudRecepcion->getDocumento('gui_facals') ?? 0,
                'gui_sucori' => $this->sucursalOrigen,
                'gui_sucdes' => $context->solicitudRecepcion->getDocumento('gui_sucdes'),
                'gui_paract' => $context->parametro,
                'gui_fecmod' => $context->recepcionWms->getDocumento('fechaRecepcionWMS')->format('Y-m-d'),
                'gui_codusu' => $context->solicitudRecepcion->getDocumento('gui_codusu'),
                'gui_empres' => $context->empresa,
            ];

            foreach ($datos as $key => $value) {
                $documento->$key = $value;
            }
        });
    }

    /**
     * Guarda los detalles del documento.
     *
     * @param OrdenEntradaContext $context
     */
    private function guardarDetalles(OrdenEntradaContext $context): void
    {
        $context->recepcionWms->iterarDetalle(function ($detalle) use ($context) {
            $context->guiaRecepcion->guardarDetalle(function ($detalleRecepcion) use ($context, $detalle) {
                $detalleSolicitud = $context->solicitudRecepcion->getDetalle('gui_produc', $detalle['codigoProducto']);
                $detallePrecio = $context->guiaCompra->getDetalle('gui_produc', $detalle['codigoProducto']);

                $datosDetalle = [
                    'gui_numero' => $context->recepcionWms->getDocumento('numeroDocumento'),
                    'gui_tipgui' => $context->guiaRecepcion->tipoDocumento,
                    'gui_bodori' => $this->bodegaOrigen,
                    'gui_boddes' => $detalleSolicitud['gui_boddes'],
                    'gui_produc' => $detalle['codigoProducto'],
                    'gui_descri' => $detalleSolicitud['gui_descri'],
                    'gui_canord' => $detalle['cantidadSolicitada'],
                    'gui_canrep' => $detalle['cantidadRecepcionada'],
                    'gui_preuni' => $detallePrecio['gui_preuni'],
                ];

                foreach ($datosDetalle as $key => $value) {
                    $detalleRecepcion->$key = $value;
                }
            });
        });
    }
}
