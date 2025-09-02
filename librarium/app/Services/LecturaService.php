<?php

namespace App\Services;

use App\Enums\EstadoLectura;
use App\Models\Ejemplar;
use App\Models\Lectura;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LecturaService
{
    public function startReading(int $userId, int $ejemplarId): Lectura
    {
        return DB::transaction(function () use ($userId, $ejemplarId) {
            /** @var Ejemplar $ejemplar */
            $ejemplar = Ejemplar::query()->with('libro:idLibro')->findOrFail($ejemplarId);

            $abierta = Lectura::query()
                ->where('idUsuario', $userId)
                ->where('idLibro', $ejemplar->idLibro)
                ->where('estado', EstadoLectura::LEYENDO)
                ->first();

            if ($abierta) {
                return $abierta;
            }

            $reutilizable = Lectura::query()
                ->where('idUsuario', $userId)
                ->where('idLibro', $ejemplar->idLibro)
                ->whereIn('estado', [EstadoLectura::COMPLETADO, EstadoLectura::ABANDONADO])
                ->latest('fechaFin')
                ->first();

            if ($reutilizable) {
                $reutilizable->update([
                    'estado'      => EstadoLectura::LEYENDO,
                    'fechaInicio' => Carbon::now(),
                    'fechaFin'    => null,
                ]);
                return $reutilizable;
            }

            return Lectura::create([
                'idUsuario'   => $userId,
                'idLibro'     => $ejemplar->idLibro,
                'fechaInicio' => Carbon::now(),
                'estado'      => EstadoLectura::LEYENDO,
            ]);
        });
    }

    public function markFinished(int $userId, int $ejemplarId): Lectura
    {
        return DB::transaction(function () use ($userId, $ejemplarId) {
            $ejemplar = Ejemplar::query()->with('libro:idLibro')->findOrFail($ejemplarId);

            $lectura = Lectura::query()
                ->where('idUsuario', $userId)
                ->where('idLibro', $ejemplar->idLibro)
                ->where('estado', EstadoLectura::LEYENDO)
                ->latest('fechaInicio')
                ->first();

            if (!$lectura) {
                abort(422, 'No hay una lectura en curso para marcar como leída.');
            }

            $lectura->update([
                'estado'   => EstadoLectura::COMPLETADO,
                'fechaFin' => Carbon::now(),
            ]);

            return $lectura;
        });
    }

    public function markAbandoned(int $userId, int $ejemplarId): Lectura
    {
        return DB::transaction(function () use ($userId, $ejemplarId) {
            $ejemplar = Ejemplar::query()->with('libro:idLibro')->findOrFail($ejemplarId);

            $lectura = Lectura::query()
                ->where('idUsuario', $userId)
                ->where('idLibro', $ejemplar->idLibro)
                ->where('estado', EstadoLectura::LEYENDO)
                ->latest('fechaInicio')
                ->first();

            if (! $lectura) {
                abort(422, 'Solo puedes abandonar una lectura que está en curso.');
            }

            $lectura->update([
                'estado'   => EstadoLectura::ABANDONADO,
                'fechaFin' => now(),
            ]);

            return $lectura;
        });
    }
}
