<?php

namespace App\Libs;

use App\WMS\Exception\HttpException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class WMS
{
    const DATE_FORMAT_WMS = 'Y-m-d\TH:i:s.u\Z';
    const DATE_FORMAT_DATE =  'Y-m-d';
    const DATE_FORMAT_CURRENT = 'Y-m-d H:i';

    protected $codTipo = [
        1 => "(OCN) Orden de Compra Nacional",
        2 => "(OCI) Orden de Compra Internacional",
        4 => "(TEB) Traspaso Entre Bodegas",
        5 => "(DVC) Devolución Cliente",
        6 => "(DVR) Devolución para Redespacho",
    ];
    protected $ordenEntrada = [
        01 => "(OC) Orden de Compra de Existencia",
        02 => "(OC) Orden de Compra Bonificada",
        13 => "(NC) Nota de Credito ",
        19 => "(NC) Nota de Credito Veterinaria Menor",
        37 => "(NC) Nota de Credito (> 90 dias)",
        38 => "(NC) Nota de Credito Veterinaria Menor (> 90 dias)",
    ];
    protected $ordenSalida = [
        05 => "(SG) Solicitud de Guia Santiago a Sucursal",
        06 => "(SG) Solicitud de Guia Sucursal a Sucursal",
        11 => "(SG) Solicitud de Guia Sucursal a Santiago",
        48 => "(SG) Solicitud de Guia de Venta a Otra Sucursal",
        03 => "(SP) Solicitud de pedido",
        99 => "(FC) Factura",
        96 => "(FC) Factura Veterinaria Menor",
        88 => "(BO) Boleta",
        49 => "(ND) Nota de Debito ",
    ];


    public function __construct(
        protected $url = 'http://198.1.1.122:1950/EnfasysWMS_Api/api/',
        protected $contentType = 'application/json',
        protected $dataAuth = 'HyQeUL1SUrJ5H+Da6zcFzq006RU5AJGr8hul/QcM6xURShqoS8Tt+Znjd7lc55bbVePq2NN0FErHaDCmdHY65w==',
        protected $headers = []
    ) {
        $this->headers = [
            'Content-Type' => $this->contentType,
            'dataAuth' => $this->dataAuth
        ];
    }
    public static function post($endpoint, $body, $self = new self)
    {
        $url = "{$self->url}{$endpoint}";
        try {
            $response = (object)Http::withHeaders($self->headers)->send('POST', $url, [
                'body' =>  $body->getContent()
            ])->json();


            if (isset($response->status) && $response->status >= 200 && $response->status < 300) {
                self::saveSuccessfulResponse($url, 'POST', $body, $response);
                return $response;
            } else {
                $trackingData = [
                    'url' => $url,
                    'payload' => $body->original,
                    'errors' => $response->errors,
                    'status' => $response->status,
                    'message' => $response->title
                ];
                throw new HttpException("Error en la respuesta: ", 0, $trackingData);
            }
        } catch (HttpException $e) {

            $e->saveToDatabase();

            return $response; // o cualquier valor predeterminado que desees.
        }
    }
    private static function saveSuccessfulResponse($url, $method, $body, $response)
    {
        // Inserta tu lógica para guardar la respuesta exitosa en la misma tabla de MongoDB
        $trackingData = [
            'url' => $url,
            'method' => $method,
            'payload' => $body->original,
            'errors' => $response->errors,
            'status' => $response->status,
            'message' => $response->title
        ];

        $request = Request::instance();
        $tracking = $request->attributes->get('tracking');
        $tracking->addTrackingData($trackingData);
    }

    public static function now($format = self::DATE_FORMAT_WMS)
    {
        return Carbon::now()->format($format);
    }
    
    public static function nowYear($format = self::DATE_FORMAT_WMS)
    {
        return Carbon::now()->addYear()->format($format);
    }

    public static function date($date, $oldFormat = self::DATE_FORMAT_DATE, $newFormat = self::DATE_FORMAT_WMS)
    {
        return  Carbon::createFromFormat($oldFormat, $date)->format($newFormat);
    }
}
