<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_hora')->index('idx_citas_fecha');
            $table->string('motivo', 150)->nullable();
            $table->enum('estado', ['programada', 'atendida', 'cancelada'])->default('programada');
            $table->timestamps();

            $table->index(['paciente_id', 'fecha_hora'], 'idx_citas_paciente');
        });
        // "La cita no puede quedar en el pasado al registrarse" se valida en
        // ConsultaRequest, no con CHECK, porque MySQL no permite NOW() en CHECK.
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
