<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmordobs extends Model
{
    use HasFactory;

    protected $table = 'cmordobs';

    protected $primaryKey = 'ord_numord';

    public $incrementing = false;

    public $timestamps = false;

    public function cmordcom()
    {
        return $this->belongsTo(cmordcom::class, 'ord_numcom', 'ord_numord');
    }
}
