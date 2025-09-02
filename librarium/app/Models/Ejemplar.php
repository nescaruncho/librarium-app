<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejemplar extends Model
{

    protected $table = 'ejemplar';

    protected $primaryKey = 'idEjemplar';

    protected $fillable = [
        'idBiblioteca',
        'idLibro',
        'etiqueta',
        'disponible',
        'ubicacion'
    ];

    protected function casts()
    {
        return [
            'disponible' => 'boolean'
        ];
    }

    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'idLibro', 'idLibro');
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'idEjemplar', 'idEjemplar');
    }

    public function prestamoActivo()
    {
        return $this->hasOne(Prestamo::class, 'idEjemplar', 'idEjemplar')
            ->whereNull('fechaDevolucion');
    }

}