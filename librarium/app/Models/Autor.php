<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{

    use HasFactory;

    protected $table = 'autor';

    protected $primaryKey = 'idAutor';

    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2'
    ];

    public function libros()
    {
        return $this->belongsToMany(Libro::class, 'autorLibro', 'idAutor', 'idLibro');
    }
}
