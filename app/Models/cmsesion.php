<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmsesion extends Model
{
    use HasFactory;
    protected $table = 'cmsesion';

    protected $connection = 'informix';
    public $timestamps = false;
}
