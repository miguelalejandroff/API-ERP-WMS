<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cmguinum extends Model
{
    use HasFactory;

    protected $table = 'cmguinum';

    protected $primaryKey = 'gui_numero';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'gui_numero',
        'gui_fechag',
        'gui_bodori'
    ];

}
