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
        Schema::table('mantenimiento_generals', function (Blueprint $table) {
            $table->integer('prioridad_orden')->nullable()->after('prioridad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimiento_generals', function (Blueprint $table) {
            $table->dropColumn('prioridad_orden');
        });
    }
};
