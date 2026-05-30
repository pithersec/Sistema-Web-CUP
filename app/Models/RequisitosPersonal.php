<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitosPersonal extends Model
{
    protected $table = 'requisitos_personal';

    protected $fillable = [
        'registro_personal',
        'area',
        'nivel_grado',
        'nivel_exp',
        'maestria',
        'doctorado',
        'diplomado',
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
