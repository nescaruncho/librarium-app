<?php

namespace App\Http\Controllers;

use App\Enums\MiembroRol;
use App\Models\Biblioteca;
use App\Models\Miembro;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MiembroController extends Controller
{
    public function store($idBiblioteca)
    {
        $usuario = Auth::user();

        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $existe = Miembro::where('idUsuario', $usuario->idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya eres miembro de esta biblioteca.'
            ], 409);
        }

        if (!$biblioteca->visibilidad) {
            return response()->json([
                'message' => 'Esta biblioteca es privada. Debes enviar una solicitud para unirte.'
            ], 403);
        }

        Miembro::create([
            'idUsuario' => $usuario->idUsuario,
            'idBiblioteca' => $idBiblioteca,
            'rol' => MiembroRol::LECTOR,
        ]);

        app(NotificacionService::class)
            ->miembroNuevo($usuario->idUsuario, $idBiblioteca);

        return redirect()->back();

    }

    public function destroy($idBiblioteca, $idMiembro)
    {
        $usuario = Auth::user();

        $miembroAutenticado = Miembro::where('idUsuario', $usuario->idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if (!$miembroAutenticado || $miembroAutenticado->rol === MiembroRol::LECTOR) {
            abort(403, 'No tienes permiso para eliminar miembros de esta biblioteca.');
        }

        $miembroAEliminar = Miembro::findOrFail($idMiembro);

        if ($miembroAEliminar->rol === MiembroRol::PROPIETARIO) {
            abort(403, 'No puedes eliminar al propietario.');
        }

        $miembroAEliminar->delete();

        app(NotificacionService::class)
            ->miembroEliminado($miembroAEliminar->idUsuario, $idBiblioteca);

        app(NotificacionService::class)
            ->miembroEliminadoParaAdmins($miembroAEliminar->idUsuario, $idBiblioteca);

        return redirect()->back();

    }

    public function leave($idBiblioteca)
    {
        $usuario = Auth::user();

        $miembro = Miembro::where('idUsuario', $usuario->idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if (!$miembro) {
            return response()->json([
                'message' => 'No eres miembro de esta biblioteca.'
            ], 404);
        }

        if ($miembro->rol === MiembroRol::PROPIETARIO) {
            return response()->json([
                'message' => 'No puedes abandonar la biblioteca porque eres el propietario.'
            ], 403);
        }

        $miembro->delete();

        app(NotificacionService::class)
            ->miembroAbandona($miembro->idUsuario, $idBiblioteca);

        return redirect()->back();
    }


    public function update($idBiblioteca, $idMiembro)
    {
        $usuario = Auth::user();

        $miembro = Miembro::where('idUsuario', $usuario->idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if ($miembro->rol === MiembroRol::LECTOR) {
            return response()->json([
                'message' => 'No tienes permiso para actualizar los roles de los miembros.'
            ], 403);
        }

        $miembroAActualizar = Miembro::where('idMiembro', $idMiembro)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if ($miembroAActualizar->rol === MiembroRol::PROPIETARIO) {
            return response()->json([
                'message' => 'No puedes actualizar el rol del propietario.'
            ], 403);
        }

        $nuevoRol = $miembroAActualizar->rol === MiembroRol::ADMIN ? MiembroRol::LECTOR : MiembroRol::ADMIN;

        $miembroAActualizar->rol = $nuevoRol;
        $miembroAActualizar->save();

        app(NotificacionService::class)
            ->rolActualizado($miembroAActualizar->idUsuario, $idBiblioteca);

        return redirect()->back();
    }

    public function index($idBiblioteca)
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $miembros = $biblioteca->miembros()->with('usuario')->get();

        $rolUsuario = Miembro::where('idUsuario', Auth::user()->idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->value('rol');

        return Inertia::render('Bibliotecas/Miembros', [
            'biblioteca' => $biblioteca,
            'miembros' => $miembros->map(function ($miembro) {
                return [
                    'idMiembro' => $miembro->idMiembro,
                    'idUsuario' => $miembro->idUsuario,
                    'usuario' => $miembro->usuario->username,
                    'nombre' => $miembro->usuario->nombre,
                    'rol' => $miembro->rol
                ];
            }),
            'rolUsuario' => $rolUsuario,
        ]);
    }
}
