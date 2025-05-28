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
        Schema::create('rodados_herramientas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('frecuencia');
            $table->string('agenda');
            $table->integer('serviceHoras');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rodados_herramientas');
    }
};
