<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicion extends Model
{
    protected $table = 'mediciones';

    /** Indicadores de la tabla: clave => [nombre, unidad, decimales]. Los usa la pantalla de progreso. */
    public const INDICADORES = [
        'peso'                  => ['Peso', 'kg', 1],
        'imc'                   => ['IMC', '', 1],
        'porcentaje_grasa'      => ['Grasa corporal', '%', 1],
        'agua_corporal'         => ['Agua corporal', '%', 1],
        'masa_muscular'         => ['Masa muscular', 'kg', 1],
        'masa_osea'             => ['Masa ósea', 'kg', 1],
        'grasa_visceral'        => ['Grasa visceral', '', 0],
        'tasa_metabolica_basal' => ['Tasa metabólica basal', 'kcal', 0],
        'edad_metabolica'       => ['Edad metabólica', 'años', 0],
        'valoracion_fisica'     => ['Valoración física', '', 0],
    ];

    protected $fillable = [
        'consulta_id', 'peso', 'imc', 'porcentaje_grasa', 'agua_corporal', 'masa_muscular',
        'masa_osea', 'grasa_visceral', 'tasa_metabolica_basal', 'edad_metabolica', 'valoracion_fisica',
    ];

    protected function casts(): array
    {
        return [
            'peso' => 'float',
            'imc' => 'float',
            'porcentaje_grasa' => 'float',
            'agua_corporal' => 'float',
            'masa_muscular' => 'float',
            'masa_osea' => 'float',
            'grasa_visceral' => 'integer',
            'tasa_metabolica_basal' => 'integer',
            'edad_metabolica' => 'integer',
            'valoracion_fisica' => 'integer',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }
}
