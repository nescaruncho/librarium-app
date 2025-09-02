<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id('idNotificacion');

            $table->unsignedBigInteger('idUsuario');

            $table->string('tipo', 100);
            $table->string('titulo', 255)->nullable();

            $table->text('mensaje');

            $table->string('accion', 100)->nullable();

            $table->json('datosExtra')->nullable();   // información adicional para redirigir u ofrecer contexto
            $table->boolean('leido')->default(false);

            $table->timestamps();

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
