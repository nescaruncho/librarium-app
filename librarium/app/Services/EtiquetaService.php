<?php

namespace App\Services;

use App\Models\Biblioteca;
use App\Models\Ejemplar;
use App\Models\Libro;
use Illuminate\Support\Str;

class EtiquetaService
{
    /**
     * Genera la etiqueta para un ejemplar basado en la configuración de la biblioteca.
     *
     * @param \App\Models\Libro $libro
     * @param \App\Models\Biblioteca $biblioteca
     * @return string
     */

    public const LONGITUD_MAXIMA_TOTAL = 16;
    public const MAX_CAMPOS = 5;

    public function generarEtiqueta(Libro $libro, Biblioteca $biblioteca): string
    {
        $libro->loadMissing(['autores', 'generos', 'editorial', 'idioma']);

        $config = $biblioteca->configuracionetiqueta;

        // Si no existe configuración, usamos un formato por defecto
        if (!$config) {
            return Str::upper(Str::random(8));
        }

        $tokens = is_array($config->formato) ? $config->formato : [];
        $tokens = array_slice($tokens, 0, self::MAX_CAMPOS);

        if (empty($tokens)) {
            return Str::upper(Str::random(8));
        }

        $tokens = array_map(fn($t) => Str::upper($t), $config->formato);

        $separador = $config->separador ?? '-';

        // Determinar longitud máxima
        $maxLength = (int) $config->longitudMaxima;
        if ($maxLength <= 0)
            $maxLength = self::LONGITUD_MAXIMA_TOTAL;
        $maxLength = min($maxLength, self::LONGITUD_MAXIMA_TOTAL);

        $nTokens = count($tokens);
        $sepLenTotal = max(0, strlen($separador) * ($nTokens - 1));
        $baseBudget = max(1, $maxLength - $sepLenTotal);

        // Reparte caracteres por token
        $perToken = max(1, intdiv($baseBudget, $nTokens));
        $extraChars = $baseBudget % $nTokens;

        $componentes = [];

        foreach ($tokens as $i => $campo) {
            $valor = $this->obtenerValorCampo($libro, $campo);
            $valor = trim($valor);
            $asignado = $perToken + ($i < $extraChars ? 1 : 0);
            $componentes[] = Str::upper(Str::substr($valor, 0, $asignado));
        }

        $componentes = array_filter($componentes, fn($c) => $c !== '');

        if (empty($componentes)) {
            \Log::info("Componentes vacíos para libro ID {$libro->idLibro}", [
                'tokens' => $tokens,
                'libro' => $libro->toArray(),
                'tiene_autores' => $libro->autores && $libro->autores->count() > 0,
                'tiene_generos' => $libro->generos && $libro->generos->count() > 0,
                'formato_config' => $config->formato ?? 'no disponible'
            ]);
            $etiquetaBase = Str::upper(Str::substr(Str::slug($libro->titulo ?? '', ''), 0, $baseBudget));
            if ($etiquetaBase === '') {
                $etiquetaBase = Str::upper(Str::random(min(8, $baseBudget)));
            }
        } else {
            $etiquetaBase = implode($separador, $componentes);
        }

        return $this->resolverColision($etiquetaBase, (int) $biblioteca->idBiblioteca, $separador, $maxLength);
    }

    protected function obtenerValorCampo(Libro $libro, string $campo): string
    {
        $key = str_replace('_', '', Str::upper($campo));

        switch ($key) {
            case 'TITULO':
                return $libro->titulo ?? '';
            case 'APELLIDOAUTOR':
                return optional(optional($libro->autores)->first())->apellido1 ?? '';
            case 'NOMBREAUTOR':
                return optional(optional($libro->autores)->first())->nombre ?? '';
            case 'GENERO':
                return optional(optional($libro->generos)->first())->nombre ?? '';
            case 'EDITORIAL':
                return optional($libro->editorial)->nombre ?? '';
            case 'IDIOMA':
                return optional($libro->idioma)->nombre ?? '';
            default:
                return '';
        }
    }

    protected function resolverColision(string $etiquetaBase, int $idBiblioteca, string $separador, int $maxLength): string
    {
        $etiqueta = $etiquetaBase === '' ? 'X' : $etiquetaBase; // micro-guard por si llega vacía
        $contador = 2;

        // Verificamos si existe colisión
        if (
            !Ejemplar::where('idBiblioteca', $idBiblioteca)
                ->where('etiqueta', $etiqueta)
                ->exists()
        ) {
            return $etiqueta; // No hay colisión, retornamos la etiqueta original
        }

        // Si hay colisión, preparamos para agregar sufijo
        $contador = 2;

        do {
            $sufijo = $separador . $contador;
            $etiquetaFinal = $etiqueta . $sufijo;

            // Si excede el límite absoluto, recortamos
            if (strlen($etiquetaFinal) > self::LONGITUD_MAXIMA_TOTAL) {
                $recortada = Str::substr($etiqueta, 0, self::LONGITUD_MAXIMA_TOTAL - strlen($sufijo));
                $etiquetaFinal = $recortada . $sufijo;
            }

            $contador++;
        } while (
            Ejemplar::where('idBiblioteca', $idBiblioteca)
                ->where('etiqueta', $etiquetaFinal)
                ->exists()
        );

        return $etiquetaFinal;
    }

}
