<?php

namespace App\ERP\Procesos;


class Guia
{
    /**
     * model = despacho_encab
     */
    public function __construct($model, protected $precioGuia = 0, protected $subTotal = 0)
    {
        foreach ($model->despachoobserva as &$row) {
            //if ($row->guia_ok = "s") {
            $precioGuia = round($row->cmproductos->pro_prevta, 2);

            if ((in_array($row->des_tipo, ["48", "17"])) and $model->des_facals > 0) {

                $precioGuia = round($row->cmfacdet->fac_preuni, 2);
            }
            //}
            $subTotal += round($row->des_canrep * $precioGuia, 2);


            if ($row->des_tipo = "39" and (in_array($model->des_marcaItem, ["10", "11", "13"]))) {
                if ($row->des_descri and $row->des_descri != "                          ") {
                    if ($row->des_canrep >= "1" and is_null($row->des_preuni)) {
                        $row->des_preuni = 1;
                    }
                    //call baja_detalle_guias

                }
            } else {
            }
            //call baja_encabezado_guias

        }
    }
}
