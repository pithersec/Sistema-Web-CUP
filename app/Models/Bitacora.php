<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;
    protected $table = 'bitacora'; 

    protected $fillable = [
        'ip',
        'accion',
        'fecha_hora',
        'id_usuario',
    ];

    protected $casts = ['fecha_hora' => 'datetime'];

    public function getCreatedAtColumn()
    {
        return 'fecha_hora';
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }
}