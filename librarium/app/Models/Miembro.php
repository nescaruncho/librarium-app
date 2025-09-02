<?php

namespace App\Models;

use App\Enums\MiembroRol;
use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $table = 'miembro';

    protected $primaryKey = 'idMiembro';

    protected $fillable = [
        'idUsuario',
        'idBiblioteca',
        'rol'
    ];

    protected function casts()
    {
        return [
            'rol' => MiembroRol::class
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'idMiembro', 'idMiembro');
    }
}
