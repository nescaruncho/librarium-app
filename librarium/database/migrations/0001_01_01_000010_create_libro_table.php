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
        Schema::create('libro', function (Blueprint $table) {
            $table->id('idLibro');

            $table->string('titulo', 255);

            $table->unsignedBigInteger('idEditorial')->nullable();
            $table->string('isbn', 20)->nullable();
            $table->date('fechaPublicacion')->nullable();
            $table->integer('numeroPaginas')->nullable();
            $table->text('sinopsis')->nullable();
            $table->string('portadaUrl', 255)->nullable();

            $table->unsignedBigInteger('idIdioma')->nullable();

            $table->timestamps();

            // Claves foráneas
            $table->foreign('idEditorial')
                  ->references('idEditorial')
                  ->on('editorial')
                  ->nullOnDelete();

            $table->foreign('idIdioma')
                  ->references('idIdioma')
                  ->on('idioma')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro');
    }
};
