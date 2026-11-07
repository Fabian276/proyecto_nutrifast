<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicamentos_registrados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 120);
            $table->string('dosis', 80)->nullable();
            $table->string('frecuencia', 80)->nullable();
            $table->string('indicaciones', 255)->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE medicamentos_registrados ADD CONSTRAINT chk_medicamento_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos_registrados');
    }
};
