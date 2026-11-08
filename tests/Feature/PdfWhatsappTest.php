<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfWhatsappTest extends TestCase
{
    use RefreshDatabase;

    private function preparar(): array
    {
        $usuario = Usuario::create(['nombre' => 'Lic. Ana Ruiz', 'email' => 'ana@test.mx', 'password' => 'secreto123']);
        $paciente = Paciente::create([
            'nombre' => 'José', 'apellidos' => 'Pérez López', 'fecha_nacimiento' => '1990-05-10',
            'sexo' => 'M', 'estatura_m' => 1.75, 'telefono' => '(55) 1234-5678',
        ]);
        $consulta = $paciente->consultas()->create([
            'fecha_consulta' => now()->subDay(), 'plan_alimentacion' => "Desayuno: avena\nComida: pollo",
        ]);
        $consulta->medicion()->create(['peso' => 80, 'imc' => 26.12]);
        $consulta->medicamentos()->create(['nombre' => 'Omega 3', 'dosis' => '1 g', 'frecuencia' => 'Diario']);
        $paciente->citas()->create(['fecha_hora' => now()->addDays(2)->setTime(10, 30), 'estado' => 'programada']);

        return [$usuario, $paciente, $consulta];
    }

    public function test_agenda_muestra_boton_de_whatsapp(): void
    {
        [$usuario] = $this->preparar();

        $this->actingAs($usuario)->get('/agenda')
            ->assertOk()
            ->assertSee('https://wa.me/525512345678?text=', false);
    }

    public function test_descarga_pdf_del_paciente(): void
    {
        [$usuario, $paciente] = $this->preparar();

        $res = $this->actingAs($usuario)->get(route('pacientes.pdf', $paciente));
        $res->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_descarga_pdf_de_la_consulta(): void
    {
        [$usuario, $paciente, $consulta] = $this->preparar();

        $res = $this->actingAs($usuario)->get(route('consultas.pdf', [$paciente, $consulta]));
        $res->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_progreso_entrega_los_datos_de_la_grafica(): void
    {
        [$usuario, $paciente] = $this->preparar();
        $c2 = $paciente->consultas()->create(['fecha_consulta' => now()]);
        $c2->medicion()->create(['peso' => 78, 'imc' => 25.47]);

        $html = $this->actingAs($usuario)->get(route('progreso.show', $paciente))->assertOk()->getContent();

        preg_match('#<script type="application/json" id="nf-progreso-datos">(.*?)</script>#s', $html, $m);
        $datos = json_decode($m[1] ?? '', true);
        $this->assertCount(2, $datos['puntos']);
        $this->assertEquals(78, $datos['puntos'][1]['peso']);
    }

    public function test_guardar_y_descargar_pdf_redirige_a_la_consulta(): void
    {
        [$usuario, $paciente] = $this->preparar();

        $this->actingAs($usuario)->post(route('consultas.store', $paciente), [
            'peso' => 78.5, 'fecha' => now()->format('Y-m-d\TH:i'), 'plan' => 'Más agua', 'accion' => 'pdf',
        ])->assertRedirectContains('/consultas/')->assertSessionHas('descargar_pdf', true);
    }
}
