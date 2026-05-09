<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// BASE LARAVEL + PROYECTO:
// Controlador clásico de Laravel (no Livewire).
// Aquí se ha picado el flujo de autenticación propio: mostrar login, entrar y salir.
class AuthController extends Controller
{
    // PROYECTO + BASE LARAVEL:
    // Muestra la pantalla de login.
    // Laravel: Auth::check(), redirect(), view().
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('elections.index');
        }
        return view('auth.login');
    }

    // PROYECTO + BASE LARAVEL:
    // Procesa el formulario POST /login.
    // Laravel valida datos y prueba credenciales contra la BD.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            // BASE LARAVEL:
            // Seguridad de sesión: regenera identificador para evitar fixation.
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // PROYECTO:
        // Si falla, vuelve atrás con mensaje de error propio.
        return back()->withErrors([
            'username' => 'Las credenciales no son correctas.',
        ])->onlyInput('username');
    }

    // BASE LARAVEL + PROYECTO:
    // Cierra sesión del usuario actual desde la ruta /logout.
    public function logout(Request $request)
    {
        Auth::logout();
        // BASE LARAVEL:
        // Limpia sesión y token CSRF.
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
