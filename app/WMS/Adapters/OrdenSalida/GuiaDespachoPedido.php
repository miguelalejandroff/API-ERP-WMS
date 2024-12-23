<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\WMS\Contracts\Outbound\OrdenSalidaDocumentoFiscalService;
use App\Libs\WMS;

class GuiaDespachoPedido extends OrdenSalidaDocumentoFiscalService
{   
    protected $model;
    protected $rubro;

    public function __construct($model, $rubro)
    {
        parent::__construct($model);
        $this->model = $model;
        $this->rubro = $rubro;
    }

    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }
    protected function nroOrdenSalida($model): string
    {
        $ped_msglog = $model->folioPedido()
        ->where('ped_codrub', $this->rubro)
        ->first()
        ->ped_msglog;


        // Buscar la posición de 'OSWMS:'
        $posicionOSWMS = strpos($ped_msglog, 'OSWMS:');

        // Verificar si 'OSWMS:' está presente en la cadena
        if ($posicionOSWMS !== false) {
            // Obtener los caracteres después de 'OSWMS:'
            $numeroOrdenSalida = substr($ped_msglog, $posicionOSWMS + strlen('OSWMS:'));

            // Eliminar espacios en blanco al final (si los hay)
            $numeroOrdenSalida = trim($numeroOrdenSalida);

            return $numeroOrdenSalida;
        }

        // Si 'OSWMS:' no está presente, retornar cadena vacía o manejar según sea necesario
        return '';
    }
    protected function folioFacturacion($model): int
    {
        return $model->gui_numero;
    }
    protected function tipoFacturacion($model): string
    {
        return 52;
    }
    protected function fechaEmision($model): string
    {
        return WMS::date($model->gui_fechag, 'Y-m-d');
    }
}
