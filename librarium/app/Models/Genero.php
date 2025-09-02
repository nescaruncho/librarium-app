<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'genero';

    protected $primaryKey = 'idGenero';

    protected $fillable = [
        'nombre',
        'idGeneroPadre'
    ];

    public function generoPadre()
    {
        return $this->belongsTo(Genero::class, 'idGeneroPadre', 'idGenero');
    }

    public function subgeneros()
    {
        return $this->hasMany(Genero::class, 'idGeneroPadre', 'idGenero');
    }
    
    public function generoLibro()
    {
        return $this->belongsToMany(Libro::class, 'generoLibro', 'idGenero', 'idLibro');
    }
}
