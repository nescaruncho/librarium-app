<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;

    protected $table = 'libro';

    protected $primaryKey = 'idLibro';

    protected $fillable = [
        'titulo',
        'idEditorial',
        'isbn',
        'fechaPublicacion',
        'numeroPaginas',
        'sinopsis',
        'portadaUrl',
        'idIdioma',
    ];

    protected function casts()
    {
        return [
            'fechaPublicacion' => 'datetime',
        ];
    }

    public function editorial()
    {
        return $this->belongsTo(Editorial::class, 'idEditorial', 'idEditorial');
    }

    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'idIdioma', 'idIdioma');
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autorLibro', 'idLibro', 'idAutor');
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'generoLibro', 'idLibro', 'idGenero');
    }

    public function ejemplares()
    {
        return $this->hasMany(Ejemplar::class, 'idLibro', 'idLibro');
    }

    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'idLibro', 'idLibro');
    }

    

}
