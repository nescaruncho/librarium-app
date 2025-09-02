<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listalectura', function (Blueprint $table) {
            $table->id('idListaLectura');

            $table->unsignedBigInteger('idUsuario');
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();

            $table->timestamps();

            // Relaciones
            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listalectura');
    }
};
