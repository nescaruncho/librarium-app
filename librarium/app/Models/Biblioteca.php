<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biblioteca extends Model
{
    protected $table = 'biblioteca';

    protected $primaryKey = 'idBiblioteca';

    protected $fillable = [
        'idPropietario',
        'nombre',
        'descripcion',
        'visibilidad',
        'prestamosHabilitados',
        'etiquetasHabilitadas'
    ];

    protected function casts()
    {
        return [
            'visibilidad' => 'boolean',
            'prestamosHabilitados' => 'boolean',
            'etiquetasHabilitadas' => 'boolean'
        ];
    }


    public function propietario()
    {
        return $this->belongsTo(Usuario::class, 'idPropietario', 'idUsuario');
    }

    public function miembros()
    {
        return $this->hasMany(Miembro::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function ejemplares()
    {
        return $this->hasMany(Ejemplar::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudUnion::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function configuracionetiqueta()
    {
        return $this->hasOne(ConfiguracionEtiqueta::class, 'idBiblioteca', 'idBiblioteca');
    }

    public function politicaprestamo()
    {
        return $this->hasOne(PoliticaPrestamo::class, 'idBiblioteca', 'idBiblioteca');
    }

}
