<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistema de Votaciones' }}</title>
    <style>
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f3f4f6; color: #333; min-height: 100vh; }

    .navbar { background: #2d046b; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .navbar h1 { font-size: 1.2rem; }
    .navbar-right { display: flex; align-items: center; gap: 1rem; }
    .navbar a { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; transition: background 0.2s; }
    .navbar a:hover { background: rgba(255,255,255,0.2); }
    .navbar .user-info { font-size: 0.9rem; opacity: 0.9; }

    .btn { display: inline-block; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.95rem; text-decoration: none; transition: all 0.2s; }
    .btn-primary { background: #facc15; color: #333; }
    .btn-primary:hover { background: #eab308; }

    .btn-success { background: #2d0462; color: white; }
    .btn-success:hover { background: #2d046b; }

    .btn-danger { background: #6b7280; color: white; }
    .btn-danger:hover { background: #4b5563; }

    .btn-secondary { background: #9ca3af; color: white; }
    .btn-secondary:hover { background: #6b7280; }

    .btn-sm { padding: 0.35rem 0.8rem; font-size: 0.85rem; }

    .container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }

    .card { background: white; border-radius: 10px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
    .card h2 { margin-bottom: 1rem; color: #2d046b; }
    .card h3 { margin-bottom: 0.75rem; }

    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.3rem; font-weight: 600; font-size: 0.9rem; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }

    .alert { padding: 0.8rem 1rem; border-radius: 6px; margin-bottom: 1rem; }

    .alert-success { background: #fef9c3; color: #713f12; border: 1px solid #fde68a; }
    .alert-error { background: #ede9fe; color: #2d046b; border: 1px solid #c4b5fd; }
    .alert-info { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }

    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .badge-active { background: #fef9c3; color: #713f12; }
    .badge-pending { background: #ede9fe; color: #2d046b; }
    .badge-closed { background: #e5e7eb; color: #374151; }

    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    table th { background: #f9fafb; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; }
    table tr:hover { background: #f3f4f6; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

    .text-center { text-align: center; }
    .text-muted { color: #6b7280; }

    .mt-1 { margin-top: 0.5rem; }
    .mt-2 { margin-top: 1rem; }
    .mb-1 { margin-bottom: 0.5rem; }
    .mb-2 { margin-bottom: 1rem; }

    .flex { display: flex; }
    .flex-between { display: flex; justify-content: space-between; align-items: center; }
    .gap-1 { gap: 0.5rem; }

    .option-card { border: 2px solid #e5e7eb; border-radius: 8px; padding: 1rem; cursor: pointer; transition: all 0.2s; }
    .option-card:hover { border-color: #7c3aed; background: #f5f3ff; }
    .option-card.selected { border-color: #7c3aed; background: #ede9fe; }
    .option-card input[type="radio"], .option-card input[type="checkbox"] { margin-right: 0.5rem; }

    .receipt-box { background: #fef9c3; border: 2px dashed #facc15; border-radius: 8px; padding: 1.5rem; text-align: center; }
    .receipt-code { font-size: 1.5rem; font-weight: bold; color: #7c3aed; letter-spacing: 2px; font-family: monospace; }

    .chart-container { position: relative; height: 300px; margin-top: 1rem; }

    .participation-bar { height: 24px; background: #e5e7eb; border-radius: 12px; overflow: hidden; }
    .participation-fill { height: 100%; background: linear-gradient(90deg, #7c3aed, #a78bfa); border-radius: 12px; transition: width 0.5s; }

    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
    .modal { background: white; border-radius: 12px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }

    @media (max-width: 768px) {
        .grid-2, .grid-3 { grid-template-columns: 1fr; }
        .navbar { flex-direction: column; gap: 0.5rem; }
    }

    </style>
    {{-- BASE LARAVEL + LIVEWIRE:
         @livewireStyles inyecta estilos base necesarios para componentes Livewire. --}}
    @livewireStyles
</head>
<body>
    @auth
    <nav class="navbar">
        <h1>Sistema de Votaciones</h1>
        <div class="navbar-right">
            <span class="user-info">{{ auth()->user()->full_name }}</span>
            <a href="{{ route('elections.index') }}">Votaciones</a>
            <a href="{{ route('verify') }}">Verificar voto</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.elections') }}">Administrar votaciones</a>
                <a href="{{ route('admin.users') }}">Administrar usuarios</a>
            @endif
            {{-- BASE LARAVEL + PROYECTO:
                 Formulario clásico Laravel (no Livewire) para cerrar sesión. --}}
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">Salir</button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="container">
        {{-- LIVEWIRE + BLADE:
             En $slot se renderiza el contenido de cada página/componente. --}}
        {{ $slot }}
    </div>

    {{-- LIVEWIRE:
         Scripts de Livewire para que funcionen wire:click, wire:model, etc. --}}
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
