<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vpparsis extends Model
{
    use HasFactory;
    protected $table = 'vpparsis';

    protected $connection = 'informix';

    public $timestamps = false;
}
