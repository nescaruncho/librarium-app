<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idioma', function (Blueprint $table) {
            $table->id('idIdioma');
            $table->string('nombre', 100)->unique();
            $table->string('codigo', 10)->unique(); // ej: 'es', 'en', 'fr'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idioma');
    }
};
