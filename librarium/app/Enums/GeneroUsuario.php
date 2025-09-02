<?php

namespace App\Enums;

enum GeneroUsuario: string
{
    case MASCULINO = 'masculino';
    case FEMENINO = 'femenino';

    public function label(): string
    {
        return match ($this) {
            self::MASCULINO => 'Masculino',
            self::FEMENINO => 'Femenino',
        };
    }
}
