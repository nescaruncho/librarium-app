<?php

namespace App\Policies;

use App\Models\Notificacion;
use App\Models\Usuario;

class NotificacionPolicy
{

    public function viewAny(Usuario $usuario)
    {
        return true;
    }

    public function view(Usuario $usuario, Notificacion $notificacion)
    {
        return $notificacion->idUsuario === $usuario->idUsuario;
    }

    public function update(Usuario $usuario, Notificacion $notificacion)
    {
        return $notificacion->idUsuario === $usuario->idUsuario;
    }

    public function delete(Usuario $usuario, Notificacion $notificacion)
    {
        return $notificacion->idUsuario === $usuario->idUsuario;
    }
}
