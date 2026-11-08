<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = ['paciente_id', 'fecha_hora', 'motivo', 'estado'];

    protected function casts(): array
    {
        return ['fecha_hora' => 'datetime'];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /** Mensaje de recordatorio para mandar por WhatsApp. */
    public function mensajeRecordatorio(): string
    {
        $fecha = $this->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM');

        return "Hola {$this->paciente->nombre}, te recordamos tu cita de nutrición el {$fecha} a las {$this->fecha_hora->format('H:i')} hrs."
            .($this->motivo ? " Motivo: {$this->motivo}." : '')
            .' Si necesitas cambiarla, avísanos por este medio. ¡Te esperamos!';
    }

    public function whatsappUrl(): ?string
    {
        return $this->paciente->whatsappUrl($this->mensajeRecordatorio());
    }
}
