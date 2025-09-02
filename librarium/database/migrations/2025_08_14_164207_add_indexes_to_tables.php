<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // USUARIO
        Schema::table('usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('usuario', 'email'))
                return;
            $table->index('created_at', 'usuario_created_at_idx');
        });

        // BIBLIOTECA
        Schema::table('biblioteca', function (Blueprint $table) {
            $table->index('idPropietario', 'biblioteca_propietario_idx');
            $table->index('visibilidad', 'biblioteca_visibilidad_idx');
            $table->fullText(['nombre', 'descripcion'], 'biblioteca_ftx');
        });

        // LIBRO
        Schema::table('libro', function (Blueprint $table) {
            if (Schema::hasColumn('libro', 'isbn')) {
                $table->unique('isbn', 'libro_isbn_unique');
            }
            if (Schema::hasColumn('libro', 'idEditorial')) {
                $table->index('idEditorial', 'libro_editorial_idx');
            }
            if (Schema::hasColumn('libro', 'idIdioma')) {
                $table->index('idIdioma', 'libro_idioma_idx');
            }
            if (Schema::hasColumn('libro', 'titulo')) {
                $table->index('titulo', 'libro_titulo_idx');
            }
            if (Schema::hasColumn('libro', 'fechaPublicacion')) {
                $table->index('fechaPublicacion', 'libro_fecha_idx');
            }
        });

        // AUTOR
        Schema::table('autor', function (Blueprint $table) {
            if (Schema::hasColumns('autor', ['nombre', 'apellido1', 'apellido2'])) {
                $table->fullText(['nombre', 'apellido1', 'apellido2'], 'autor_apellidos_nombre_idx');
            }
        });

        // AUTOR LIBRO (pivot)
        Schema::table('autorLibro', function (Blueprint $table) {
            if (Schema::hasColumns('autorLibro', ['idAutor', 'idLibro'])) {
                $table->index(['idAutor', 'idLibro'], 'autor_libro_idx');
            }
        });

        // GENERO LIBRO (pivot)
        Schema::table('generoLibro', function (Blueprint $table) {
            if (!Schema::hasColumn('generoLibro', 'id'))
                return; // por si ya migraste diferente
            $table->unique(['idLibro', 'idGenero'], 'generoLibro_unq');
            $table->index('idLibro', 'generoLibro_libro_idx');
            $table->index('idGenero', 'generoLibro_genero_idx');
        });

        // EJEMPLAR
        Schema::table('ejemplar', function (Blueprint $table) {
            $table->index('idBiblioteca', 'ejemplar_biblioteca_idx');
            $table->index('idLibro', 'ejemplar_libro_idx');
            $table->index(['idBiblioteca', 'idLibro'], 'ejemplar_biblio_libro_idx');
            if (Schema::hasColumn('ejemplar', 'disponible')) {
                $table->index(['idBiblioteca', 'disponible'], 'ejemplar_biblio_disp_idx');
            }
            if (Schema::hasColumn('ejemplar', 'etiqueta')) {
                $table->unique(['idBiblioteca', 'etiqueta'], 'ejemplar_etiqueta_por_biblio_uniq');
            }
            if (Schema::hasColumn('ejemplar', 'ubicacion')) {
                $table->index(['idBiblioteca', 'ubicacion'], 'ejemplar_biblio_ubicacion_idx');
            }
        });

        // MIEMBRO
        Schema::table('miembro', function (Blueprint $table) {
            $table->index('idUsuario', 'miembro_usuario_idx');
            $table->index('idBiblioteca', 'miembro_biblioteca_idx');
            if (Schema::hasColumn('miembro', 'rol')) {
                $table->index(['idBiblioteca', 'rol'], 'miembro_biblio_rol_idx');
            }
        });

        // PRESTAMO
        Schema::table('prestamo', function (Blueprint $table) {
            $table->index(['idMiembro', 'fechaDevolucion'], 'prestamo_miembro_devol_idx');
            $table->index(['idEjemplar', 'fechaDevolucion'], 'prestamo_ejemplar_devol_idx');
            if (Schema::hasColumn('prestamo', 'fechaFin')) {
                $table->index('fechaFin', 'prestamo_fecha_fin_idx');
            }
        });

        // TOKEN_QR
        Schema::table('token_qr', function (Blueprint $table) {
            $table->index('created_at', 'token_qr_created_idx');
        });

        // SOLICITUD_UNION
        Schema::table('solicitud_union', function (Blueprint $table) {
            $table->index(['idBiblioteca', 'estado'], 'sol_union_biblio_estado_idx');
            $table->index(['idUsuario', 'estado'], 'sol_union_usuario_estado_idx');
        });

        // LISTALECTURA
        Schema::table('listalectura', function (Blueprint $table) {
            $table->index('idUsuario', 'listalectura_usuario_idx');
        });

        // LISTA_LECTURA_EJEMPLAR (pivot)
        Schema::table('lista_lectura_ejemplar', function (Blueprint $table) {
            $table->index('idListaLectura', 'lle_lista_idx');
            $table->index('idEjemplar', 'lle_ejemplar_idx');
        });

        // LECTURA
        Schema::table('lectura', function (Blueprint $table) {
            $table->index('idUsuario', 'lectura_usuario_idx');
            $table->index('idLibro', 'lectura_libro_idx');
            if (Schema::hasColumns('lectura', ['idUsuario', 'idLibro', 'fechaInicio'])) {
                $table->index(['idUsuario', 'idLibro', 'fechaInicio'], 'lectura_usuario_libro_inicio_idx');
            }
            if (Schema::hasColumn('lectura', 'fechaFin')) {
                $table->index(['idUsuario', 'fechaFin'], 'lectura_usuario_fin_idx');
            }
        });

        // REVIEW
        Schema::table('review', function (Blueprint $table) {
            $table->unique('idLectura', 'review_lectura_unique');
            $table->index('idLectura', 'review_lectura_idx');
        });

        // NOTIFICACION
        Schema::table('notificacion', function (Blueprint $table) {
            $table->index(['idUsuario', 'leido', 'created_at'], 'noti_usuario_leido_fecha_idx');
            $table->index(['idUsuario', 'created_at'], 'notif_usuario_created_idx');
            $table->index(['idUsuario', 'tipo', 'created_at'], 'noti_usuario_tipo_fecha_idx');
        });
    }

    public function down(): void
    {
        // USUARIO
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropIndex('usuario_created_at_idx');
        });

        // BIBLIOTECA
        Schema::table('biblioteca', function (Blueprint $table) {
            $table->dropIndex('biblioteca_propietario_idx');
            $table->dropIndex('biblioteca_visibilidad_idx');
        });

        // LIBRO
        Schema::table('libro', function (Blueprint $table) {
            $table->dropUnique('libro_isbn_unique');
            $table->dropIndex('libro_editorial_idx');
            $table->dropIndex('libro_idioma_idx');
            $table->dropIndex('libro_titulo_idx');
            $table->dropIndex('libro_anio_idx');
        });

        // AUTOR
        Schema::table('autor', function (Blueprint $table) {
            $table->dropIndex('autor_apellidos_nombre_idx');
        });

        // AUTOR LIBRO
        Schema::table('autorLibro', function (Blueprint $table) {
            $table->dropIndex('autor_libro_idx');
        });

        // GENERO LIBRO
        Schema::table('generoLibro', function (Blueprint $table) {
            $table->dropUnique('generoLibro_unq');
            $table->dropIndex('generoLibro_libro_idx');
            $table->dropIndex('generoLibro_genero_idx');
        });

        // EJEMPLAR
        Schema::table('ejemplar', function (Blueprint $table) {
            $table->dropIndex('ejemplar_biblioteca_idx');
            $table->dropIndex('ejemplar_libro_idx');
            $table->dropIndex('ejemplar_biblio_libro_idx');
            $table->dropIndex('ejemplar_biblio_disp_idx');
            $table->dropUnique('ejemplar_etiqueta_por_biblio_uniq');
            $table->dropIndex('ejemplar_biblio_ubicacion_idx');
        });

        // MIEMBRO
        Schema::table('miembro', function (Blueprint $table) {
            $table->dropIndex('miembro_usuario_idx');
            $table->dropIndex('miembro_biblioteca_idx');
            $table->dropIndex('miembro_biblio_rol_idx');
        });

        // PRESTAMO
        Schema::table('prestamo', function (Blueprint $table) {
            $table->dropIndex('prestamo_miembro_devol_real_idx');
            $table->dropIndex('prestamo_ejemplar_devol_real_idx');
            $table->dropIndex('prestamo_devol_prev_idx');
        });

        // TOKEN_QR
        Schema::table('token_qr', function (Blueprint $table) {
            $table->dropUnique('token_qr_token_unique');
            $table->dropIndex('token_qr_created_idx');
        });

        // SOLICITUD_UNION
        Schema::table('solicitud_union', function (Blueprint $table) {
            $table->dropIndex('sol_union_biblio_estado_idx');
            $table->dropIndex('sol_union_usuario_estado_idx');
        });

        // LISTALECTURA
        Schema::table('listalectura', function (Blueprint $table) {
            $table->dropIndex('listalectura_usuario_idx');
        });

        // LISTA_LECTURA_EJEMPLAR
        Schema::table('lista_lectura_ejemplar', function (Blueprint $table) {
            $table->dropIndex('lle_lista_idx');
            $table->dropIndex('lle_ejemplar_idx');
        });

        // LECTURA
        Schema::table('lectura', function (Blueprint $table) {
            $table->dropIndex('lectura_usuario_idx');
            $table->dropIndex('lectura_libro_idx');
            $table->dropIndex('lectura_usuario_libro_inicio_idx');
            $table->dropIndex('lectura_usuario_fin_idx');
        });

        // REVIEW
        Schema::table('review', function (Blueprint $table) {
            $table->dropUnique('review_lectura_unique');
            $table->dropIndex('review_lectura_idx');
        });

        // NOTIFICACION
        Schema::table('notificacion', function (Blueprint $table) {
            $table->dropIndex('notif_usuario_idx');
            $table->dropIndex('notif_usuario_leida_idx');
            $table->dropIndex('notif_accion_idx');
            $table->dropIndex('notif_usuario_created_idx');
        });
    }
};
