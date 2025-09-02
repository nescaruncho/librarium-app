<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generoLibro', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idLibro');
            $table->unsignedBigInteger('idGenero');

            $table->timestamps();

            $table->foreign('idLibro')
                  ->references('idLibro')
                  ->on('libro')
                  ->onDelete('cascade');

            $table->foreign('idGenero')
                  ->references('idGenero')
                  ->on('genero')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generoLibro');
    }
};
