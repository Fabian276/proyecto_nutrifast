<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Datos FICTICIOS de demostración (los mismos del prototipo). No usar en producción.
 * Acceso: nutriologa@ejemplo.test / prueba123
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nombre' => 'Nutrióloga de prueba',
            'email' => 'nutriologa@ejemplo.test',
            'password' => 'prueba123', // el cast "hashed" del modelo la encripta
        ]);

        $p = [];
        foreach ([
            [1, 'Ana', 'Ejemplo Ficticio', '1990-04-12', 'F', 1.62, '000 000 0001', 'Dato ficticio: sin antecedentes relevantes.'],
            [2, 'Luis', 'Prueba Demo', '1985-09-30', 'M', 1.75, '000 000 0002', null],
            [3, 'Marta', 'Muestra Inventada', '2000-01-08', 'F', 1.58, null, 'Dato ficticio: actividad física 3 veces por semana.'],
            [4, 'Jorge', 'Simulado Ensayo', '1978-06-21', 'M', 1.70, '000 000 0004', null],
            [5, 'Sofía', 'Ficción Modelo', '1995-11-03', 'F', 1.66, '000 000 0005', null],
        ] as [$id, $nombre, $apellidos, $nac, $sexo, $est, $tel, $ant]) {
            $p[$id] = Paciente::create([
                'nombre' => $nombre, 'apellidos' => $apellidos, 'fecha_nacimiento' => $nac,
                'sexo' => $sexo, 'estatura_m' => $est, 'telefono' => $tel, 'antecedentes' => $ant,
            ]);
        }

        // [paciente, fecha, observaciones, plan, medición, medicamentos]
        $consultas = [
            [1, '2026-07-06 10:00', 'Primera consulta (dato ficticio).', 'Plan ficticio: 5 tiempos de comida, hidratación 2 L.',
                ['peso' => 73.6, 'imc' => 28.04, 'porcentaje_grasa' => 35.0, 'agua_corporal' => 46.5, 'masa_muscular' => 44.8],
                [['Medicamento ficticio A', '1 tableta', 'Cada 24 h']]],
            [1, '2026-08-03 10:00', 'Seguimiento (dato ficticio).', 'Plan ficticio: se mantiene.',
                ['peso' => 72.4, 'imc' => 27.59, 'porcentaje_grasa' => 34.1, 'agua_corporal' => 47.2, 'masa_muscular' => 45.0], []],
            [1, '2026-09-01 10:00', 'Seguimiento (dato ficticio).', 'Plan ficticio: ajuste de colaciones.',
                ['peso' => 70.9, 'imc' => 27.02, 'porcentaje_grasa' => 33.0, 'agua_corporal' => 48.0, 'masa_muscular' => 45.3], []],
            [1, '2026-10-01 10:00', 'Seguimiento (dato ficticio).', 'Plan ficticio: ajuste de cenas.',
                ['peso' => 69.8, 'imc' => 26.60, 'porcentaje_grasa' => 32.2, 'agua_corporal' => 48.6, 'masa_muscular' => 45.4], []],
            [3, '2026-08-18 12:00', 'Primera consulta (dato ficticio).', 'Plan ficticio inicial.',
                ['peso' => 58.9, 'imc' => 23.59], []],
            [3, '2026-09-15 12:00', 'Seguimiento (dato ficticio).', 'Plan ficticio: se mantiene.',
                ['peso' => 58.1, 'imc' => 23.27], []],
            [4, '2026-09-22 17:00', 'Primera consulta (dato ficticio).', 'Plan ficticio: reducir bebidas azucaradas.',
                ['peso' => 91.2, 'imc' => 31.56, 'porcentaje_grasa' => 29.4],
                [['Medicamento ficticio B', '500 mg', 'Cada 12 h']]],
        ];

        foreach ($consultas as [$pid, $fecha, $obs, $plan, $med, $meds]) {
            $c = Consulta::create([
                'paciente_id' => $p[$pid]->id, 'fecha_consulta' => $fecha,
                'observaciones' => $obs, 'plan_alimentacion' => $plan,
            ]);
            $c->medicion()->create($med);
            foreach ($meds as [$nombre, $dosis, $frecuencia]) {
                $c->medicamentos()->create(compact('nombre', 'dosis', 'frecuencia'));
            }
        }

        // Citas relativas a hoy, para que la agenda siempre tenga datos.
        foreach ([
            [2, now()->setTime(12, 30), 'Primera consulta'],
            [5, now()->setTime(16, 0), 'Primera consulta'],
            [4, now()->addDays(3)->setTime(17, 0), 'Seguimiento'],
            [3, now()->addDays(9)->setTime(12, 0), 'Seguimiento'],
            [1, now()->addDays(21)->setTime(10, 0), 'Seguimiento mensual'],
        ] as [$pid, $fechaHora, $motivo]) {
            Cita::create(['paciente_id' => $p[$pid]->id, 'fecha_hora' => $fechaHora, 'motivo' => $motivo]);
        }
    }
}
