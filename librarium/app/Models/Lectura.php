<?php

namespace App\Models;

use App\Enums\EstadoLectura;
use Illuminate\Database\Eloquent\Model;

class Lectura extends Model
{
    protected $table = 'lectura';

    protected $primaryKey = 'idLectura';

    protected $fillable = [
        'idUsuario',
        'idLibro',
        'fechaInicio',
        'fechaFin',
        'estado'
    ];

    protected function casts()
    {
        return [
            'fechaInicio' => 'datetime',
            'fechaFin' => 'datetime',
            'estado' => EstadoLectura::class
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'idLibro', 'idLibro');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'idLectura', 'idLectura');
    }
}
