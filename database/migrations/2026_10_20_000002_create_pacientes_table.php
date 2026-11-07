<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['F', 'M']);
            $table->decimal('estatura_m', 3, 2);   // metros; se usa para el IMC
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 150)->nullable();
            $table->text('antecedentes')->nullable();
            $table->timestamps();

            $table->index(['apellidos', 'nombre'], 'idx_pacientes_nombre');
        });

        // Última barrera de integridad (MySQL 8.0.16+). En SQLite (pruebas) se omite.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE pacientes ADD CONSTRAINT chk_pacientes_estatura CHECK (estatura_m BETWEEN 0.50 AND 2.50)');
            DB::statement('ALTER TABLE pacientes ADD CONSTRAINT chk_pacientes_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)');
            DB::statement('ALTER TABLE pacientes ADD CONSTRAINT chk_pacientes_apellidos CHECK (CHAR_LENGTH(TRIM(apellidos)) > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
