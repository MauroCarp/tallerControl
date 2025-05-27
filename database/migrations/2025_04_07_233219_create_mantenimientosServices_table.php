<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimientosServices', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('rodadoHerramienta_id');
            $table->string('responsable');
            $table->boolean('turno');
            $table->string('tareas');
            $table->integer('horasMotor');
            $table->integer('km');
            $table->string('observaciones');
            $table->string('combustible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientosServices');
    }
};
