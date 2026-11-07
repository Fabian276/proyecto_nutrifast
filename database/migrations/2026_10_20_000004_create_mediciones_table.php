<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->unique()->constrained('consultas')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('peso', 5, 2);                          // kg
            $table->decimal('imc', 4, 2);                           // kg/m2
            $table->decimal('porcentaje_grasa', 4, 1)->nullable();  // %
            $table->decimal('agua_corporal', 4, 1)->nullable();     // %
            $table->decimal('masa_muscular', 5, 2)->nullable();     // kg
            $table->decimal('masa_osea', 4, 2)->nullable();         // kg
            $table->unsignedTinyInteger('grasa_visceral')->nullable();       // nivel 1 a 59
            $table->unsignedSmallInteger('tasa_metabolica_basal')->nullable(); // kcal/día
            $table->unsignedTinyInteger('edad_metabolica')->nullable();      // años
            $table->unsignedTinyInteger('valoracion_fisica')->nullable();    // 1 a 9 [CONFIRMAR según báscula]
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            $checks = [
                'chk_med_peso'       => 'peso > 0 AND peso <= 400',
                'chk_med_imc'        => 'imc > 0 AND imc <= 90',
                'chk_med_grasa'      => 'porcentaje_grasa IS NULL OR porcentaje_grasa BETWEEN 0 AND 75',
                'chk_med_agua'       => 'agua_corporal IS NULL OR agua_corporal BETWEEN 0 AND 100',
                'chk_med_muscular'   => 'masa_muscular IS NULL OR masa_muscular > 0',
                'chk_med_osea'       => 'masa_osea IS NULL OR masa_osea > 0',
                'chk_med_visceral'   => 'grasa_visceral IS NULL OR grasa_visceral BETWEEN 1 AND 59',
                'chk_med_tmb'        => 'tasa_metabolica_basal IS NULL OR tasa_metabolica_basal BETWEEN 500 AND 6000',
                'chk_med_edad_met'   => 'edad_metabolica IS NULL OR edad_metabolica BETWEEN 5 AND 120',
                'chk_med_valoracion' => 'valoracion_fisica IS NULL OR valoracion_fisica BETWEEN 1 AND 9',
            ];
            foreach ($checks as $nombre => $condicion) {
                DB::statement("ALTER TABLE mediciones ADD CONSTRAINT {$nombre} CHECK ({$condicion})");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mediciones');
    }
};
