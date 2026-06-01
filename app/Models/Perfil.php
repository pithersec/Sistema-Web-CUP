<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    public $timestamps = false;
    protected $table = 'perfil';
    protected $fillable = ['nombre', 'descripcion'];
}
