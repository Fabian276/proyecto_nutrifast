<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'nombre', 'apellidos', 'fecha_nacimiento', 'sexo',
        'estatura_m', 'telefono', 'correo', 'antecedentes',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'estatura_m' => 'float',
        ];
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class)->orderBy('fecha_consulta');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function ultimaConsulta(): HasOne
    {
        return $this->hasOne(Consulta::class)->latestOfMany('fecha_consulta');
    }

    /** Próxima cita programada (se tolera una hora de retraso, igual que el prototipo). */
    public function proximaCita(): HasOne
    {
        return $this->hasOne(Cita::class)->ofMany(
            ['fecha_hora' => 'min'],
            fn ($q) => $q->where('estado', 'programada')->where('fecha_hora', '>=', now()->subHour())
        );
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre.' '.$this->apellidos;
    }

    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }

    /** Teléfono sólo con dígitos y lada de país. Números de 10 dígitos se toman como de México (+52). */
    public function getTelefonoWhatsappAttribute(): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $this->telefono);

        if (strlen($digitos) === 10) {
            return '52'.$digitos;
        }

        return strlen($digitos) >= 11 ? $digitos : null;
    }

    /** Liga de WhatsApp con el mensaje ya escrito, o null si el paciente no tiene un teléfono válido. */
    public function whatsappUrl(string $mensaje): ?string
    {
        return $this->telefono_whatsapp
            ? 'https://wa.me/'.$this->telefono_whatsapp.'?text='.rawurlencode($mensaje)
            : null;
    }
}
