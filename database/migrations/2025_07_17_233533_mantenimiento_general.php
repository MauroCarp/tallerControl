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
        Schema::create('mantenimiento_general', function (Blueprint $table) {
            $table->id();
            $table->date('fechaSolicitud');
            $table->text('tarea');
            $table->text('solicitado');
            $table->boolean('reparado');
            $table->integer('horas');
            $table->text('materiales');
            $table->float('costo');
            $table->text('realizado');
            $table->date('fechaRealizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
