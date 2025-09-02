<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenQR extends Model
{
    protected $table = 'token_qr';

    protected $primaryKey = 'idTokenQR';

    protected $fillable = [
        'idEjemplar',
        'token'
    ];

    public function ejemplar()
    {
        return $this->belongsTo(Ejemplar::class, 'idEjemplar', 'idEjemplar');
    }
    public function getTokenAttribute($value)
    {
        return decrypt($value);
    }
    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = encrypt($value);
    }
}
