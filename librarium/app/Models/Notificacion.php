<?php

namespace App\Models;

use App\Enums\TipoAccionNotificacion;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';

    protected $primaryKey = 'idNotificacion';

    protected $fillable = [
        'idUsuario',
        'tipo',
        'titulo',
        'mensaje',
        'accion',
        'datosExtra',
        'leido'
    ];

    protected function casts()
    {
        return [
            'leido' => 'boolean',
            'accion' => TipoAccionNotificacion::class,
            'datosExtra' => 'array'
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function scopeParaUsuario($query, $idUsuario)
    {
        return $query->where('idUsuario', $idUsuario);
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leido', false);
    }
}
