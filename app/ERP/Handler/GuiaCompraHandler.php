<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Clase GuiaCompraHandler
 * Maneja la creación y actualización de guías de compra.
 */
class GuiaCompraHandler
{
    private $bodegaOrigen = 0;
    private $bodegaDestino = 29;
    private $sucursalOrigen = 0;
    private $sucursalDestino = 1;

    /**
     * Maneja la creación y actualización de la guía de compra.
     *
     * @param OrdenEntradaContext $context
     */
    public function handle(OrdenEntradaContext $context): void
    {
        Log::info('GuiaCompraHandler iniciado.');

        DB::beginTransaction();

        try {
            $this->crearGuiaCompra($context);
            $this->actualizarSaldos($context);

            DB::commit();
            Log::info('GuiaCompraHandler finalizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en GuiaCompraHandler', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Crea la guía de compra si no existe.
     *
     * @param OrdenEntradaContext $context
     */
    private function crearGuiaCompra(OrdenEntradaContext $context): void
    {
        if (!$context->guiaCompra->getDocumento()) {
            $context->guiaCompra->guardarDocumento(function ($documento) use ($context) {
                $documento->fill([
                    'gui_numero' => $context->ordenCompra->getDocumento('ord_numcom'),
                    'gui_tipgui' => $context->guiaCompra->tipoDocumento,
                    'gui_fechag' => now()->format('Y-m-d'),
                    'gui_ordcom' => $context->ordenCompra->getDocumento('ord_numcom'),
                    'gui_numrut' => $context->proveedor->getDocumento('aux_numrut'),
                    'gui_digrut' => $context->proveedor->getDocumento('aux_digrut'),
                    'gui_sucori' => $this->sucursalOrigen,
                    'gui_sucdes' => $this->sucursalDestino,
                    'gui_empres' => $context->empresa,
                    'gui_codusu' => $context->solicitudRecepcion->getDocumento('gui_codusu'),
                ]);
            });

            $this->guardarDetallesGuia($context);
            Log::info('Guía de compra creada', ['numero' => $context->guiaCompra->getDocumento('gui_numero')]);
        }
    }

    /**
     * Guarda los detalles de la guía de compra.
     *
     * @param OrdenEntradaContext $context
     */
    private function guardarDetallesGuia(OrdenEntradaContext $context): void
    {
        $context->ordenCompra->iterarDetalle(function ($detalle) use ($context) {
            $context->guiaCompra->guardarDetalle(function ($detalleCompra) use ($detalle) {
                $detalleCompra->fill([
                    'gui_bodori' => $this->bodegaOrigen,
                    'gui_boddes' => $this->bodegaDestino,
                    'gui_produc' => $detalle->ord_produc,
                    'gui_descri' => $detalle->ord_descri,
                    'gui_canord' => $detalle->calculaCosto->cantidadCalculada,
                    'gui_canrep' => $detalle->calculaCosto->cantidadCalculada,
                    'gui_preuni' => $detalle->calculaCosto->precioCalculado,
                    'gui_saldo' => $detalle->calculaCosto->cantidadCalculada,
                ]);
            });
        });
    }

    /**
     * Actualiza los saldos de la orden de compra y guía.
     *
     * @param OrdenEntradaContext $context
     */
    private function actualizarSaldos(OrdenEntradaContext $context): void
    {
        $context->guiaCompra->iterarDetalle(function ($detalle) use ($context) {
            $recepcionWms = $context->recepcionWms->getDetalle('codigoProducto', $detalle->gui_produc);

            if ($recepcionWms) {
                $context->guiaCompra->guardarDetalle(function ($detalleCompra) use ($recepcionWms) {
                    $detalleCompra->gui_saldo -= $recepcionWms['cantidadRecepcionada'];
                }, ['gui_numero' => $detalle->gui_numero, 'gui_produc' => $detalle->gui_produc]);
            }
        });

        $context->ordenCompra->iterarDetalle(function ($detalle) use ($context) {
            $recepcionWms = $context->recepcionWms->getDetalle('codigoProducto', $detalle->ord_produc);

            if ($recepcionWms && $recepcionWms['cantidadSolicitada'] > $recepcionWms['cantidadRecepcionada']) {
                $diferencia = $recepcionWms['cantidadSolicitada'] - $recepcionWms['cantidadRecepcionada'];

                $context->ordenCompra->guardarDetalle(function ($detalleCompra) use ($diferencia) {
                    $detalleCompra->ord_saldos += $diferencia;
                });
            }
        });
    }

    /**
     * Envía la guía de compra al sistema WMS.
     *
     * @param OrdenEntradaContext $context
     */
    public function enviaGuiaCompraWMS(OrdenEntradaContext $context): void
    {
        try {
            $guiaCompra = $context->guiaCompra->getDocumento('gui_numero');
            $url = url('/WMS/CreateOrdenEntrada');

            $response = Http::post($url, ['guiaCompra' => $guiaCompra]);

            Log::info('Guía enviada a WMS', ['response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error('Error al enviar guía al WMS', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
