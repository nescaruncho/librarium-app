<?php

namespace Database\Factories;

use App\Enums\GeneroUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{

    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'passwordhash' => bcrypt('password'), // contraseña por defecto
            'nombre' => $this->faker->firstName(),
            'apellido1' => $this->faker->lastName(),
            'apellido2' => $this->faker->optional()->lastName(),
            'fecha_nacimiento' => $this->faker->date(),
            'genero' => $this->faker->randomElement(GeneroUsuario::cases()),
            'ciudad' => $this->faker->city(),
            'privacidad' => false,
            'notifEmail' => false,
            'temaOscuro' => false,
            'remember_token' => Str::random(10),
        ];
    }
}

