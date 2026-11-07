<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = ['paciente_id', 'fecha_consulta', 'observaciones', 'plan_alimentacion'];

    protected function casts(): array
    {
        return ['fecha_consulta' => 'datetime'];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medicion(): HasOne
    {
        return $this->hasOne(Medicion::class);
    }

    public function medicamentos(): HasMany
    {
        return $this->hasMany(MedicamentoRegistrado::class);
    }
}
