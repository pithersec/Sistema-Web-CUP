<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema CUP - FICCT')</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Source+Sans+3:wght@300;400;600&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --azul: #0d3b6e;
            --azul-claro: #1a5fa8;
            --rojo: #c0392b;
            --rojo-claro: #e74c3c;
            --blanco: #f8f9fc;
            --gris: #5a5a5a;
            --gris-claro: #e2e8f0;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: #f0f4f8;
            display: flex;
            min-height: 100vh;
        }

        /* ── OVERLAY (solo móvil) ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 99;
        }

        .sidebar-overlay.active { display: block; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 220px;
            background: var(--azul);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: transform 0.25s ease;
            z-index: 100;
        }

        .sidebar-header {
            background: var(--azul-claro);
            padding: 20px 16px;
            text-align: center;
            border-bottom: 3px solid var(--rojo);
        }

        .sidebar-header h3 {
            color: white;
            font-size: 16px;
            line-height: 1.3;
        }

        .sidebar-header p {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            margin-top: 2px;
        }

        .nav-section { padding: 16px 0; }

        .nav-label {
            font-size: 10px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 16px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.75);
            font-size: 13px;
            cursor: pointer;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.12);
            color: white;
            border-left-color: var(--rojo);
        }

        .nav-icon { font-size: 15px; width: 18px; text-align: center; }

        /* ── MAIN ── */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: white;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Botón hamburguesa — oculto en desktop */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            padding: 4px 4px 5px 4px;
            background: none;
            border: none;
        }

        .hamburger span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--azul);
            border-radius: 2px;
            transition: all 0.2s;
        }

        .topbar h1 {
            font-family: 'Merriweather', serif;
            color: var(--azul);
            font-size: 18px;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--gris);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--azul-claro);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }

        .username { }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;  /* ← agregar */
            align-items: flex-start; /* ← cambiar de center */
            justify-content: flex-start; /* ← cambiar de center */
            padding: 28px;
        }

        /* Modales */
        .modal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 48px 52px;
            text-align: center;
            max-width: 420px;
            width: 100%;
            margin: auto;
        }

        .icon-circle {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: #fde8e8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .modal h2 {
            font-family: 'Merriweather', serif;
            color: var(--azul);
            font-size: 22px;
            margin-bottom: 10px;
        }

        .modal p {
            color: var(--gris);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .modal p strong { color: var(--azul); }

        .buttons { display: flex; gap: 12px; }

        .btn-cancel {
            flex: 1; padding: 12px;
            border: 1.5px solid var(--gris-claro);
            border-radius: 6px;
            background: white;
            color: var(--gris);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .btn-confirm {
            flex: 1; padding: 12px;
            border: none; border-radius: 6px;
            background: var(--rojo); color: white;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
        }

        .session-info {
            margin-top: 20px;
            padding: 12px 16px;
            background: #f8f9fc;
            border-radius: 6px;
            border-left: 3px solid var(--azul-claro);
            text-align: left;
        }

        .session-info p { font-size: 12px; color: var(--gris); margin-bottom: 0; }
        .session-info strong { color: var(--azul); }

        /* ── RESPONSIVE MOBILE ── */
        @media (max-width: 768px) {

            body { flex-direction: column; }

            /* Sidebar se convierte en drawer */
            .sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            /* Mostrar hamburguesa */
            .hamburger { display: flex; }

            /* Ocultar nombre usuario en móvil */
            .username { display: none; }

            .topbar {
                padding: 12px 16px;
            }

            .topbar h1 { font-size: 15px; }

            .content { padding: 16px; }

            .modal {
                padding: 32px 24px;
            }
        }
    </style>
</head>

<body>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="overlay" onclick="cerrarSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('img/Escudo_FICCT.png') }}" alt="FICCT"
                style="width:140px;height:140px;object-fit:contain;margin:0 auto 8px;display:block;">
            <h3>Sistema de Admisión</h3>
            <p>FICCT · UAGRM</p>
        </div>
        <div class="nav-section">
            <div class="nav-label">Principal</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ Request::is('dashboard*') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>

            @auth
            @php $user = Auth::user(); @endphp

            @if($user && $user->tienePrivilegio('postulantes.ver'))
            <a href="{{ route('postulantes.index') }}" class="nav-item {{ Request::is('admin/postulantes*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span> Postulantes
            </a>
            @endif

            @if($user && $user->tienePrivilegio('docentes.ver'))
            <a href="{{ route('personal.index') }}" class="nav-item {{ Request::is('admin/personal*') ? 'active' : '' }}">
                <span class="nav-icon">👨‍🏫</span> Personal
            </a>
            @endif

            @if($user && $user->tienePrivilegio('carreras.ver'))
            <a href="{{ route('carreras.index') }}" class="nav-item {{ Request::is('admin/carreras*') ? 'active' : '' }}">
                <span class="nav-icon">🎓</span> Carreras y Cupos
            </a>
            @endif

            @if($user && $user->tienePrivilegio('usuarios.ver'))
            <a href="{{ route('usuarios.index') }}" class="nav-item {{ Request::is('admin/usuarios*') || Request::is('admin/perfiles*') ? 'active' : '' }}">
                <span class="nav-icon">👤</span> Usuarios y Perfiles
            </a>
            @endif

            @if($user && $user->tienePrivilegio('notas.ver'))
            <a href="{{ route('notas.index') }}" class="nav-item {{ Request::is('docente/registrar-notas*') ? 'active' : '' }}">
                <span class="nav-icon">📝</span> Exámenes
            </a>
            @endif

            <div class="nav-label">Sistema</div>

            @if($user && $user->tienePrivilegio('bitacora.ver'))
            <a href="{{ route('bitacora.index') }}" class="nav-item {{ Request::is('admin/bitacora*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span> Bitácora
            </a>
            @endif
            @endauth

            <a href="{{ url('/logout-confirm') }}" class="nav-item {{ Request::is('logout-confirm*') ? 'active' : '' }}">
                <span class="nav-icon">🚪</span> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Main -->
    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1>@yield('page_title')</h1>
            </div>
            <div class="user">
                <div class="user-avatar">{{ substr(Auth::user()?->user_name ?? 'AD', 0, 2) }}</div>
                <span class="username">{{ Auth::user()?->user_name ?? 'Administrador' }}</span>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('active');
        }

        function cerrarSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('active');
        }

        // Cerrar sidebar al navegar (click en nav-item en móvil)
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) cerrarSidebar();
            });
        });
    </script>

</body>
</html>