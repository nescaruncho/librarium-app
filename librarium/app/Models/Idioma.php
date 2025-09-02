<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idioma';

    protected $primaryKey = 'idIdioma';

    protected $fillable = [
        'nombre',
        'codigo'
    ];

    public function libros()
    {
        return $this->hasMany(Libro::class, 'idIdioma', 'idIdioma');
    }
}
