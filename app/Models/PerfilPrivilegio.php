<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPrivilegio extends Model
{
    protected $table = 'perfil_privilegio';
    public $timestamps = false;

    protected $fillable = ['id_perfil', 'id_privilegio'];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil');
    }

    public function privilegio()
    {
        return $this->belongsTo(Privilegio::class, 'id_privilegio');
    }
}
