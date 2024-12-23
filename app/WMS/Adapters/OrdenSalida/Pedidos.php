<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\WMS;
use App\WMS\Contracts\Outbound\OrdenSalidaDetalleService;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use App\WMS\Adapters\Admin\CreateCliente;
use App\Models\cmclientes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Casts\CorrelativoUnicoCast;


class Pedidos extends OrdenSalidaService
{

    protected $model;
    protected $ultimoDigito;

    public function __construct($model, $ultimoDigito)
    {
        parent::__construct($model);
        $this->model = $model;
        $this->ultimoDigito = $ultimoDigito;
    }

    protected function codDeposito($model): string
    {
        if($this->ultimoDigito == 'M') {
            return "2";
        }else {
            return "56";
        }
    }
    protected function nroOrdenSalida($model): string
    {
        $model->load('pedidosdetalles');
        $concatenation = CorrelativoUnicoCast::generateConcatenation($model->ped_folio, $this->ultimoDigito);
        Log::info("CorrelativoUnicoCast - nroOrdenSalidaEncabezado: $concatenation");

        return $concatenation;
    }

    public function nroOrdenCliente($model): string
    {
        return $model->ped_folio;
    }

    protected function tipoOrdenSalida($model): int
    {
        return 16;
    }

    public function nroReferencia($model): string
    {
        return $model->ped_folio;
    }

    public function nroReferencia2($model): string
    {
        return "P";
    }

    public function codCliente($model): ?string
    {
        return 120320;
    }

    public function codSucursal($model): ?string
    {
        $bodega = $model->bodegaOrigen()->where('bod_divisi', 'C')->first();
        return $bodega ? $bodega->bod_codigo : null; 
    }
    
    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->ped_fecha, 'Y-m-d');
    }

    public function observacion($model): string
    {
        $detalle = $model->pedidosdetalles
            ->where('ped_codrub', $this->ultimoDigito)
            ->first();

        return $detalle ? $detalle->ped_nomrubro : '';
    }

    public function OrdenSalidaDetalle($model): Collection
    {
        $ultimoDigito = $this->ultimoDigito;

        $detalles = $model->pedidosdetalles
            ->filter(function ($detalle) use ($ultimoDigito) {
                $filterResult = $detalle->ped_estped === 'A' && $detalle->ped_codrub == $ultimoDigito;
                if (!$filterResult) {
                    Log::info('Filtrado no aplicado para detalle:', ['detalle' => $detalle->toArray()]);
                }
                return $filterResult;
            });

            return $detalles->mapToGroups(function ($detalle) use ($ultimoDigito) {
                $detalle = new class($detalle, $ultimoDigito) extends OrdenSalidaDetalleService
                {
                    private $ultimoDigito;
            
                    public function __construct($detalle, $ultimoDigito)
                    {
                        parent::__construct($detalle);
                        $this->ultimoDigito = $ultimoDigito;
                    }
                

                protected function codDeposito($model): string
                {
                    if($this->ultimoDigito == 'M') {
                        return "2";
                    }else {
                        return "56";
                    }
                }

                protected function nroOrdenSalida($model): string
                {
                    $concatenation = CorrelativoUnicoCast::generateConcatenation($model->ped_folio, $model->ped_codrub);
                    Log::info("CorrelativoUnicoCast - nroOrdenSalidaDetalle: $concatenation");

                    return $concatenation;
                }


                public function codItem($model): string
                {
                    return $model->ped_codigo;
                }



                public function cantidad($model): float
                {
                    return $model->ped_cantsol;
                }
            };
            return [$detalle->get()];
        })->collapse();
    }

    public function cliente($model) //distinto de 16. Para 16 es des_subcta
    {
        return (new CreateCliente($model->cmclientes ?? cmclientes::where('aux_claves', 120320)->first()))->get(); //preguntar Miguel
    }
}
