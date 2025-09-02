<?php

namespace App\Enums;

enum EstadoLectura: string
{
    case LEYENDO = 'leyendo';
    case COMPLETADO = 'completado';
    case ABANDONADO = 'abandonado';

    public function label(): string
    {
        return match ($this) {
            self::LEYENDO => 'Leyendo',
            self::COMPLETADO => 'Completado',
            self::ABANDONADO => 'Abandonado',
        };
    }
}
