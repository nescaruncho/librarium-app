<?php

namespace App\Models;

use App\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Model;

class SolicitudUnion extends Model
{
    protected $table = 'solicitud_union';

    protected $primaryKey = 'idSolicitudUnion';

    protected $fillable = [
        'idBiblioteca',
        'idUsuario',
        'estado'
    ];

    protected function casts()
    {
        return [
            'estado' => EstadoSolicitud::class
        ];
    }

    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }
}
