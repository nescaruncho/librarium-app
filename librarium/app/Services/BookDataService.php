<?php

namespace App\Services;

use App\Models\{Autor, Editorial, Genero, Idioma, Libro};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class BookDataService
{
    public function obtenerOLibro(string $isbn): ?Libro
    {
        $datos = $this->fetchFromOpenLibrary($isbn);
        if (!$datos || empty($datos['titulo'])) {
            return null;
        }
        return $this->crearLibroConRelaciones($datos);
    }

    public function fetchFromOpenLibrary(string $isbn): ?array
    {
        $editionRes = Http::timeout(10)->retry(2, 200)
            ->get("https://openlibrary.org/isbn/{$isbn}.json");

        if ($editionRes->failed()) return null;

        $edition = $editionRes->json();

        $portada = $this->resolveCoverUrlFromEdition($edition);

        $editorial = null;
        if (!empty($edition['publishers'])) {
            $editorial = is_array($edition['publishers'])
                ? ($edition['publishers'][0] ?? null)
                : $edition['publishers'];
        }

        $idiomaCodigo = null;
        if (!empty($edition['languages'][0]['key'])) {
            $idiomaCodigo = basename($edition['languages'][0]['key']);
        }

        $numeroPaginas = $edition['number_of_pages'] ?? null;

        $sinopsis = null;
        $subjects = [];
        if (!empty($edition['works'][0]['key'])) {
            $workKey = $edition['works'][0]['key'];
            $work = $this->fetchWork($workKey);
            if ($work) {
                $sinopsis = $this->extractDescription($work);
                $subjects = $this->extractSubjects($work);
                if (!$portada) {
                    $portada = $this->resolveCoverUrlFromWork($work);
                }
            }
        }

        $autores = $this->fetchAuthors($edition['authors'] ?? []);

        return [
            'isbn'              => $isbn,
            'titulo'            => $edition['title'] ?? null,
            'portadaUrl'        => $portada,
            'fechaPublicacion'  => $edition['publish_date'] ?? null,
            'numeroPaginas'     => $numeroPaginas,
            'editorial'         => $editorial,
            'idioma'            => $idiomaCodigo,
            'autores'           => $autores,
            'generos'           => $subjects,
            'sinopsis'          => $sinopsis,
        ];
    }

    private function fetchWork(string $workKey): ?array
    {
        $res = Http::timeout(10)->retry(2, 200)
            ->get("https://openlibrary.org{$workKey}.json");
        return $res->successful() ? $res->json() : null;
    }

    private function fetchAuthors(array $authors): array
    {
        return collect($authors)->map(function ($a) {
            $key = Arr::get($a, 'key');
            if (!$key) return null;

            $res = Http::timeout(8)->retry(2, 200)
                ->get("https://openlibrary.org{$key}.json");
            if ($res->failed()) return null;

            $json = $res->json();
            return $json['name'] ?? null;
        })
        ->filter()
        ->unique()
        ->values()
        ->all();
    }

    private function extractDescription(array $work): ?string
    {
        $desc = $work['description'] ?? null;
        return is_array($desc) ? ($desc['value'] ?? null) : $desc;
    }

    private function extractSubjects(array $work): array
    {
        $subjects = $work['subjects'] ?? [];
        if (!is_array($subjects)) return [];
        return collect($subjects)
            ->filter(fn($s) => is_string($s))
            ->take(12)
            ->values()
            ->all();
    }

    private function resolveCoverUrlFromEdition(array $edition): ?string
    {
        if (!empty($edition['covers']) && is_array($edition['covers'])) {
            $coverId = $edition['covers'][0];
            return "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
        }
        return null;
    }

    private function resolveCoverUrlFromWork(array $work): ?string
    {
        if (!empty($work['covers']) && is_array($work['covers'])) {
            $coverId = $work['covers'][0];
            return "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
        }
        return null;
    }

    public function crearLibroConRelaciones(array $d): Libro
    {
        $libro = Libro::create([
            'isbn'             => $d['isbn'],
            'titulo'           => $d['titulo'],
            'portadaUrl'       => $d['portadaUrl'] ?? null,
            'fechaPublicacion' => $d['fechaPublicacion'] ?? null,
            'numeroPaginas'    => $d['numeroPaginas'] ?? null,
            'sinopsis'         => $d['sinopsis'] ?? null,
        ]);

        if (!empty($d['editorial'])) {
            $editorial = Editorial::firstOrCreate(['nombre' => $d['editorial']]);
            $libro->idEditorial = $editorial->idEditorial;
        }

        if (!empty($d['idioma'])) {
            $map = config('idiomas.map');
            $codigo = $d['idioma'];
            $nombre = $map[$codigo] ?? strtoupper($codigo);

            $idioma = Idioma::firstOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre]
            );
            $libro->idIdioma = $idioma->idIdioma;
        }

        $libro->save();

        if (!empty($d['autores'])) {
            $ids = [];
            foreach ($d['autores'] as $fullName) {
                $parts = $this->splitAuthorName($fullName); // 👈 asegura apellido1
                $autor = Autor::firstOrCreate(
                    ['nombre' => $parts['nombre'], 'apellido1' => $parts['apellido1']]
                );
                $ids[] = $autor->idAutor;
            }
            $libro->autores()->sync($ids);
        }

        if (!empty($d['generos'])) {
            $ids = [];
            foreach ($d['generos'] as $nombre) {
                $genero = Genero::firstOrCreate(['nombre' => $nombre]);
                $ids[] = $genero->idGenero;
            }
            $libro->generos()->sync($ids);
        }

        return $libro;
    }

    private function splitAuthorName(string $full): array
    {
        $parts = preg_split('/\s+/', trim($full), -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts)   return ['nombre' => 'Desconocido', 'apellido1' => '-'];
        if (count($parts) === 1) return ['nombre' => $parts[0], 'apellido1' => $parts[0]];
        $apellido1 = array_pop($parts);
        $nombre = implode(' ', $parts);
        return ['nombre' => $nombre ?: $full, 'apellido1' => $apellido1];
    }
}
