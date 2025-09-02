<?php

namespace App\Enums;

enum EstadoSolicitud: string
{
    case PENDIENTE = 'pendiente';
    case ACEPTADA = 'aceptada';
    case RECHAZADA = 'rechazada';

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::ACEPTADA => 'Aceptada',
            self::RECHAZADA => 'Rechazada',
        };
    }
}
