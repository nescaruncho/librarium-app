<?php

namespace App\Http\Controllers;

use App\Models\Biblioteca;
use App\Models\Ejemplar;
use App\Models\Libro;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LibroController extends Controller
{
    public function show($idLibro)
    {
        $libro = Libro::with(['autores', 'generos', 'editorial', 'idioma'])->findOrFail($idLibro);
        return Inertia::render('Libros/Show', [
            'libro' => $libro
        ]);
    }

    public function showEnBiblioteca(Biblioteca $biblioteca, $idLibro)
    {

        $usuarioId = auth()->user()->idUsuario;
        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro) {
            return redirect()->back()
                ->with('error', 'No tienes acceso a esta biblioteca.');
        }

        $libro = Libro::with(['autores', 'generos', 'editorial', 'idioma'])->findOrFail($idLibro);

        $ejemplares = $libro->ejemplares()
            ->where('idBiblioteca', $biblioteca->idBiblioteca)
            ->orderBy('etiqueta')
            ->get(['idEjemplar', 'etiqueta', 'ubicacion', 'disponible']);

        return Inertia::render('Bibliotecas/Libros/Show', [
            'biblioteca' => $biblioteca,
            'rol' => $miembro->rol,
            'libro' => [
                'idLibro' => $libro->idLibro,
                'titulo' => $libro->titulo,
                'portadaUrl' => $libro->portadaUrl,
                'isbn' => $libro->isbn,
                'fechaPublicacion' => $libro->fechaPublicacion,
                'editorial' => $libro->editorial->nombre,
                'idioma' => $libro->idioma?->nombre,
                'autores' => $libro->autores->map(fn($a)=>trim($a->nombre.' '.($a->apellido1??'')))->values(),
                'generos' => $libro->generos->pluck('nombre')->values(),
                'sinopsis' => $libro->sinopsis
            ],
            'ejemplares' => $ejemplares
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $libros = Libro::with(['autores', 'editorial', 'idioma'])
            ->where('titulo', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->orWhereHas('autores', function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%");
            })
            ->get();

        return Inertia::render('Libros/SearchResults', [
            'libros' => $libros,
            'query' => $query
        ]);
    }

    public function searchEnBiblioteca(Request $request, $idBiblioteca)
    {
        $usuarioId = auth()->user()->idUsuario;
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro) {
            return response()->json(['error' => 'No tienes acceso a esta biblioteca.'], 403);
        }

        $query = $request->input('query');

        // Si la consulta es muy corta, no buscar
        if (strlen($query) < 2) {
            return response()->json(['libros' => []], 200);
        }

        $ejemplares = Ejemplar::with(['libro.autores', 'libro.editorial', 'libro.generos'])
            ->where('idBiblioteca', $idBiblioteca)
            ->whereHas('libro', function ($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                    ->orWhereHas('autores', function ($q2) use ($query) {
                        $q2->where('nombre', 'like', "%{$query}%")
                            ->orWhere('apellido1', 'like', "%{$query}%");
                    })
                    ->orWhereHas('editorial', function ($q2) use ($query) {
                        $q2->where('nombre', 'like', "%{$query}%");
                    })
                    ->orWhereHas('generos', function ($q2) use ($query) {
                        $q2->where('nombre', 'like', "%{$query}%");
                    });
            })
            ->get()
            ->groupBy('idLibro');

        $libros = [];

        foreach ($ejemplares as $idLibro => $ejemplaresDelLibro) {
            $libro = $ejemplaresDelLibro->first()->libro;
            $libros[] = [
                'idLibro' => $libro->idLibro,
                'titulo' => $libro->titulo,
                'portadaUrl' => $libro->portadaUrl,
                'autores' => $libro->autores->map(function($autor) {
                    return [
                        'nombre' => $autor->nombre,
                        'apellido1' => $autor->apellido1
                    ];
                }),
                'editorial' => optional($libro->editorial)->nombre,
                'generos' => $libro->generos->pluck('nombre')
            ];
        }

        // Para peticiones AJAX (búsqueda dinámica)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['libros' => $libros]);
        }

        // Para peticiones normales (si se navega directamente a la URL)
        return Inertia::render('Bibliotecas/SearchResults', [
            'biblioteca' => $biblioteca,
            'rol' => $miembro->rol,
            'libros' => $libros,
            'query' => $query
        ]);
    }
}
