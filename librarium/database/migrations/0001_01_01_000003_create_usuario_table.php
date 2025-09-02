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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('idUsuario');
            $table->string('username',30)->unique();
            $table->string('email',255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('passwordhash',255);
            $table->string('nombre',50);
            $table->string('apellido1',50);
            $table->string('apellido2',50)->nullable();
            $table->string('descripcion',500)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino'])->nullable();
            $table->string('ciudad',100)->nullable();
            $table->boolean('privacidad')->default(false);
            $table->boolean('notifEmail')->default(true);
            $table->boolean('temaOscuro')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
