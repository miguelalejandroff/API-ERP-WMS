<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmenlmon extends Model
{
    use HasFactory;

    protected $table = 'cmenlmon';

    protected $primaryKey = 'enl_ordcom';

    public $incrementing = false;

    public $timestamps = false;
}
