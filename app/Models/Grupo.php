<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'aula', 'turno', 'horario', 'total_ins', 'codigo_gestion',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'codigo_gestion', 'codigo');
    }
}
