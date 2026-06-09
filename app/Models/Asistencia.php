<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencia';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'fecha', 'presente', 'codigo_postulante',
        'codigo_gestion', 'id_grupo', 'id_materia',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'codigo_postulante', 'codigo');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id');
    }
}