<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosPersonales extends Model
{
    public $timestamps = false;
    protected $table = 'datos_personales'; // Especifica el nombre de la tabla
    protected $primaryKey = 'ci'; // Especifica la clave primaria
    public $incrementing = false; // Indica que la clave primaria no es auto-incrementable
    protected $keyType = 'string'; // Especifica el tipo de la clave primaria

    protected $fillable = [
        'ci',
        'nombre',
        'apellido',
        'genero',
        'telefono',
        'correo',
        'fecha_nac',
        'direccion'
    ];

    protected $casts = ['fecha_nac' => 'date'];

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'ci', 'ci');
    }

    public function personal()
    {
        return $this->hasOne(Personal::class, 'ci', 'ci');
    }
}
