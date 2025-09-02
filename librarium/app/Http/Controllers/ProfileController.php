<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\EmailUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        $userData = [
            'idUsuario' => $user->idUsuario,
            'username' => $user->username,
            'email' => $user->email,
            'nombre' => $user->nombre,
            'apellido1' => $user->apellido1,
            'apellido2' => $user->apellido2,
            'fechaNacimiento' => $user->fecha_nacimiento ? $user->fecha_nacimiento->format('Y-m-d') : null,
            'genero' => $user->genero,
            'ciudad' => $user->ciudad,
            'descripcion' => $user->descripcion,
            'privacidad' => $user->privacidad,
            'notificacionesEmail' => $user->notifEmail,
            'temaOscuro' => $user->temaOscuro
        ];

        return Inertia::render('Profile/Show', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'userData' => $userData
        ]);
    }

    public function account(Request $request): Response
{
    return Inertia::render('Profile/Account', [
        'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
        'status'          => session('status'),
        'auth'            => ['user' => $request->user()],
    ]);
}

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /**
         * @var  \App\Models\Usuario $user
         */
        $user = Auth::user();
        $user->update([
            'username' => $request->username,
            'nombre' => $request->nombre,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'descripcion' => $request->descripcion,
            'genero' => $request->genero,
            'fecha_nacimiento' => $request->fechaNacimiento,
            'ciudad' => $request->ciudad,
        ]);

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    public function updateEmail(EmailUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->email !== $user->email) {
            $user->email = strtolower($request->email);

            if ($user instanceof MustVerifyEmail) {
                $user->email_verified_at = null;
            }

            $user->save();

            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
                return back()->with('status', 'verification-link-sent');
            }
        }

        return back()->with('status', 'profile-updated');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'privacidad' => 'sometimes|string',
            'notifEmail' => 'sometimes|boolean',
            'temaOscuro' => 'sometimes|string',
        ]);

        $user = $request->user();

        if ($request->has('privacidad')) {
            $user->privacidad = $request->privacidad === 'publico';
        }

        if ($request->has('notifEmail')) {
            $user->notifEmail = $request->notifEmail;
        }

        if ($request->has('temaOscuro')) {
            $user->temaOscuro = $request->temaOscuro === 'oscuro';
        }

        $user->save();
        return back()->with('status', 'preferencias-actualizadas');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
