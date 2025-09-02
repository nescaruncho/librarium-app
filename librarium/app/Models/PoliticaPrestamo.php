<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliticaPrestamo extends Model
{
    protected $table = 'politicaprestamo';

    protected $primaryKey = 'idPoliticaPrestamo';

    protected $fillable = [
        'idBiblioteca',
        'maxLibrosSimultaneos',
        'duracionPrestamoDias',
        'numeroMaxProrrogas',
        'duracionProrrogaDias',
        'penalizacionDias'
    ];



    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class, 'idBiblioteca', 'idBiblioteca');
    }
}
