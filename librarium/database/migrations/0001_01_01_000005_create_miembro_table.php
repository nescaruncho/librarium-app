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
        Schema::create('miembro', function (Blueprint $table) {
            $table->id('idMiembro');

            $table->unsignedBigInteger('idUsuario');
            $table->unsignedBigInteger('idBiblioteca');

            $table->enum('rol', ['propietario', 'admin', 'lector']);

            $table->timestamps();

            // Claves foráneas
            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('idBiblioteca')
                  ->references('idBiblioteca')
                  ->on('biblioteca')
                  ->onDelete('cascade');

            // Evitar duplicados de usuarios en la misma biblioteca
            $table->unique(['idUsuario', 'idBiblioteca']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembro');
    }
};