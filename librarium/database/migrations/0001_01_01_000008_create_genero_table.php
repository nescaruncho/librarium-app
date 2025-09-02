<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genero', function (Blueprint $table) {
            $table->id('idGenero');
            $table->string('nombre', 100);
            $table->unsignedBigInteger('idGeneroPadre')->nullable();

            $table->timestamps();

            $table->foreign('idGeneroPadre')
                  ->references('idGenero')
                  ->on('genero')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genero');
    }
};
