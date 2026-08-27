<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(str()->random(16)),
                    'es_admin' => false,
                ]
            );

            Auth::login($user, true);

            request()->session()->regenerate();

            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Error en login con Google: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensaje = config('app.debug')
                ? 'Error de Google: '.$e->getMessage()
                : 'Ocurrió un error al iniciar sesión con Google. Intenta de nuevo.';

            return redirect()->route('home')->with('error', $mensaje);
        }
    }
}