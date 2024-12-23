<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\WMS\Contracts\Outbound\OrdenSalidaCambioEstadoService;

class CambioEstado extends OrdenSalidaCambioEstadoService
{
    protected $numeroDocumento;
    protected $tipoDocumento;
    protected $bodega;

    public function __construct($numeroDocumento, $tipoDocumento, $bodega)
    {
        $this->numeroDocumento = $numeroDocumento;
        $this->tipoDocumento = $tipoDocumento;
        $this->bodega = $bodega;
    }

    protected function codDeposito(): string
    {
        return $this->bodega;
    }
    protected function nroOrdenSalida(): string
    {
        return $this->numeroDocumento;
    }
    public function codEstado(): string
    {
        return "11";
    }

    public function observacion(): string
    {
        switch ($this->tipoDocumento) { 
            case '16':
                return "Solicitud Despacho Cliente";
            case '88':
                return "Boleta";
        }
    }
   
        
}
