<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enlaceprd extends Model
{
    use HasFactory;
    protected $table = 'enlaceprd';

    protected $connection = 'informix';

    public $timestamps = false;


}

