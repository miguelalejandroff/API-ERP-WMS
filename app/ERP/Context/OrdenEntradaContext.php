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
use App\ERP\Enum\TipoDocumentoERP;

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
     * @var object $ordenCompra define la orden de compra.
     */
    public $ordenCompra;

    /**
     * @var object $solicitudRecepcion  define la solicitud de recepción creada por el ERP.
     */
    public $solicitudRecepcion;

    /**
     * @var object $recepcion define la recepción creada por el WMS.
     */
    public $recepcionWms;

    /**
     * @var object $proveedor define el proveedor.
     */
    public $proveedor;

    /**
     * @var object $guiaCompra  define la guia de compra creada por el ERP.
     */
    public $guiaCompra;

    /**
     * @var object $guiaRecepcion define la guia de recepcion creada por el ERP
     */
    public $guiaRecepcion;


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
     * @var int Cantidad recepcionada del producto.
     */
    public $cantidadRecepcionada;

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


    public function setProducto($producto)
    {
        $this->producto = $producto;
    }


    public function __construct($requestData)
    {
        $this->trackingId = uniqid();
    
        // Verificar si existe la clave 'tipoDocumentoERP' en $requestData
        if (array_key_exists('tipoDocumentoERP', $requestData)) {
            $tipoDocumentoERP = $requestData['tipoDocumentoERP'];
    
            // Inicializar el contexto dependiendo del tipo de documento ERP
            switch ($tipoDocumentoERP) {
                case TipoDocumentoERP::SOLICITUD_RECEPCION->value:
                    $this->initializeSolicitudRecepcionContext($requestData);
                    break;
                default:            
                    $this->initializeDefaultContext($requestData);
                    break;
            }
        } else {
            // Si no existe 'tipoDocumentoERP', inicializar el contexto predeterminado
            $this->initializeDefaultContext($requestData);
        }
    }
    
    
    // Función para inicializar contextos para TipoDocumentoERP::SOLICITUD_RECEPCION
    private function initializeSolicitudRecepcionContext($requestData)
    {
        $this->recepcionWms = new RecepcionWmsContext;
        $this->recepcionWms->cargarDocumento($requestData);
    
        $this->solicitudRecepcion = new SolicitudRecepcionContext;
        $this->solicitudRecepcion->cargarDocumento($this->recepcionWms->getDocumento('numeroDocumento'));
    
        $this->ordenCompra = new OrdenCompraContext;
        $this->ordenCompra->cargarDocumento($this->solicitudRecepcion->getDocumento('gui_ordcom'));
    
        $this->proveedor = new ProveedorContext;
        $this->proveedor->cargarDocumento($this->solicitudRecepcion->getDocumento('gui_subcta'));
    
        $this->guiaCompra = new GuiaCompraContext;
        //$this->guiaCompra->cargarDocumento($this->ordenCompra->getDocumento('ord_numcom'));
    
        $this->guiaRecepcion = new GuiaRecepcionContext;
        $this->guiaRecepcion->cargarDocumento($this->recepcionWms->getDocumento('numeroDocumento'));
    }
    
    // Función para inicializar contextos para otros tipos de documentos
    private function initializeDefaultContext($requestData)
    {
        $this->recepcionWms = new RecepcionWmsContext;
        $this->recepcionWms->cargarDocumento($requestData);
    
        $this->guiaRecepcion = new GuiaRecepcionContext;
        $this->guiaRecepcion->cargarDocumento($this->recepcionWms->getDocumento('numeroDocumento'));
    }
    


    private function initialize()
    {
        //$this->recepcion->fechaRecepcionWMS = Carbon::now();
        //$this->recepcion->documentoDetalle = collect($this->recepcion->documentoDetalle);
    }

    public function cargarDatosSolicitudRecepcion()
    {
        /**
         * Busca la solicitud de recepción basándose en el número de documento.
         */
        //$this->solicitudRecepcion = wmscmguias::solicitudesPromo($this->recepcion->numeroDocumento);

        /**
         * Lanza una excepción si la solicitud de recepción no existe.
         */
        //if (!$this->solicitudRecepcion) {
        //    throw new CustomException("Solicitud de Recepcion no Existe: {$this->recepcion->numeroDocumento}", [], 500);
        //}

        /**
         * Busca la orden de compra.
         */
        //$this->ordenCompra = cmordcom::Orden($this->solicitudRecepcion->gui_ordcom);

        /**
         * Lanza una excepción si la orden de compra no existe.
         */
        //if (!$this->ordenCompra) {
        //    throw new CustomException("Orden de Compra no Existe: {$this->solicitudRecepcion->gui_ordcom}", [], 500);
        //}


        /**
         * Busca el proveedor.
         */
        //$this->proveedor = cmclientes::Cliente($this->solicitudRecepcion->gui_subcta);
    }
}
