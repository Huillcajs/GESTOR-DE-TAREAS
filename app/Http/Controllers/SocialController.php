<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asume que tienes un modelo User

class SocialController extends Controller
{
    /**
     * Redirige al proveedor de OAuth.
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Maneja la respuesta del proveedor.
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider)
    {
        try {
            // Obtener información del usuario del proveedor
            $socialUser = Socialite::driver($provider)->user();

            // Buscar si el usuario ya existe por ID social o email
            $user = User::where("{$provider}_id", $socialUser->getId())
                        ->orWhere('email', $socialUser->getEmail())
                        ->first();

            if ($user) {
                // 1. Usuario existe: Actualizar token y logear
                $user->update([
                    'provider_id' => $socialUser->getId(),
                    'provider' => $provider,
                ]);
            } else {
                // 2. Usuario no existe: Crear un nuevo usuario
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'password' => \Illuminate\Support\Str::random(10), // Contraseña aleatoria (no se usa)
                    "{$provider}_id" => $socialUser->getId(),
                    'provider' => $provider,
                ]);
            }

            // Logear al usuario
            Auth::login($user, true);

            // Redirigir a la página principal de tareas
            return redirect()->route('tasks.index');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors('No se pudo iniciar sesión con ' . ucfirst($provider) . '.');
        }
    }
}