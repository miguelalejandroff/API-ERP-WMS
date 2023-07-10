<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enlacepromo extends Model
{
    use HasFactory;

    protected $table = 'enlacepromo';

    protected $primaryKey = 'codigo_origen';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;
}
