<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicamentoRegistrado extends Model
{
    protected $table = 'medicamentos_registrados';

    protected $fillable = ['consulta_id', 'nombre', 'dosis', 'frecuencia', 'indicaciones'];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }
}
