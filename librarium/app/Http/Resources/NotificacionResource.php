<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->idNotificacion ?? $this->id, // según tu PK
            'titulo'    => $this->titulo,
            'mensaje'   => $this->mensaje,
            'tipo'      => $this->tipo,
            'accion'    => $this->accion instanceof \BackedEnum ? $this->accion->value : $this->accion,
            'datosExtra'=> $this->datosExtra,
            'leido'     => (bool) $this->leido,
            // el gateway:
            'go_url'    => route('notificaciones.go', $this->resource),
        ];
    }
}
