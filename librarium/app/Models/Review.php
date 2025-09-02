<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    protected $table = 'review';

    protected $primaryKey = 'idReview';

    protected $fillable = [
        'idLectura',
        'valoracion',
        'titulo',
        'contenido',
    ];

    public function lectura()
    {
        return $this->belongsTo(Lectura::class, 'idLectura', 'idLectura');
    }
}
