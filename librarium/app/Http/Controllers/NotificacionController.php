<?php

namespace App\Http\Controllers;

use App\Enums\TipoAccionNotificacion;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $usuarioId = $request->user()->idUsuario;

        $q = trim((string) $request->input('q'));
        $leido = $request->input('leido');
        $tipo = $request->input('tipo');
        $perPage = (int) ($request->input('per_page', 10)) ?: 10;

        $query = Notificacion::select([
            'idNotificacion',
            'idUsuario',
            'tipo',
            'titulo',
            'mensaje',
            'accion',
            'datosExtra',
            'leido',
            'created_at'
        ])->paraUsuario($usuarioId)->when($q !== '', function ($qq) use ($q) {
            $qq->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('mensaje', 'like', "%{$q}%");
            });
        })
            ->when($leido !== null && $leido !== '', fn($qq) => $qq->where('leido', (bool) $leido))
            ->when($tipo, fn($qq) => $qq->where('tipo', $tipo))
            ->latest('created_at');

        $items = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($items);
        }

        $tipos = Notificacion::paraUsuario($usuarioId)
            ->select('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return Inertia::render('Notificaciones/Index', [
            'items' => $items,
            'filters' => ['q' => $q, 'leido' => $leido, 'tipo' => $tipo],
            'tipos' => $tipos
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = Notificacion::paraUsuario($request->user()->idUsuario)
            ->noLeidas()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function marcarLeida(Request $request, Notificacion $notificacion)
    {
        $this->authorize('update', $notificacion);

        if (!$notificacion->leido) {
            $notificacion->update(['leido' => true]);
        }

        return response()->noContent();
    }

    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::paraUsuario($request->user()->idUsuario)->noLeidas()->update(['leido' => true]);

        return response()->noContent();
    }

    public function destroy(Request $request, Notificacion $notificacion)
    {
        $this->authorize('delete', $notificacion);

        $notificacion->delete();

        return response()->noContent();
    }

    public function go(Notificacion $notificacion): RedirectResponse
    {
        $this->authorize('view', $notificacion);

        $url = $this->resolveDestino($notificacion);

        if ($url) {
            if (!$notificacion->leido) {
                $notificacion->forceFill(['leido' => true])->save();
            }
            return redirect($url);
        }

        return redirect()
            ->route('dashboard')
            ->with('flash', ['type' => 'warning', 'message' => 'No se encontró un destino para esta notificación.']);
    }

    private function resolveDestino(Notificacion $n): ?string
    {
        $accion = $n->accion instanceof \BackedEnum
            ? $n->accion
            : TipoAccionNotificacion::tryFrom((string) $n->accion);

        $extra = is_array($n->datosExtra) ? $n->datosExtra : json_decode((string) $n->datosExtra, true) ?? [];

        $idBib = $extra['idBiblioteca']
            ?? $extra['id_biblioteca']
            ?? $extra['bibliotecaId']
            ?? $extra['biblioteca_id']
            ?? null;

        $idLib = $extra['idLibro']
            ?? $extra['id_libro']
            ?? $extra['libroId']
            ?? $extra['libro_id']
            ?? null;

        return match ($accion) {
            TipoAccionNotificacion::SOLICITUD_UNION_RECIBIDA => $this->toSolicitudesIndex($idBib),
            TipoAccionNotificacion::SOLICITUD_UNION_ACEPTADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::SOLICITUD_UNION_RECHAZADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::MIEMBRO_NUEVO => $this->toMiembrosIndex($idBib),
            TipoAccionNotificacion::MIEMBRO_ELIMINADO_PARA_ADMINS => $this->toMiembrosIndex($idBib),
            TipoAccionNotificacion::MIEMBRO_ROL_ACTUALIZADO => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::MIEMBRO_ABANDONA => $this->toMiembrosIndex($idBib),
            TipoAccionNotificacion::POLIT_PRESTAMO_ACTIVADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::POLIT_PRESTAMO_ACTUALIZADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::POLIT_PRESTAMO_ELIMINADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ACTIVADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ACTUALIZADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ELIMINADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::BIBLIOTECA_ACTUALIZADA => $this->toBibliotecaShow($idBib),
            TipoAccionNotificacion::LIBRO_CREADO => $this->toLibroShow($idLib),
            TipoAccionNotificacion::LIBRO_ELIMINADO => $this->toBibliotecaShow($idBib),

            default => null,
        };
    }

    private function toSolicitudesIndex(?int $idBib): ?string
    {

        $name = 'solicitud-union.index';

        if (\Route::has($name)) {
            $url = route($name, ['biblioteca' => $idBib]);
            return $url;
        }

        return null;
    }

    private function toBibliotecaShow(?int $idBib): ?string
    {
        $name = 'bibliotecas.show';

        if (\Route::has($name)) {
            $url = route($name, ['biblioteca' => $idBib]);
            return $url;
        }

        return null;
    }

    public function toMiembrosIndex(?int $idBib): ?string
    {
        $name = 'miembros.index';

        if (\Route::has($name)) {
            $url = route($name, ['biblioteca' => $idBib]);
            return $url;
        }

        return null;
    }

    public function toLibroShow(?int $idLibro): ?string
    {
        $name = 'libros.showEnBiblioteca';

        if (\Route::has($name)) {
            $url = route($name, ['libro' => $idLibro]);
            return $url;
        }

        return null;
    }

}
