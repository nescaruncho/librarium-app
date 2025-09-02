<?php

namespace App\Providers;

use App\Models\Ejemplar;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Policies\NotificacionPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject(__('Completa tu registro en Librarium'))
                ->greeting(__('Hola, :name', ['name' => $notifiable->nombre]))
                ->line(__('Haz clic en el botón de abajo para verificar tu dirección de correo electrónico y completar tu registro.'))
                ->action(__('Verificar correo electrónico'), $url)
                ->line(__('Si no has creado una cuenta, puedes ignorar este correo electrónico.'))
                ->salutation(__('Saludos, :appName', ['appName' => config('app.name')]));
        });

        Gate::policy(Notificacion::class, NotificacionPolicy::class);

        Gate::define('accessLibroViaEjemplar', function (Usuario $user, int $idLibro, ?int $idBiblioteca = null): bool {
            $q = Ejemplar::query()
                ->join('miembro', 'miembro.idBiblioteca', '=', 'ejemplar.idBiblioteca')
                ->where('miembro.idUsuario', $user->idUsuario)
                ->where('ejemplar.idLibro', $idLibro);

            if ($idBiblioteca) {
                $q->where('ejemplar.idBiblioteca', $idBiblioteca);
            }

            return $q->exists();
        });
    }
}
