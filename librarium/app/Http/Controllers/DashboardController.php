<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Lectura;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Usuario::with(['miembro.biblioteca', 'lecturas.libro.autores'])->findOrFail(Auth::id());

        $bibliotecas = $usuario->miembro
            ->sortByDesc('created_at')
            ->take(4)
            ->map(function ($miembro) {
                return [
                    'idBiblioteca' => $miembro->biblioteca->idBiblioteca,
                    'nombre' => $miembro->biblioteca->nombre,
                    'rol' => $miembro->rol,
                ];
            })
            ->values();

        $lecturas = $usuario->lecturas()
            ->where('estado', \App\Enums\EstadoLectura::LEYENDO)
            ->latest('fechaInicio')
            ->take(4)
            ->with('libro.autores')
            ->get()
            ->map(function ($lectura) {
                $libro = $lectura->libro;

                return [
                    'idLectura' => $lectura->idLectura,
                    'libro' => [
                        'idLibro' => $libro->idLibro,
                        'titulo' => $libro->titulo,
                        'portadaUrl' => $libro->portadaUrl,
                        'autores' => $libro->autores->map(fn($a) => [
                            'nombre' => $a->nombre,
                            'apellido1' => $a->apellido1,
                        ])->values(),
                    ],
                ];
            });
        return Inertia::render('Dashboard', [
            'usuario' => [
                'nombre' => $usuario->nombre,
                'username' => $usuario->username,
            ],
            'bibliotecas' => $bibliotecas,
            'lecturas' => $lecturas,
        ]);
    }
}
