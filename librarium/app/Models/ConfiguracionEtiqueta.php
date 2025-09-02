<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionEtiqueta extends Model
{
    protected $table = 'configuracionetiqueta';

    protected $primaryKey = 'idConfiguracionEtiqueta';

    protected $fillable = [
        'idBiblioteca',
        'formato',
        'longitudMaxima',
        'separador'
    ];

    protected function casts()
    {
        return [
            'formato' => 'array',
            'longitudMaxima' => 'integer'
        ];
    }

    public function biblioteca()
    {
        return $this->belongsTo(Biblioteca::class, 'idBiblioteca', 'idBiblioteca');
    }
}
