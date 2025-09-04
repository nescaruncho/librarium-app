<?php

namespace App\Services;

use App\Enums\MiembroRol;
use App\Enums\TipoAccionNotificacion;
use App\Models\Biblioteca;
use App\Models\Libro;
use App\Models\Miembro;
use App\Models\Notificacion;
use App\Models\Usuario;

class NotificacionService
{
    public function crear(
        int $idUsuario,
        string $tipo,
        ?string $titulo,
        string $mensaje,
        ?TipoAccionNotificacion $accion = null,
        ?array $datosExtra = null,
        bool $leido = false,
    ): Notificacion {
        return Notificacion::create([
            'idUsuario' => $idUsuario,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'accion' => $accion?->value,
            'datosExtra' => $datosExtra,
            'leido' => $leido,
        ]);
    }

    public function crearParaMuchos(
        array $idUsuarios,
        string $tipo,
        ?string $titulo,
        string $mensaje,
        ?TipoAccionNotificacion $accion = null,
        ?array $datosExtra = null,
        bool $leido = false,
    ): void {
        \Log::info('=== INICIO crearParaMuchos ===', [
            'usuarios' => $idUsuarios,
            'tipo' => $tipo,
            'titulo' => $titulo
        ]);

        foreach ($idUsuarios as $id) {
            $notificacion = $this->crear($id, $tipo, $titulo, $mensaje, $accion, $datosExtra, $leido);
        }
    }
    /*
        Libros
    */
    public function libroBibliotecaCreado(int $idLibro, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $miembros = $biblioteca->miembros;
        $libro = Libro::findOrFail($idLibro);

        $this->crearParaMuchos(
            $miembros->pluck('idUsuario')->toArray(),
            'libro',
            "Nuevo libro en {$biblioteca->nombre}",
            "Se ha añadido {$libro->titulo} a la biblioteca {$biblioteca->nombre}.",
            TipoAccionNotificacion::LIBRO_CREADO,
            ['idLibro' => $idLibro, 'idBiblioteca' => $idBiblioteca]
        );
    }

    public function libroBibliotecaEliminado(int $idBiblioteca, int $idLibro): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $miembros = $biblioteca->miembros;
        $libro = Libro::findOrFail($idLibro);

