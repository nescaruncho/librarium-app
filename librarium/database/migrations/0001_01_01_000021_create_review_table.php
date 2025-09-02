<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review', function (Blueprint $table) {
            $table->id('idReview');

            $table->unsignedBigInteger('idLectura');

            $table->tinyInteger('valoracion');
            $table->string('titulo', 100);
            $table->text('contenido');

            $table->timestamps();

            // Relaciones
            $table->foreign('idLectura')
                  ->references('idLectura')
                  ->on('lectura')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
