<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tablaparam extends Model
{
    use HasFactory;
    protected $table = 'tabla_param';

    protected $connection = 'informix';

    public $timestamps = false;
}
