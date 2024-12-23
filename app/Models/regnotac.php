<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regnotac extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $table = 'regnotac';

    protected $connection = 'informix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "nrofac",
        "nrocre",
        "codpro",
        "cantid",
    ];

}
