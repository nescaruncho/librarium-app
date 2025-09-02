<?php

namespace App\Http\Controllers;

use App\Enums\EstadoLectura;
use App\Enums\EstadoSolicitud;
use App\Enums\MiembroRol;
use App\Http\Requests\StoreBibliotecaRequest;
use App\Http\Requests\UpdateBibliotecaSettingsRequest;
use App\Models\Biblioteca;
use App\Models\Ejemplar;
use App\Models\Lectura;
use App\Models\Miembro;
use App\Models\SolicitudUnion;
use App\Services\EtiquetaService;
use App\Services\NotificacionService;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BibliotecaController extends Controller
{
    public function create()
    {
        return Inertia::render('Bibliotecas/Create');
    }

    public function store(StoreBibliotecaRequest $request)
    {
        /**
         * @var  \App\Models\Usuario $user
         */
        $user = Auth::user();

        $biblioteca = Biblioteca::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'visibilidad' => $request->input('visibilidad'),
            'idPropietario' => $user->idUsuario,
        ]);

        Miembro::create([
            'idUsuario' => $user->idUsuario,
            'idBiblioteca' => $biblioteca->idBiblioteca,
            'rol' => MiembroRol::PROPIETARIO
        ]);

        return redirect()
            ->route('bibliotecas.show', $biblioteca)
            ->with('success', 'Biblioteca creada exitosamente.');
    }

    public function index()
    {
        $usuario = Auth::user();

        $miembros = Miembro::with('biblioteca')
            ->where('idUsuario', $usuario->idUsuario)
            ->get();

        $bibliotecas = $miembros->map(function ($miembro) {
            if (!$miembro->biblioteca)
                return null;

            $biblioteca = $miembro->biblioteca;
            $biblioteca->rol = $miembro->rol;
            return $biblioteca;
        })->filter();

        return Inertia::render('Bibliotecas/Index', [
            'bibliotecas' => $bibliotecas,
        ]);
    }


    public function show(Biblioteca $biblioteca)
    {

        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->with('usuario')->first();

        if (!$miembro) {
            return redirect()->route('bibliotecas.index')->with('error', 'No tienes acceso a esta biblioteca.');
        }

        $libros = $biblioteca->ejemplares()
            ->with('libro.autores')
            ->get()
            ->pluck('libro')
            ->unique('idLibro')
            ->values();

        $ids = $libros->pluck('idLibro')->all();

        if (!empty($ids)) {
            $lecturas = Lectura::query()
                ->select('idLibro', 'estado', 'fechaInicio', 'fechaFin')
                ->where('idUsuario', $usuarioId)
                ->whereIn('idLibro', $ids)
                ->orderByDesc('fechaInicio')
                ->get();

            $estadoPorLibro = [];

            foreach ($lecturas as $lectura) {
                $id = $lectura->idLibro;
                if (($estadoPorLibro[$id] ?? null) === EstadoLectura::LEYENDO->value) {
                    continue;
                }
                if ($lectura->estado === EstadoLectura::LEYENDO) {
                    $estadoPorLibro[$id] = EstadoLectura::LEYENDO->value;
                } elseif (!isset($estadoPorLibro[$id]) && $lectura->estado === EstadoLectura::COMPLETADO) {
                    $estadoPorLibro[$id] = EstadoLectura::COMPLETADO->value;
                } elseif (!isset($estadoPorLibro[$id]) && $lectura->estado === EstadoLectura::ABANDONADO) {
                    $estadoPorLibro[$id] = EstadoLectura::ABANDONADO->value;
                }

                $libros = $libros->map(function ($libro) use ($estadoPorLibro) {
                    $libro->estadoLectura = $estadoPorLibro[$libro->idLibro] ?? null;
                    return $libro;
                });
            }

            return Inertia::render('Bibliotecas/Show', [
                'biblioteca' => $biblioteca,
                'miembros' => $biblioteca->miembros()->with('usuario')->get(),
                'libros' => $libros,
                'rol' => $miembro->rol
            ]);
        }
    }

    public function edit(Biblioteca $biblioteca)
    {
        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol !== MiembroRol::PROPIETARIO) {
            return redirect()->route('bibliotecas.index')->with('error', 'No tienes permiso para editar esta biblioteca.');
        }

        return Inertia::render('Bibliotecas/Edit', [
            'biblioteca' => $biblioteca,
        ]);
    }

    public function update(StoreBibliotecaRequest $request, Biblioteca $biblioteca)
    {
        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol !== MiembroRol::PROPIETARIO) {
            return redirect()->route('bibliotecas.index')->with('error', 'No tienes permiso para editar esta biblioteca.');
        }

        $biblioteca->update([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'visibilidad' => $request->input('visibilidad'),
        ]);

        app(NotificacionService::class)
            ->bibliotecaActualizada($biblioteca->idBiblioteca);

        return redirect()
            ->route('bibliotecas.show', $biblioteca)
            ->with('success', 'Biblioteca actualizada exitosamente.');
    }

    public function destroy(Biblioteca $biblioteca)
    {
        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();
        if (!$miembro || $miembro->rol !== MiembroRol::PROPIETARIO) {
            return redirect()->route('bibliotecas.index')
                ->with('error', 'No tienes permiso para eliminar esta biblioteca.');
        }

        $payload = [
            'idBiblioteca' => $biblioteca->idBiblioteca,
            'nombre' => $biblioteca->nombre,
            'destinatarios' => $biblioteca->miembros()->pluck('idUsuario')->all(),
        ];

        $biblioteca->delete();

        app(NotificacionService::class)->bibliotecaEliminada($payload);

        return redirect()->route('bibliotecas.index')
            ->with('success', 'Biblioteca eliminada exitosamente.');
    }

    public function search(Request $request)
    {
        $usuarioId = Auth::user()->idUsuario;
        $query = trim($request->input('query', ''));
        $visibilidad = $request->input('visibilidad');
        $orden = $request->input('orden');

        $miembroships = Miembro::where('idUsuario', $usuarioId)
            ->select('idBiblioteca', 'rol')
            ->get()
            ->groupBy('rol')
            ->map->pluck('idBiblioteca');


        $bibliotecas = Biblioteca::query()
            ->where(function ($q) use ($query) {
                if (!empty($query)) {
                    $q->where('nombre', 'like', "%{$query}%")
                        ->orWhere('descripcion', 'like', "%{$query}%");
                }
            });

        if ($visibilidad !== null) {
            $bibliotecas->where('visibilidad', (bool) $visibilidad);
        }

        $bibliotecas->orderBy('nombre', $orden === 'desc' ? 'desc' : 'asc');

        $bibliotecas->with([
            'miembros' => function ($q) {
                $q->select('idMiembro', 'idBiblioteca', 'idUsuario', 'rol');
            }
        ]);

        $solicitudesPendientes = SolicitudUnion::where('idUsuario', auth()->user()->idUsuario)
            ->where('estado', EstadoSolicitud::PENDIENTE)
            ->pluck('idBiblioteca')
            ->toArray();

        return Inertia::render('Bibliotecas/SearchResults', [
            'bibliotecas' => $bibliotecas->get(),
            'query' => $request->input('query'),
            'visibilidad' => $request->input('visibilidad'),
            'orden' => $request->input('orden'),
            'miembroships' => $miembroships,
            'solicitudesPendientes' => $solicitudesPendientes
        ]);
    }

    public function editConfig(Biblioteca $biblioteca)
    {
        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol == MiembroRol::LECTOR) {
            return redirect()->route('bibliotecas.index')->with('error', 'No tienes permiso para editar la configuración de esta biblioteca.');
        }

        $biblioteca->load(['politicaprestamo', 'configuracionetiqueta']);

        return Inertia::render('Bibliotecas/EditConfig', [
            'biblioteca' => $biblioteca,
        ]);
    }

    public function updateConfig(UpdateBibliotecaSettingsRequest $request, Biblioteca $biblioteca)
    {
        $usuarioId = Auth::user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();
        if (!$miembro || $miembro->rol == MiembroRol::LECTOR) {
            return redirect()->route('bibliotecas.index')
                ->with('error', 'No tienes permiso para editar la configuración de esta biblioteca.');
        }

        $nuevoPrestamos = $request->boolean('prestamosHabilitados');
        $nuevasEtiquetas = $request->boolean('etiquetasHabilitadas');

        $prevPrestamos = (bool) $biblioteca->prestamosHabilitados;
        $prevEtiquetas = (bool) $biblioteca->etiquetasHabilitadas;

        $prevConfigEtiqueta = optional($biblioteca->configuracionetiqueta)->only([
            'formato',
            'separador',
            'longitudMaxima'
        ]);

        $payloadEtiqueta = [
            'formato' => array_values((array) $request->input('configEtiqueta.formato', [])),
            'separador' => (string) $request->input('configEtiqueta.separador', '-'),
            'longitudMaxima' => (int) $request->input('configEtiqueta.longitudMaxima', 12),
        ];

        $prevPolitica = optional($biblioteca->politicaprestamo)->only([
            'duracionMaxima',
            'maximoRenovaciones',
            'maximoEjemplares',
            'duracionRenovacion',
            'penalizacionDias'
        ]);

        $payloadPolitica = [
            'duracionPrestamoDias' => (int) $request->input('politica.duracionPrestamoDias'),
            'numeroMaxProrrogas' => (int) $request->input('politica.numeroMaxProrrogas'),
            'maxLibrosSimultaneos' => (int) $request->input('politica.maxLibrosSimultaneos'),
            'duracionProrrogaDias' => (int) $request->input('politica.duracionProrrogaDias'),
            'penalizacionDias' => (int) $request->input('politica.penalizacionDias'),
        ];

        DB::transaction(function () use ($biblioteca, $nuevoPrestamos, $nuevasEtiquetas, $prevPrestamos, $prevEtiquetas, $prevConfigEtiqueta, $payloadEtiqueta, $prevPolitica, $payloadPolitica) {
            $biblioteca->fill([
                'prestamosHabilitados' => $nuevoPrestamos,
                'etiquetasHabilitadas' => $nuevasEtiquetas,
            ]);
            if ($biblioteca->isDirty(['prestamosHabilitados', 'etiquetasHabilitadas'])) {
                $biblioteca->save();
            }

            if ($nuevasEtiquetas) {
                if (!$prevEtiquetas) {
                    $biblioteca->configuracionetiqueta()->updateOrCreate(
                        ['idBiblioteca' => $biblioteca->idBiblioteca],
                        $payloadEtiqueta
                    );
                    DB::afterCommit(fn() => app(NotificacionService::class)
                        ->configEtiquetasActivada($biblioteca->idBiblioteca));
                } else {
                    $cambiaEtiqueta = static function ($prev, $nuevo) {
                        if (!$prev)
                            return true;
                        return $prev['separador'] !== $nuevo['separador']
                            || (int) $prev['longitudMaxima'] !== (int) $nuevo['longitudMaxima']
                            || json_encode(array_values($prev['formato'] ?? [])) !== json_encode(array_values($nuevo['formato'] ?? []));
                    };
                    if ($cambiaEtiqueta($prevConfigEtiqueta, $payloadEtiqueta)) {
                        $config = $biblioteca->configuracionetiqueta()->updateOrCreate(
                            ['idBiblioteca' => $biblioteca->idBiblioteca],
                            $payloadEtiqueta
                        );
                        DB::afterCommit(fn() => app(NotificacionService::class)
                            ->{$config->wasRecentlyCreated ? 'configEtiquetasActivada' : 'configEtiquetasActualizada'}($biblioteca->idBiblioteca));
                    }
                }
            } elseif ($prevEtiquetas) {
                $biblioteca->configuracionetiqueta()->delete();
                DB::afterCommit(fn() => app(NotificacionService::class)
                    ->configEtiquetasEliminada($biblioteca->idBiblioteca));
            }

            if ($nuevoPrestamos) {
                $politica = $biblioteca->politicaprestamo()->firstOrNew([
                    'idBiblioteca' => $biblioteca->idBiblioteca,
                ]);

                $politica->fill($payloadPolitica);

                $wasNew = !$politica->exists;
                $politica->save();

                if ($wasNew) {
                    DB::afterCommit(
                        fn() =>
                        app(NotificacionService::class)->politPrestamoActivada($biblioteca->idBiblioteca)
                    );
                } elseif (
                    $politica->wasChanged([
                        'maxLibrosSimultaneos',
                        'duracionPrestamoDias',
                        'numeroMaxProrrogas',
                        'duracionProrrogaDias',
                        'penalizacionDias',
                    ])
                ) {

                    \Log::info('[Noti] prestamo ACTUALIZADA', [
                        'bib' => $biblioteca->idBiblioteca,
                        'changes' => $politica->getChanges(),
                    ]);
                    DB::afterCommit(
                        fn() =>
                        app(NotificacionService::class)->politPrestamoActualizada($biblioteca->idBiblioteca)
                    );
                }
            } elseif ($prevPrestamos) {
                $biblioteca->politicaprestamo()->delete();
                DB::afterCommit(
                    fn() =>
                    app(NotificacionService::class)->politPrestamoEliminada($biblioteca->idBiblioteca)
                );
            }

        });

        return redirect()->route('bibliotecas.show', $biblioteca)
            ->with('success', 'Configuración de la biblioteca actualizada exitosamente.');
    }
    public function regenerarEtiquetas(Biblioteca $biblioteca, EtiquetaService $etiquetaService)
    {
        $usuarioId = auth()->user()->idUsuario;
        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol == MiembroRol::LECTOR) {
            return redirect()->route('bibliotecas.index')->with('error', 'No tienes permiso para regenerar etiquetas en esta biblioteca.');
        }

        $biblioteca->load('configuracionetiqueta');

        if (
            !$biblioteca->etiquetasHabilitadas ||
            !$biblioteca->configuracionetiqueta ||
            empty($biblioteca->configuracionetiqueta->formato)
        ) {
            return back()->withErrors(['error' => 'Activa y configura las etiquetas antes de regenerarlas.']);
        }

        $procesados = 0;

        Ejemplar::where('idBiblioteca', $biblioteca->idBiblioteca)
            ->whereHas('libro')
            ->with(['libro.autores', 'libro.generos', 'libro.editorial', 'libro.idioma'])
            ->orderBy('idEjemplar')
            ->chunkById(200, function ($ejemplares) use ($etiquetaService, $biblioteca, &$procesados) {
                foreach ($ejemplares as $e) {
                    $nueva = $etiquetaService->generarEtiqueta($e->libro, $biblioteca);
                    $e->update(['etiqueta' => $nueva]);
                    $procesados++;
                }
            }, 'idEjemplar');

        return back()->with('success', "Etiquetas regeneradas: {$procesados}.");
    }
}

