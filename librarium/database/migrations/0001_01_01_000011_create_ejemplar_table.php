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
        Schema::create('ejemplar', function (Blueprint $table) {
            $table->id('idEjemplar');

            $table->unsignedBigInteger('idBiblioteca');
            $table->unsignedBigInteger('idLibro');

            $table->string('etiqueta', 16)->unique()->nullable();
            $table->boolean('disponible')->default(true);
            $table->string('ubicacion', 100)->nullable();

            $table->timestamps();

            // Claves foráneas
            $table->foreign('idBiblioteca')
                  ->references('idBiblioteca')
                  ->on('biblioteca')
                  ->onDelete('cascade');

            $table->foreign('idLibro')
                  ->references('idLibro')
                  ->on('libro')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejemplar');
    }
};
