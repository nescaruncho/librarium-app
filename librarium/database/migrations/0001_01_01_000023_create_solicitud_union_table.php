<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_union', function (Blueprint $table) {
            $table->id('idSolicitudUnion');

            $table->unsignedBigInteger('idUsuario');
            $table->unsignedBigInteger('idBiblioteca');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');

            $table->timestamps();

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('idBiblioteca')
                  ->references('idBiblioteca')
                  ->on('biblioteca')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_union');
    }
};
