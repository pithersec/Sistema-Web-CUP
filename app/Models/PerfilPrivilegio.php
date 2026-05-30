<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPrivilegio extends Model
{
    protected $table = 'perfil_privilegio';
    public $timestamps = false; // No timestamps
    protected $fillable = ['id_perfil', 'id_privilegio'];
}
