<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    use HasFactory;

    /**
     * La tabla asociado con el modelo.
     *
     * @var string
     */
    protected $table = 'my_flights';
    /**
     * La llave primaria asociada con el modelo.
     *
     * @var string
     */
    protected $primaryKey = 'flight_id';
    /**
     * Indica si el IDs es auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    /**
     * El "tipo" de el ID auto-incrementing.
     *
     * @var string
     */
    protected $keyType = 'string';
    /**
     * Indica si el modelo debe de tener timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * El formato de almacenamiento de la columna date en el modelo.
     *
     * @var string
     */
    protected $dateFormat = 'U';
    /**
     * si quieres personalizar los nombres de las columnas CREATED_AT o UPDATED_AT
     */
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
    /**
     * El nombre de conexion para el modelo
     *
     * @var string
     */
    protected $connection = 'connection-name';
    /**
     * Los valores predeterminados del modelo.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = ['name'];
    /**
     * Los atributos que no son asignables en masa.
     *
     * @var array
     */
    protected $guarded = ['price'];
    /**
     * Las relaciones que siempre deben estar cargadas.
     *
     * @var array
     */
    protected $with = ['author'];
    /**
     * relacion uno a uno
     * se obtiene el numero de telefono relacionado al usuario
     */
    public function phone()
    {
        /**
         * puede indicar la llave foranea
         * puede indicar la llave primaria
         */
        return $this->hasOne(Phone::class, 'foreign_key', 'local_key');
    }
    /**
     * relacion inversa del modelo uno a uno
     * se obtiene el usuario relacionado al numero de telefono
     */
    public function user()
    {
        /**
         * puede indicar la llave foranea
         * puede indicar la llave primaria de la tabla principal
         */
        return $this->belongsTo(User::class, 'foreign_key', 'owner_key');
    }

    /**
     * relacion de uno a muchos
     * se obtiene todos los comentarios de una publicacion
     */
    public function comments()
    {
        /**
         * puede indicar la llave foranea
         * puede indicar la llave primaria
         */
        return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    }
    /**
     * relacion inversa del modelo uno a muchos
     * se obtiene la publicacion de los comentarios
     */
    public function post()
    {
        /**
         * puede indicar la llave foranea
         * puede indicar la llave primaria de la tabla principal
         */
        return $this->belongsTo(Post::class, 'foreign_key', 'owner_key');
    }
    /**
     * relacion de muchos a muchos
     * busca todos los roles que tengan cierto usuario
     */
    public function roles()
    {
        /**
         * nombre de la tabla intermedia
         * nombre de la columna externa intermedia
         * nombre de la columna externa en el que se une
         */
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
    /**
     * relacion inversa del modelo de muchos a muchos
     * busca todos los usuarios que tengan cierto rol
     */
    public function users()
    {
        /**
         * nombre de la tabla intermedia
         * nombre de la columna externa intermedia
         * nombre de la columna externa en el que se une
         */
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
