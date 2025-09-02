<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_qr', function (Blueprint $table) {
            $table->id('idTokenQR');

            $table->unsignedBigInteger('idEjemplar')->unique(); // Un solo token por ejemplar
            $table->string('token', 255)->unique(); // Será cifrado en modelo

            $table->timestamps();

            $table->foreign('idEjemplar')
                  ->references('idEjemplar')
                  ->on('ejemplar')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_qr');
    }
};
