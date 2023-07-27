<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enlrecpromocion extends Model
{
    use HasFactory;
    protected $table = 'enl_rec_promocion';

    protected $connection = 'informix';

    public $timestamps = false;
}
