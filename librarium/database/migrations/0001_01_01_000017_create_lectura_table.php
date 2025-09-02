<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectura', function (Blueprint $table) {
            $table->id('idLectura');

            $table->unsignedBigInteger('idUsuario');
            $table->unsignedBigInteger('idLibro');

            $table->timestamp('fechaInicio');
            $table->timestamp('fechaFin')->nullable();
            $table->enum('estado', ['leyendo', 'completado', 'abandonado'])->default('leyendo');

            $table->timestamps();

            // Relaciones
            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('idLibro')
                  ->references('idLibro')
                  ->on('libro')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectura');
    }
};
