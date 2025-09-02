<?php

namespace App\Enums;

enum TipoAccionNotificacion: string
{
    // MIEMBROS BIBLIOTECA
    case SOLICITUD_UNION_RECIBIDA = 'solicitud_union_recibida';
    case SOLICITUD_UNION_ACEPTADA = 'solicitud_union_aceptada';
    case SOLICITUD_UNION_RECHAZADA = 'solicitud_union_rechazada';
    case MIEMBRO_NUEVO = 'miembro_nuevo';
    case MIEMBRO_ELIMINADO = 'miembro_eliminado';
    case MIEMBRO_ELIMINADO_PARA_ADMINS = 'miembro_eliminado_para_admins';
    case MIEMBRO_ROL_ACTUALIZADO = 'miembro_rol_actualizado';
    case MIEMBRO_ABANDONA = 'miembro_abandona';

    // LIBROS
    case LIBRO_CREADO = 'libro_creado';
    case LIBRO_ACTUALIZADO = 'libro_actualizado';
    case LIBRO_ELIMINADO = 'libro_eliminado';

    // BIBLIOTECAS
    case BIBLIOTECA_ACTUALIZADA = 'biblioteca_actualizada';
    case BIBLIOTECA_ELIMINADA = 'biblioteca_eliminada';
    case CONFIG_ETIQUETAS_ACTIVADA = 'config_etiquetas_activada';
    case CONFIG_ETIQUETAS_ACTUALIZADA = 'config_etiquetas_actualizada';
    case CONFIG_ETIQUETAS_ELIMINADA = 'config_etiquetas_eliminada';
    case POLIT_PRESTAMO_ACTIVADA = 'polit_prestamo_activada';
    case POLIT_PRESTAMO_ACTUALIZADA = 'polit_prestamo_actualizada';
    case POLIT_PRESTAMO_ELIMINADA = 'polit_prestamo_eliminada';

    // PRESTAMOS (funcionalidad pendiente de aplicacion)
    case PRESTAMO_A_VENCER = 'prestamo_a_vencer';
    case PRESTAMO_VENCIDO = 'prestamo_vencido';
    case PENALIZACION_DIAS = 'penalizacion_dias';

    public function label(): string
    {
        return match ($this) {

            // MIEMBROS BIBLIOTECA
            self::SOLICITUD_UNION_RECIBIDA => 'Solicitud de unión recibida',
            self::SOLICITUD_UNION_ACEPTADA => 'Solicitud de unión aceptada',
            self::SOLICITUD_UNION_RECHAZADA => 'Solicitud de unión rechazada',
            self::MIEMBRO_NUEVO => 'Has sido añadido a la biblioteca',
            self::MIEMBRO_ELIMINADO => 'Has sido eliminado de la biblioteca',
            self::MIEMBRO_ELIMINADO_PARA_ADMINS => 'Se ha eliminado a un miembro de la biblioteca',
            self::MIEMBRO_ROL_ACTUALIZADO => 'Tu rol ha sido actualizado',
            self::MIEMBRO_ABANDONA => 'Has abandonado la biblioteca',

            // LIBROS
            self::LIBRO_CREADO => 'Libro creado',
            self::LIBRO_ACTUALIZADO => 'Libro actualizado',
            self::LIBRO_ELIMINADO => 'Libro eliminado',

            // BIBLIOTECAS
            self::BIBLIOTECA_ACTUALIZADA => 'Biblioteca actualizada',
            self::BIBLIOTECA_ELIMINADA => 'Biblioteca eliminada',
            self::CONFIG_ETIQUETAS_ACTIVADA => 'Configuración de etiquetas activada',
            self::CONFIG_ETIQUETAS_ACTUALIZADA => 'Configuración de etiquetas actualizada',
            self::CONFIG_ETIQUETAS_ELIMINADA => 'Configuración de etiquetas eliminada',
            self::POLIT_PRESTAMO_ACTIVADA => 'Política de préstamo activada',
            self::POLIT_PRESTAMO_ACTUALIZADA => 'Política de préstamo actualizada',
            self::POLIT_PRESTAMO_ELIMINADA => 'Política de préstamo eliminada',

            // PRESTAMOS (funcionalidad pendiente de aplicacion)
            self::PRESTAMO_A_VENCER => 'Préstamo a vencer',
            self::PRESTAMO_VENCIDO => 'Préstamo vencido',
            self::PENALIZACION_DIAS => 'Penalización por días',
        };
    }
}
