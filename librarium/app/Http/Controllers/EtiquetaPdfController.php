<?php

namespace App\Http\Controllers;

use App\Enums\MiembroRol;
use App\Models\Biblioteca;
use App\Models\Ejemplar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EtiquetaPdfController extends Controller
{
    public function show(Biblioteca $biblioteca, Ejemplar $ejemplar)
    {

        if ((int) $ejemplar->idBiblioteca !== (int) $biblioteca->idBiblioteca) {
            abort(404);
        }

        $usuario = auth()->user()->idUsuario;
        $miembro = $biblioteca->miembros()->where('idUsuario', $usuario)->first();

        if (!$miembro || $miembro->rol === MiembroRol::LECTOR) {
            abort(403, 'No tienes permiso para ver esta etiqueta.');
        }

        if (!$biblioteca->etiquetasHabilitadas) {
            abort(404);
        }

        $ejemplar->loadMissing('libro:idLibro,titulo,isbn');

        if (empty($ejemplar->etiqueta)) {
            abort(404);
        }

        $data = [
            'codigo' => $ejemplar->etiqueta ?? 'N/A',
            'titulo' => $ejemplar->libro->titulo ?? 'N/A',
            'isbn' => $ejemplar->libro->isbn ?? 'N/A',
        ];

        $pdf = Pdf::loadView('pdf.etiqueta', $data)->setPaper('a7', 'landscape');
        return $pdf->stream("etiqueta-ejemplar-{$ejemplar->idEjemplar}.pdf");
    }

}
