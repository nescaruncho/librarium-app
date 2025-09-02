<?php

namespace App\Models;

use App\Enums\GeneroUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'username',
        'email',
        'passwordhash',
        'nombre',
        'apellido1',
        'apellido2',
        'fecha_nacimiento',
        'genero',
        'ciudad',
        'descripcion',
        'privacidad',
        'notifEmail',
        'temaOscuro',
        'email_verified_at'
    ];

    protected $hidden = [
        'passwordhash',
        'remember_token',
    ];

    protected function casts()
    {
        return [
            'fecha_nacimiento' => 'datetime',
            'genero' => GeneroUsuario::class,
            'email_verified_at' => 'datetime',
            'privacidad' => 'boolean',
            'notifEmail' => 'boolean',
            'temaOscuro' => 'boolean'
        ];
    }

    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    public function bibliotecasPropietario()
    {
        return $this->hasMany(Biblioteca::class, 'idPropietario', 'idUsuario');
    }

    public function miembro()
    {
        return $this->hasMany(Miembro::class, 'idUsuario', 'idUsuario');
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudUnion::class, 'idUsuario', 'idUsuario');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'idUsuario', 'idUsuario');
    }

    public function listaslectura()
    {
        return $this->hasMany(ListaLectura::class, 'idUsuario', 'idUsuario');
    }

    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'idUsuario', 'idUsuario');
    }

}