        $this->crearParaMuchos(
            $miembros->pluck('idUsuario')->toArray(),
            'libro',
            "Libro eliminado de {$biblioteca->nombre}",
            "Se ha eliminado {$libro->titulo} de la biblioteca {$biblioteca->nombre}.",
            TipoAccionNotificacion::LIBRO_ELIMINADO,
            ['idLibro' => $idLibro, 'idBiblioteca' => $idBiblioteca]
        );
    }

    /*
        Miembros
    */

    public function solicitudUnionRecibida(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $miembros = $biblioteca->miembros;

        $admins = $miembros
            ->filter(function ($miembro) {
                return in_array($miembro->rol, [MiembroRol::ADMIN, MiembroRol::PROPIETARIO], true);
            })
            ->pluck('idUsuario')
            ->toArray();

        $usuario = Usuario::findOrFail($idUsuario);

        $this->crearParaMuchos(
            $admins,
            'miembro',
            'Nueva solicitud de unión',
            "El usuario {$usuario->username} ha solicitado unirse a \"{$biblioteca->nombre}\".",
            TipoAccionNotificacion::SOLICITUD_UNION_RECIBIDA,
            ['idBiblioteca' => $idBiblioteca, 'idUsuarioSolicitante' => $idUsuario]
        );
    }

    public function solicitudUnionAceptada(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $this->crear(
            $idUsuario,
            'miembro',
            'Solicitud aceptada',
            "Tu solicitud para unirte a {$biblioteca->nombre} ha sido aceptada.",
            TipoAccionNotificacion::SOLICITUD_UNION_ACEPTADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function solicitudUnionRechazada(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $this->crear(
            $idUsuario,
            'miembro',
            'Solicitud rechazada',
            "Tu solicitud para unirte a {$biblioteca->nombre} fue rechazada.",
            TipoAccionNotificacion::SOLICITUD_UNION_RECHAZADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function miembroNuevo(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros
            ->filter(function ($miembro) {
                return in_array($miembro->rol, ['admin', 'propietario']);
            })
            ->pluck('idUsuario')
            ->toArray();

        $usuario = Usuario::findOrFail($idUsuario);

        $this->crearParaMuchos(
            $admins,
            'miembro',
            'Nuevo miembro',
            "El usuario {$usuario->username} ha sido añadido a {$biblioteca->nombre}.",
            TipoAccionNotificacion::MIEMBRO_NUEVO,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function miembroEliminado(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);

        $this->crear(
            $idUsuario,
            'miembro',
            'Miembro eliminado',
            "Has sido eliminado de {$biblioteca->nombre}.",
            TipoAccionNotificacion::MIEMBRO_ELIMINADO,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function miembroEliminadoParaAdmins(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros
            ->filter(function ($miembro) {
                return in_array($miembro->rol, ['admin', 'propietario']);
            })
            ->pluck('idUsuario')
            ->toArray();
        $usuario = Usuario::findOrFail($idUsuario);

        $this->crearParaMuchos(
            $admins,
            'miembro',
            'Miembro eliminado',
            "Se ha eliminado al usuario {$usuario->username} de {$biblioteca->nombre}.",
            TipoAccionNotificacion::MIEMBRO_ELIMINADO_PARA_ADMINS,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function rolActualizado(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $rolUsuario = Miembro::where('idUsuario', $idUsuario)
            ->where('idBiblioteca', $idBiblioteca)
            ->value('rol');

        if ($rolUsuario == MiembroRol::LECTOR) {
            $this->crear(
                $idUsuario,
                'miembro',
                'Rol actualizado',
                "Tu rol ha cambiado a lector en {$biblioteca->nombre}.",
                TipoAccionNotificacion::MIEMBRO_ROL_ACTUALIZADO,
                ['idBiblioteca' => $idBiblioteca]
            );
        } else if ($rolUsuario == MiembroRol::ADMIN) {
            $this->crear(
                $idUsuario,
                'miembro',
                'Rol actualizado',
                "Tu rol ha cambiado a administrador en {$biblioteca->nombre}.",
                TipoAccionNotificacion::MIEMBRO_ROL_ACTUALIZADO,
                ['idBiblioteca' => $idBiblioteca]
            );
        }


    }

    public function miembroAbandona(int $idUsuario, int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros
            ->filter(function ($miembro) {
                return in_array($miembro->rol, ['admin', 'propietario']);
            })
            ->pluck('idUsuario')
            ->toArray();
        $this->crearParaMuchos(
            $admins,
            'miembro',
            'Miembro abandonó',
            "El usuario {$biblioteca->nombre} ha abandonado la biblioteca.",
            TipoAccionNotificacion::MIEMBRO_ABANDONA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    /*
        Biblioteca
    */

    public function bibliotecaActualizada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros
            ->filter(function ($miembro) {
                return in_array($miembro->rol, ['admin', 'propietario']);
            })
            ->pluck('idUsuario')
            ->toArray();
        $this->crearParaMuchos(
            $admins,
            'biblioteca',
            'Biblioteca actualizada',
            "Se actualizó «{$biblioteca->nombre}».",
            TipoAccionNotificacion::BIBLIOTECA_ACTUALIZADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function bibliotecaEliminada(array $payload): void
    {
        $miembros = $payload['destinatarios'] ?? [];

        $this->crearParaMuchos(
            $miembros,
            'biblioteca',
            'Biblioteca eliminada',
            "Se eliminó «{$payload['nombre']}».",
            TipoAccionNotificacion::BIBLIOTECA_ELIMINADA,
            ['idBiblioteca' => $payload['idBiblioteca']]
        );
    }


    public function configEtiquetasActivada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros()
            ->whereIn('rol', [MiembroRol::ADMIN->value, MiembroRol::PROPIETARIO->value])
            ->pluck('idUsuario')
            ->all();

        if (empty($admins)) {
            \Log::warning('[Noti] Sin destinatarios (admins/propietarios) para bibliotea', ['bib' => $idBiblioteca]);
            return;
        }
        $this->crearParaMuchos(
            $admins,
            'biblioteca',
            'Etiquetas activadas',
            "Se activó la configuración de etiquetas.",
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ACTIVADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function configEtiquetasActualizada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros()
            ->whereIn('rol', [MiembroRol::ADMIN->value, MiembroRol::PROPIETARIO->value])
            ->pluck('idUsuario')
            ->all();

        if (empty($admins)) {
            \Log::warning('[Noti] Sin destinatarios (admins/propietarios) para bibliotea', ['bib' => $idBiblioteca]);
            return;
        }
        $this->crearParaMuchos(
            $admins,
            'biblioteca',
            'Etiquetas actualizadas',
            "Se actualizó la configuración de etiquetas de {$biblioteca->nombre}.",
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ACTUALIZADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function configEtiquetasEliminada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros()
            ->whereIn('rol', [MiembroRol::ADMIN->value, MiembroRol::PROPIETARIO->value])
            ->pluck('idUsuario')
            ->all();

        $this->crearParaMuchos(
            $admins,
            'biblioteca',
            'Etiquetas desactivadas',
            "Se desactivó la configuración de etiquetas de {$biblioteca->nombre}.",
            TipoAccionNotificacion::CONFIG_ETIQUETAS_ELIMINADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function politPrestamoActivada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $miembros = $biblioteca->miembros->pluck('idUsuario')->toArray();

        $this->crearParaMuchos(
            $miembros,
            'biblioteca',
            'Política de préstamo activada',
            "La biblioteca {$biblioteca->nombre} ha activado los préstamos.",
            TipoAccionNotificacion::POLIT_PRESTAMO_ACTIVADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function politPrestamoEliminada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $miembros = $biblioteca->miembros->pluck('idUsuario')->toArray();

        $this->crearParaMuchos(
            $miembros,
            'biblioteca',
            'Política de préstamo desactivada',
            "La biblioteca {$biblioteca->nombre} ha desactivado los préstamos.",
            TipoAccionNotificacion::POLIT_PRESTAMO_ELIMINADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }

    public function politPrestamoActualizada(int $idBiblioteca): void
    {
        $biblioteca = Biblioteca::findOrFail($idBiblioteca);
        $admins = $biblioteca->miembros()
            ->whereIn('rol', [MiembroRol::ADMIN->value, MiembroRol::PROPIETARIO->value])
            ->pluck('idUsuario')
            ->all();

        if (empty($admins)) {
            \Log::warning('[Noti] Sin destinatarios (admins/propietarios) para bibliotea', ['bib' => $idBiblioteca]);
            return;
        }
        $this->crearParaMuchos(
            $admins,
            'biblioteca',
            'Política de préstamo actualizada',
            "Se actualizaron las políticas de préstamos en {$biblioteca->nombre}.",
            TipoAccionNotificacion::POLIT_PRESTAMO_ACTUALIZADA,
            ['idBiblioteca' => $idBiblioteca]
        );
    }
}
