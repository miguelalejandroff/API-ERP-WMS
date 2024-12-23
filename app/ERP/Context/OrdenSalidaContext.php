<?php

namespace App\ERP\Context;

use App\Models\cmordcom;
use App\Models\cmclientes;
use App\Models\wmscmguias;
use Carbon\Carbon;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Models\cmproductos;
use App\ERP\Handler\MaestroProductoHandler;

class OrdenEntradaContext
{
    public $trackingId;
    //public $ordenCompraBonificada;

    /**
     * @var string $parametro define un parámetro de defecto. Por defecto es "N".
     */
    public $parametro = "N";

    /**
     * @var string $empresa define el ID de la empresa. Por defecto es 1.
     */
    public $empresa = 1;

    /**
     * @var object $recepcion define la recepción creada por el WMS.
     */
    public $recepcionWms;

    /**
     * @var object $proveedor define el proveedor.
     */
    public $proveedor;

    /**
     * @var object $producto define el producto a actualizar
     */
    public $producto;

    /**
     * @var int Precio actual del producto.
     */
    public $precio;

    /**
     * @var int Cantidad actual del producto.
     */
    public $cantidad;

    /**
     * @var string Fecha de la operación.
     */
    public $fecha;

    /**
     * @var string Documento asociado a la operación.
     */
    public $documento;

    /**
     * Establecer el producto en el contexto.
     * @param object $producto El objeto del producto.
     */

    /**
     * @var object $solicitudDespacho  define la solicitud de Despacho enviada por el ERP.
     */
    public $solicitudDespacho;

    public function setProducto($producto)
    {
        $this->producto = $producto;
    }


    public function __construct($requestData)
    {
        $this->trackingId = uniqid();

        $this->recepcionWms = new RecepcionWmsContext;
        $this->recepcionWms->cargarDocumento($requestData);

        $this->solicitudDespacho = new SolicitudDespachoContext;
        $this->solicitudDespacho->cargarDocumento($this->recepcionWms->getDocumento('numeroDocumento'));
    }
}
