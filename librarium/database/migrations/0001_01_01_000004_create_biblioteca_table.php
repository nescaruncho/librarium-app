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
        Schema::create('biblioteca', function (Blueprint $table) {
            $table->id('idBiblioteca');
            $table->unsignedBigInteger('idPropietario');
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('visibilidad')->default(true);
            $table->boolean('prestamosHabilitados')->default(true);
            $table->boolean('etiquetasHabilitadas')->default(true);
            $table->timestamps();

            $table->foreign('idPropietario')
                ->references('idUsuario')
                ->on('usuario')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblioteca');
    }
};
