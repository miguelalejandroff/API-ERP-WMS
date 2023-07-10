<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmenlbon extends Model
{
    use HasFactory;

    protected $table = 'cmenlbon';

    protected $primaryKey = 'bon_ordori';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;
}
