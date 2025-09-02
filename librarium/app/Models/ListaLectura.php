<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaLectura extends Model
{

    protected $table = 'lista_lectura';

    protected $primaryKey = 'idListaLectura';

    protected $fillable = [
        'idUsuario',
        'nombre',
        'descripcion'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function ejemplares()
    {
        return $this->belongsToMany(Ejemplar::class, 'lista_lectura_ejemplar', 'idListaLectura', 'idEjemplar')
            ->withTimestamps();
    }

    

}
