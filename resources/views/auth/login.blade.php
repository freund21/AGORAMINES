<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema de Votaciones</title>
    <style>
       * { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #210355 0%, #a78bfa 100%);
    min-height: 100vh; display: flex; align-items: center; justify-content: center;}

.login-card { background: white; border-radius: 12px; padding: 2.5rem; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);}

.login-card h1 { text-align: center; color: #7c3aed; margin-bottom: 0.5rem; font-size: 1.5rem;}

.login-card p { text-align: center; color: #6b7280; margin-bottom: 2rem;}

.form-group { margin-bottom: 1.2rem; }

.form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; color: #374151;}

.form-group input { width: 100%; padding: 0.7rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem;}

.form-group input:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1);}

.btn { width: 100%; padding: 0.8rem; background: #facc15; color: #333; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; font-weight: 600;}

.btn:hover { background: #eab308;}

.error { color: #6b7280; font-size: 0.85rem; margin-top: 0.3rem;}

.error-box { background: #ede9fe; color: #4c1d95; padding: 0.7rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem;}
    
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Sistema de Votaciones</h1>
        <p>Centro Educativo</p>

        {{-- BASE LARAVEL + PROYECTO:
             Blade de Laravel:
             muestra primer error de validación devuelto por AuthController@login --}}
        @if($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        {{-- BASE LARAVEL + PROYECTO:
             Formulario HTTP clásico (no Livewire).
             Se envía por POST a la ruta login del controlador. --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
