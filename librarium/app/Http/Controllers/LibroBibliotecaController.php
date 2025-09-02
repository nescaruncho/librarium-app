<?php

namespace App\Http\Controllers;

use App\Enums\MiembroRol;
use App\Models\Biblioteca;
use App\Models\Ejemplar;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class LibroBibliotecaController extends Controller
{
    public function destroy(Biblioteca $biblioteca, $idLibro)
    {
        $usuarioId = auth()->user()->idUsuario;
        $miembro = $biblioteca->miembros()->where('idUsuario', $usuarioId)->first();

        if (!$miembro || $miembro->rol === MiembroRol::LECTOR) {
            return back()->with('error', 'No tienes permiso para eliminar este libro.');
        }

        Ejemplar::where('idBiblioteca', $biblioteca->idBiblioteca)
            ->where('idLibro', $idLibro)
            ->delete();

        app(NotificacionService::class)->libroBibliotecaEliminado($idLibro, $biblioteca->idBiblioteca);

        return back()->with('success', 'Libro eliminado de la biblioteca.');
    }
}
