<?php

namespace Database\Seeders;

use App\Enums\GeneroUsuario;
use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Crea 10 usuarios falsos
       // Usuario::factory()->count(10)->create();

        // Crea un usuario de prueba específico
        Usuario::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'passwordhash' => bcrypt('admin123'),
            'nombre' => 'Admin',
            'apellido1' => 'Test',
            'apellido2' => 'Usuario',
            'genero' => GeneroUsuario::MASCULINO,
            'privacidad' => false,
            'notifEmail' => false,
            'temaOscuro' => false,
            'email_verified_at' => now(),
        ]);
    }
}
