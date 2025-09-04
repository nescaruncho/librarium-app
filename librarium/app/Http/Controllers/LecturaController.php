<?php

namespace App\Http\Controllers;

use App\Enums\EstadoLectura;
use App\Models\Lectura;
use App\Services\LecturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LecturaController extends Controller
{
    public function __construct(private LecturaService $service)
    {
    }

    public function index(Request $r)
    {
        $estado = $r->string('estado')->toString();
        $permitidos = [EstadoLectura::LEYENDO->value, EstadoLectura::COMPLETADO->value];

        $q = Lectura::query()
            ->with(['libro:idLibro,titulo,portadaUrl,isbn',
                                'libro.autores:idAutor,nombre,apellido1'])
            ->where('idUsuario', auth()->id())
            ->when(in_array($estado, $permitidos, true), fn($qq) => $qq->where('estado', $estado))
            ->when(($estado ?: null) === EstadoLectura::LEYENDO->value, fn($qq) => $qq->orderByDesc('fechaInicio'))
            ->when(($estado ?: null) === EstadoLectura::COMPLETADO->value, fn($qq) => $qq->orderByDesc('fechaFin'));

        return inertia('Lecturas/Index', [
            'estado' => in_array($estado, $permitidos, true) ? $estado : EstadoLectura::LEYENDO->value,
            'lecturas' => $q->paginate(20)->withQueryString(),
        ]);
    }

    public function leyendo(Request $r)
    {
        $r->merge(['estado' => EstadoLectura::LEYENDO->value]);
        return $this->index($r);
    }
    public function leidos(Request $r)
    {
        $r->merge(['estado' => EstadoLectura::COMPLETADO->value]);
        return $this->index($r);
    }

    private function pickEjemplarIdForUserLibro(int $userId, int $idLibro, ?int $idBiblioteca = null): int
    {
        $q = DB::table('ejemplar')
            ->join('miembro', 'miembro.idBiblioteca', '=', 'ejemplar.idBiblioteca')
            ->where('miembro.idUsuario', $userId)
            ->where('ejemplar.idLibro', $idLibro);

        if ($idBiblioteca) {
            $q->where('ejemplar.idBiblioteca', $idBiblioteca);
        }

        $ejemplares = $q->pluck('ejemplar.idEjemplar');

        abort_if($ejemplares->isEmpty(), 403, 'No tienes ejemplares de este libro.');

        return (int) $ejemplares->first();
    }

    public function start(Request $r, int $idLibro)
    {
        $idBib = $r->integer('idBiblioteca') ?: null;

        $this->authorize('accessLibroViaEjemplar', [$idLibro, $idBib]);

        $ejemplarId = $this->pickEjemplarIdForUserLibro(auth()->id(), $idLibro, $idBib);

        $lectura = $this->service->startReading(auth()->id(), $ejemplarId);
        return back(303)->with('success', 'Lectura iniciada');
    }

    public function finish(Request $r, int $idLibro)
    {
        $idBib = $r->integer('idBiblioteca') ?: null;
        $this->authorize('accessLibroViaEjemplar', [$idLibro, $idBib]);

        $ejemplarId = $this->pickEjemplarIdForUserLibro(auth()->id(), $idLibro, $idBib);

        $this->service->markFinished(auth()->id(), $ejemplarId);

        return back(303)->with('success', 'Marcado como leído');
    }

    public function abandon(Request $r, int $idLibro)
    {
        $idBib = $r->integer('idBiblioteca') ?: null;
        $this->authorize('accessLibroViaEjemplar', [$idLibro, $idBib]);

        $ejemplarId = $this->pickEjemplarIdForUserLibro(auth()->id(), $idLibro, $idBib);

        $this->service->markAbandoned(auth()->id(), $ejemplarId);

        return back(303)->with('success', 'Lectura abandonada');
    }
}
