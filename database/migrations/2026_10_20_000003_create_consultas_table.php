<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_consulta');
            $table->text('observaciones')->nullable();
            $table->text('plan_alimentacion')->nullable();
            $table->timestamps();

            $table->index(['paciente_id', 'fecha_consulta'], 'idx_consultas_paciente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
