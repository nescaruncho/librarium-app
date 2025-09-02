<?php

namespace App\Enums;

enum MiembroRol: string
{
    case PROPIETARIO = 'propietario';
    case ADMIN = 'admin';
    case LECTOR = 'lector';

    public function label(): string
    {
        return match ($this) {
            self::PROPIETARIO => 'Propietario',
            self::ADMIN => 'Administrador',
            self::LECTOR => 'Lector',
        };
    }
}
