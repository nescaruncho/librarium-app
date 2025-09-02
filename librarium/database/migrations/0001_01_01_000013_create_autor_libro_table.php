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
        Schema::create('autorLibro', function (Blueprint $table) {
            $table->unsignedBigInteger('idAutor');
            $table->unsignedBigInteger('idLibro');

            $table->foreign('idAutor')
                ->references('idAutor')
                ->on('autor')
                ->onDelete('cascade');

            $table->foreign('idLibro')
                ->references('idLibro')
                ->on('libro')
                ->onDelete('cascade');

            $table->primary(['idAutor', 'idLibro']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autorLibro');
    }
};
