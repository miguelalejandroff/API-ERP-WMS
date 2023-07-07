<?php

namespace App\ERP\Templates;

use App\WMS\Build\Templates;

class Cmguias extends Templates
{
    public function __construct(
        protected string $gui_numero,
        protected string $gui_tipgui,
        protected string $gui_fechag,
        protected string $gui_ordcom,
        protected string $gui_numrut,
        protected string $gui_digrut,
        protected string $gui_subcta,
        protected string $gui_nombre,
        protected string $gui_guipro,
        protected string $gui_facpro,
        protected string $gui_facals,
        protected string $gui_sucori,
        protected string $gui_sucdes,
        protected string $gui_paract,
        protected string $gui_fecmod,
        protected string $gui_codusu,
        protected string $gui_empres,
    ) {
    }
}
