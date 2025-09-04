<?php

namespace App\Http\Controllers;

use App\Enums\EstadoSolicitud;
use App\Enums\MiembroRol;
use App\Models\Biblioteca;
use App\Models\Miembro;
use App\Models\SolicitudUnion;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolicitudUnionController extends Controller
{
    public function store($idBiblioteca)
    {
        $usuario = Auth::user()->idUsuario;

        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        if ($biblioteca->visibilidad) {
            return response()->json([
                'message' => 'Esta biblioteca es pública. No necesitas enviar una solicitud para unirte.'
            ], 403);
        }

        $yaEsMiembro = $biblioteca->miembros()->where('idUsuario', $usuario)->exists();

        if ($yaEsMiembro) {
            return response()->json([
                'message' => 'Ya eres miembro de esta biblioteca.'
            ], 409);
        }

        $yaSolicitado = $biblioteca->solicitudes()->where('idUsuario', $usuario)->exists();

        if ($yaSolicitado) {
            return response()->json([
                'message' => 'Ya has enviado una solicitud para unirte a esta biblioteca.'
            ], 409);
        }

        SolicitudUnion::create([
            'idUsuario' => $usuario,
            'idBiblioteca' => $idBiblioteca,
            'estado' => EstadoSolicitud::PENDIENTE,
        ]);

        app(NotificacionService::class)
            ->solicitudUnionRecibida($usuario, $idBiblioteca);

        return redirect()->back();
    }

    public function update(Request $request, $idBiblioteca, $idSolicitudUnion)
    {

        $solicitud = SolicitudUnion::findOrFail($idSolicitudUnion);

        $usuario = Auth::user()->idUsuario;

        $miembro = Miembro::where('idUsuario', $usuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if (!$miembro || $miembro->rol === MiembroRol::LECTOR) {
            return response()->json([
                'message' => 'No tienes permisos para aprobar o rechazar solicitudes.'
            ], 403);
        }

        if ($solicitud->estado !== EstadoSolicitud::PENDIENTE) {
            return response()->json([
                'message' => 'La solicitud ya ha sido procesada.'
            ], 400);
        }

        $accion = $request->input('accion');

        if ($accion === 'aceptar') {
            Miembro::create([
                'idUsuario' => $solicitud->idUsuario,
                'idBiblioteca' => $idBiblioteca,
                'rol' => MiembroRol::LECTOR,
            ]);
            app(NotificacionService::class)
                ->solicitudUnionAceptada($solicitud->idUsuario, $idBiblioteca);
            $solicitud->delete();
            return redirect()->back();
        } else if ($accion === 'rechazar') {
            $solicitud->delete();
            app(NotificacionService::class)
                ->solicitudUnionRechazada($solicitud->idUsuario, $idBiblioteca);
            return redirect()->back();
        }
    }

    public function destroyPropia($idBiblioteca)
    {
        $usuario = Auth::user()->idUsuario;

        $solicitud = SolicitudUnion::where('idBiblioteca', $idBiblioteca)
            ->where('idUsuario', $usuario)
            ->first();

        if (!$solicitud) {
            return response()->json([
                'message' => 'No se encontró ninguna solicitud pendiente.'
            ], 404);
        }

        $solicitud->delete();

        return redirect()->back();
    }

    public function index($idBiblioteca)
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $usuario = Auth::user()->idUsuario;

        $miembro = Miembro::where('idUsuario', $usuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->firstOrFail();

        if (!$miembro || $miembro->rol === MiembroRol::LECTOR) {
            abort(403, 'No tienes permisos para ver las solicitudes de unión.');
        }

        $solicitudes = SolicitudUnion::with('usuario')
            ->where('idBiblioteca', $idBiblioteca)
            ->where('estado', EstadoSolicitud::PENDIENTE)
            ->get();

        return Inertia::render('Bibliotecas/Solicitudes', [
            'biblioteca' => $biblioteca,
            'solicitudes' => $solicitudes,
        ]);
    }
}
