<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politicaprestamo', function (Blueprint $table) {
            $table->id('idPoliticaPrestamo');

            $table->unsignedBigInteger('idBiblioteca')->unique();

            $table->unsignedInteger('maxLibrosSimultaneos')->default(4);
            $table->unsignedInteger('duracionPrestamoDias')->default(14);
            $table->unsignedInteger('numeroMaxProrrogas')->default(2);
            $table->unsignedInteger('duracionProrrogaDias')->default(7);

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
        Schema::dropIfExists('politicaprestamo');
    }
};
