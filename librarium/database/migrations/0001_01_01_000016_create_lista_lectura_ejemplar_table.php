<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_lectura_ejemplar', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idListaLectura');
            $table->unsignedBigInteger('idEjemplar');

            $table->timestamps();

            // Relaciones
            $table->foreign('idListaLectura')
                  ->references('idListaLectura')
                  ->on('listalectura')
                  ->onDelete('cascade');

            $table->foreign('idEjemplar')
                  ->references('idEjemplar')
                  ->on('ejemplar')
                  ->onDelete('cascade');

            // Evitar duplicados
            $table->unique(['idListaLectura', 'idEjemplar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_lectura_ejemplar');
    }
};
