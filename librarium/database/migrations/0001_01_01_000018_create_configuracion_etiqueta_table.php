<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracionetiqueta', function (Blueprint $table) {
            $table->id('idConfiguracionEtiqueta');

            $table->unsignedBigInteger('idBiblioteca')->unique();

            $table->json('formato');
            $table->string('separador', 5)->default('-');
            $table->unsignedTinyInteger('longitudMaxima')->default(12);

            $table->timestamps();

            // Relación con biblioteca
            $table->foreign('idBiblioteca')
                  ->references('idBiblioteca')
                  ->on('biblioteca')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracionetiqueta');
    }
};
