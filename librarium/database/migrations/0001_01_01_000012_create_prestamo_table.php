<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo', function (Blueprint $table) {
            $table->id('idPrestamo');

            $table->unsignedBigInteger('idEjemplar');
            $table->unsignedBigInteger('idMiembro');

            $table->dateTime('fechaInicio');
            $table->dateTime('fechaFin');
            $table->dateTime('fechaDevolucion')->nullable();
            $table->unsignedTinyInteger('prorrogasUsadas')->default(0);
            $table->boolean('devuelto')->default(false);

            $table->timestamps();

            // Relaciones
            $table->foreign('idEjemplar')
                  ->references('idEjemplar')
                  ->on('ejemplar')
                  ->onDelete('cascade');

            $table->foreign('idMiembro')
                  ->references('idMiembro')
                  ->on('miembro')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo');
    }
};
