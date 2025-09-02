<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {

        [$apellido1, $apellido2] = array_pad(explode(' ', $request->apellidos, 2), 2, null);

        $user = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'passwordhash' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
        ]);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
