<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    public $timestamps = false;
    protected $table = 'perfil';
    protected $fillable = ['nombre', 'descripcion'];

    public function privilegios()
    {
        return $this->belongsToMany(Privilegio::class, 'perfil_privilegio', 'id_perfil', 'id_privilegio');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_perfil');
    }
}
