<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table = 'prestamo';

    protected $primaryKey = 'idPrestamo';

    protected $fillable = [
        'idEjemplar',
        'idMiembro',
        'fechaInicio',
        'fechaFin',
        'fechaDevolucion',
        'prorrogasUsadas',
        'devuelto'
    ];

    protected function casts()
    {
        return [
            'fechaInicio' => 'datetime',
            'fechaFin' => 'datetime',
            'fechaDevolucion' => 'datetime',
            'devuelto' => 'boolean'
        ];
    }

    public function ejemplar()
    {
        return $this->belongsTo(Ejemplar::class, 'idEjemplar', 'idEjemplar');
    }

    public function miembro()
    {
        return $this->belongsTo(Miembro::class, 'idMiembro', 'idMiembro');
    }
}
