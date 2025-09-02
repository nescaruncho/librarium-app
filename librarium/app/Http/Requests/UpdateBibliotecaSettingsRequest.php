<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBibliotecaSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $enabledPrestamos = $this->boolean('prestamosHabilitados');
        $enabledEtiquetas = $this->boolean('etiquetasHabilitadas');

        return [
            'prestamosHabilitados' => ['required','boolean'],
            'etiquetasHabilitadas' => ['required','boolean'],

            // Política de préstamo
            'politica.duracionPrestamoDias' => [
                Rule::excludeIf(!$enabledPrestamos),
                'integer', 'min:1', 'max:365'
            ],
            'politica.numeroMaxProrrogas' => [
                Rule::excludeIf(!$enabledPrestamos),
                'integer', 'min:0', 'max:10'
            ],
            'politica.maxLibrosSimultaneos' => [
                Rule::excludeIf(!$enabledPrestamos),
                'integer', 'min:1', 'max:100'
            ],
            'politica.duracionProrrogaDias' => [
                Rule::excludeIf(!$enabledPrestamos),
                'integer', 'min:1', 'max:180'
            ],
            'politica.penalizacionDias' => [
                Rule::excludeIf(!$enabledPrestamos),
                'integer', 'min:0', 'max:365'
            ],

            // Configuración de etiquetas
            'configEtiqueta.formato' => [
                Rule::excludeIf(!$enabledEtiquetas),
                'array', 'min:1', 'max:5'
                ],
            'configEtiqueta.formato.*' => [
                Rule::excludeIf(!$enabledEtiquetas), 'string', Rule::in(['TITULO', 'APELLIDO_AUTOR', 'NOMBRE_AUTOR',
                                'GENERO', 'EDITORIAL','IDIOMA'])
            ],
            'configEtiqueta.longitudMaxima' => [
                Rule::excludeIf(!$enabledEtiquetas),
                'integer', 'min:4', 'max:16'
            ],
            'configEtiqueta.separador' => [
                Rule::excludeIf(!$enabledEtiquetas),
                'string', 'max:5'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Mensajes personalizados para la política de préstamo
            'politica.duracionMaxima.required'     => 'La duración máxima del préstamo es obligatoria cuando los préstamos están habilitados.',
            'politica.maximoRenovaciones.required' => 'El número máximo de renovaciones es obligatorio cuando los préstamos están habilitados.',
            'politica.maximoEjemplares.required'   => 'El número máximo de ejemplares es obligatorio cuando los préstamos están habilitados.',
            'politica.duracionRenovacion.required' => 'La duración de la renovación es obligatoria cuando los préstamos están habilitados.',
            'politica.penalizacionDias.required'   => 'Los días de penalización son obligatorios cuando los préstamos están habilitados.',

            // Mensajes personalizados para la configuración de etiquetas
            'configEtiqueta.formato.required'        => 'Debes seleccionar al menos un campo para el formato de la etiqueta.',
            'configEtiqueta.formato.max'             => 'Solo puedes seleccionar hasta 5 campos para el formato de la etiqueta.',
            'configEtiqueta.formato.*.distinct'      => 'No puedes repetir campos en el formato de la etiqueta.',
            'configEtiqueta.formato.*.in'            => 'Se han enviado campos de formato no válidos.',
            'configEtiqueta.longitudMaxima.required' => 'La longitud máxima es obligatoria cuando las etiquetas están habilitadas.',
            'configEtiqueta.separador.required'      => 'El separador es obligatorio cuando las etiquetas están habilitadas.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convertir los valores booleanos a enteros para la validación
        $this->merge([
            'prestamosHabilitados' => filter_var($this->prestamosHabilitados, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'etiquetasHabilitadas' => filter_var($this->etiquetasHabilitadas, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Comprobación defensiva si las etiquetas están habilitadas y hay un array formato
            if ($this->boolean('etiquetasHabilitadas') && is_array($this->input('configEtiqueta.formato'))) {
                $max = (int)($this->input('configEtiqueta.longitudMaxima', 12));
                $campos = $this->input('configEtiqueta.formato', []);

                // Regla: longitud max vs campos min
                if (
                    ($max >= 6 && count($campos) < 2) ||
                    ($max >= 9 && count($campos) < 3) ||
                    ($max == 12 && count($campos) < 4)
                ) {
                    $validator->errors()->add(
                        'configEtiqueta.longitudMaxima',
                        'La longitud máxima de la etiqueta debe ser compatible con el número de campos seleccionados.'
                    );
                }
            }
        });
    }
}
