<?php

namespace App\Http\Controllers;

use App\Enums\MiembroRol;
use App\Models\Biblioteca;
use App\Models\Ejemplar;
use App\Models\Libro;
use App\Services\BookDataService;
use App\Services\EtiquetaService;
use App\Services\NotificacionService;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;

class EjemplarController extends Controller
{

    public function create($idBiblioteca)
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $this->verificarPermisos($biblioteca);

        return Inertia::render('Bibliotecas/Ejemplares/Create', [
            'biblioteca' => $biblioteca,
        ]);
    }

public function store(Request $request, $idBiblioteca, BookDataService $bookDataService, EtiquetaService $etiquetaService, NotificacionService $notificacionService)
{
    Log::info('[EjemplarController] Iniciando store', [
        'idBiblioteca' => $idBiblioteca,
        'request_data' => $request->all()
    ]);

    $biblioteca = Biblioteca::with('configuracionetiqueta')->findOrFail($idBiblioteca);
    $this->verificarPermisos($biblioteca);

    $validated = $request->validate([
        'isbn'      => 'required|string|max:13',
        'ubicacion' => 'nullable|string',
    ]);

    Log::info('[EjemplarController] Datos validados', ['validated' => $validated]);

    return DB::transaction(function () use ($validated, $biblioteca, $bookDataService, $etiquetaService, $notificacionService, $idBiblioteca) {

        $isbn  = $validated['isbn'];
        Log::info('[EjemplarController] Buscando libro con ISBN', ['isbn' => $isbn]);

        $libro = Libro::where('isbn', $isbn)->first();

        if (!$libro) {
            Log::info('[EjemplarController] Libro no encontrado, creando nuevo');
            $libro = $bookDataService->obtenerOLibro($isbn);
            if (!$libro) {
                Log::error('[EjemplarController] No se pudo obtener libro del servicio');
                return back()->withErrors(['isbn' => 'No se ha encontrado ningún libro con ese ISBN']);
            }
            Log::info('[EjemplarController] Libro creado', ['libro_id' => $libro->idLibro]);
        } else {
            Log::info('[EjemplarController] Libro encontrado', ['libro_id' => $libro->idLibro]);
        }

        $libro->loadMissing(['autores', 'generos', 'editorial', 'idioma']);

        $etiqueta = null;
        if ($biblioteca->etiquetasHabilitadas) {
            if (!$biblioteca->configuracionetiqueta || empty($biblioteca->configuracionetiqueta->formato)) {
                Log::error('[EjemplarController] Configuración de etiquetas incorrecta');
                return back()->withErrors(['error' => 'Para generar etiquetas, configura el formato en la biblioteca.']);
            }
            $etiqueta = $etiquetaService->generarEtiqueta($libro, $biblioteca);
            Log::info('[EjemplarController] Etiqueta generada', ['etiqueta' => $etiqueta]);
        }

        Log::info('[EjemplarController] Creando ejemplar', [
            'idLibro' => $libro->idLibro,
            'idBiblioteca' => $idBiblioteca,
            'ubicacion' => $validated['ubicacion'] ?? null,
            'etiqueta' => $etiqueta
        ]);

        $ejemplar = Ejemplar::create([
            'idLibro'      => $libro->idLibro,
            'idBiblioteca' => $idBiblioteca,
            'ubicacion'    => $validated['ubicacion'] ?? null,
            'etiqueta'     => $etiqueta,
            'disponible'   => true,
        ]);

        Log::info('[Ejemplar creado]', ['id' => $ejemplar?->idEjemplar, 'attrs' => $ejemplar?->toArray()]);

        $notificacionService->libroBibliotecaCreado($libro->idLibro, $idBiblioteca);

        Log::info('[EjemplarController] Proceso completado exitosamente');

        return redirect()
            ->route('bibliotecas.show', $idBiblioteca)
            ->with('success', 'Ejemplar añadido exitosamente');
    });
}

    public function storeCopia(
        Request $request,
        $biblioteca,
        $libro,
        EtiquetaService $etiquetaService
    ) {

        $biblioteca = Biblioteca::with('configuracionetiqueta')->findOrFail($biblioteca);
        $this->verificarPermisos($biblioteca);

        $libro = Libro::findOrFail($libro);

        $request->validate([
            'ubicacion' => 'nullable|string',
        ]);

        $libro->loadMissing(['autores', 'generos', 'editorial', 'idioma']);

        $etiqueta = null;

        if ($biblioteca->etiquetasHabilitadas) {
            if (!$biblioteca->configuracionetiqueta || empty($biblioteca->configuracionetiqueta->formato)) {
                return back()->withErrors(['error' => 'Para generar etiquetas, configura el formato en la biblioteca.']);
            }
            $etiqueta = $etiquetaService->generarEtiqueta($libro, $biblioteca);
        }

        Ejemplar::create([
            'idLibro' => $libro->idLibro,
            'idBiblioteca' => $biblioteca->idBiblioteca,
            'ubicacion' => $request->ubicacion,
            'etiqueta' => $etiqueta,
            'disponible' => true,
        ]);

        return redirect()->route('libros.showEnBiblioteca', [
            'biblioteca' => $biblioteca->idBiblioteca,
            'idLibro' => $libro->idLibro,
        ])->with('success', 'Copia del ejemplar añadida exitosamente');
    }


    public function update(Request $request, $idBiblioteca, Ejemplar $ejemplar)
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $this->verificarPermisos($biblioteca);

        $request->validate([
            'ubicacion' => 'nullable|string',
            'disponible' => 'boolean',
        ]);

        $ejemplar->update($request->only('ubicacion', 'disponible'));

        return redirect()->route('bibliotecas.show', $idBiblioteca)
            ->with('success', 'Ejemplar actualizado exitosamente');
    }

    public function destroy($idBiblioteca, Ejemplar $ejemplar)
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $this->verificarPermisos($biblioteca);

        $ejemplar->delete();

        return redirect()->route('libros.showEnBiblioteca', [
            'biblioteca' => $idBiblioteca,
            'idLibro' => $ejemplar->idLibro,
        ])->with('success', 'Ejemplar eliminado exitosamente');
    }

    private function verificarPermisos(Biblioteca $biblioteca)
    {
        $usuarioId = auth()->user()->idUsuario;

        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol == MiembroRol::LECTOR) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $miembro;
    }

}
