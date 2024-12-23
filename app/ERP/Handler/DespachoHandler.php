<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\Log;

/**
 * Clase DespachoHandler
 * Maneja la creación de guías de despacho basadas en la recepción WMS.
 */
class DespachoHandler
{
    private $estado = "A";
    private $descripcion = "Aut. para Guia Despacho";

    /**
     * Maneja el proceso principal de despacho.
     *
     * @param OrdenEntradaContext $context
     */
    public function handle(OrdenEntradaContext $context): void
    {
        Log::info('DespachoHandler iniciado.');

        $context->solicitudDespacho->cargarDocumento($context->recepcionWms->getDocumento('numeroDocumento'));

        if (!$context->solicitudDespacho->getDocumento()) {
            Log::info('No existe guía de recepción, procediendo a crear una nueva.');

            $this->guardarDocumento($context);
            $this->guardarDetalles($context);

            Log::info('DespachoHandler', ['message' => 'Documento de despacho creado con éxito.']);
        }

        Log::info('DespachoHandler finalizado.');
    }

    /**
     * Guarda el documento principal de despacho.
     *
     * @param OrdenEntradaContext $context
     */
    private function guardarDocumento(OrdenEntradaContext $context): void
    {
        $context->solicitudDespacho->guardarDocumento(function ($documento) use ($context) {
            $datosDocumento = [
                'des_folio' => $context->solicitudDespacho->getDocumento('des_folio'),
                'des_tipo' => $context->solicitudDespacho->getDocumento('des_tipo'),
                'des_fecha' => $context->solicitudDespacho->getDocumento('des_fecha'),
                'des_numrut' => $context->solicitudDespacho->getDocumento('des_numrut'),
                'des_digrut' => $context->solicitudDespacho->getDocumento('des_digrut'),
                'des_subcta' => $context->solicitudDespacho->getDocumento('des_subcta'),
                'des_nombre' => $context->solicitudDespacho->getDocumento('des_nombre'),
                'des_estado' => $this->estado,
                'des_desestado' => $this->descripcion,
                'des_sucori' => $context->solicitudDespacho->getDocumento('des_facals'),
                'des_sucdes' => $context->solicitudDespacho->getDocumento('gui_sucdes'),
                'des_numgui' => $context->solicitudDespacho->getDocumento('des_numgui'),
                'des_usuario' => $context->solicitudDespacho->getDocumento('des_usuario'),
                'des_current' => now(),
            ];

            $documento->fill($datosDocumento);
        });
    }

    /**
     * Guarda los detalles del despacho.
     *
     * @param OrdenEntradaContext $context
     */
    private function guardarDetalles(OrdenEntradaContext $context): void
    {
        $context->recepcionWms->iterarDetalle(function ($detalle) use ($context) {
            $context->solicitudDespacho->guardarDetalle(function ($detalleRecepcion) use ($context, $detalle) {
                $detalleSolicitud = $context->solicitudRecepcion->getDetalle('des_codigo', $detalle['codigoProducto']);

                $datosDetalle = [
                    'des_folio' => $context->solicitudDespacho->getDocumento('des_folio'),
                    'des_tipo' => $context->guiaRecepcion->tipoDocumento,
                    'des_fecha' => $detalleSolicitud['des_fecha'] ?? now(),
                    'des_bodori' => $detalleSolicitud['des_bodori'] ?? null,
                    'des_boddes' => $detalleSolicitud['des_boddes'] ?? null,
                    'des_codigo' => $detalleSolicitud['des_codigo'] ?? $detalle['codigoProducto'],
                    'des_descri' => $detalleSolicitud['des_descri'] ?? '',
                    'des_stockp' => $detalle['cantidadRecepcionada'] ?? 0,
                    'des_preuni' => $detalleSolicitud['des_preuni'] ?? 0,
                    'des_estado' => $this->estado,
                    'des_numgui' => $detalleSolicitud['des_numgui'] ?? null,
                ];

                $detalleRecepcion->fill($datosDetalle);
            });
        });
    }
}
