<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmGalaxy - Inici de Sessió Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: radial-gradient(circle at 15% 15%, #1f2544 0%, #0c0e1a 50%, #07080f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #F2E9E4;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glowing circles */
        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            opacity: 0.4;
            z-index: 1;
        }

        .glow-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #C8A96E 0%, transparent 70%);
            top: -120px;
            left: -120px;
        }

        .glow-2 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, #5b3d7a 0%, transparent 70%);
            bottom: -150px;
            right: -100px;
        }

        /* Container */
        .login-container {
            display: flex;
            width: 100%;
            max-width: 1050px;
            background: rgba(22, 26, 46, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(200, 169, 110, 0.25);
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.75), 0 0 35px rgba(200, 169, 110, 0.08);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        /* Left Showcase */
        .login-showcase {
            flex: 1.1;
            background: linear-gradient(145deg, rgba(34, 34, 59, 0.9) 0%, rgba(15, 17, 30, 0.96) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .login-showcase::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 1px;
            background: linear-gradient(to bottom, transparent, rgba(200, 169, 110, 0.4), transparent);
        }

        .brand-logo {
            height: 56px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(200, 169, 110, 0.35));
            margin-bottom: 1.5rem;
        }

        .badge-vip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            background: rgba(200, 169, 110, 0.15);
            border: 1px solid rgba(200, 169, 110, 0.3);
            border-radius: 9999px;
            color: #C8A96E;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            width: fit-content;
        }

        .showcase-title {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            color: #F2E9E4;
            margin-bottom: 1rem;
        }

        .text-gold-gradient {
            background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-description {
            color: #C9ADA7;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .perks-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .perk-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .perk-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(200, 169, 110, 0.12);
            border: 1px solid rgba(200, 169, 110, 0.25);
            color: #C8A96E;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .perk-text strong {
            display: block;
            color: #F2E9E4;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .perk-text span {
            color: #9A8C98;
            font-size: 0.8rem;
        }

        /* Right Form */
        .login-form-section {
            flex: 1;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-title {
            font-size: 1.85rem;
            color: #F2E9E4;
            font-weight: 800;
            margin-bottom: 0.4rem;
        }

        .login-subtitle {
            color: #9A8C98;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.45rem;
            color: #EAE0D5;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            transition: all 0.25s ease;
        }

        .input-wrapper:hover {
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.06);
        }

        .input-wrapper:focus-within {
            border-color: #C8A96E;
            box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.2);
            background: rgba(255, 255, 255, 0.07);
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #9A8C98;
            font-size: 1.25rem;
        }

        .form-control {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            color: #F2E9E4;
            font-size: 0.95rem;
            padding: 0.9rem 1rem 0.9rem 46px;
        }

        .form-control::placeholder {
            color: #6c7293;
        }

        .toggle-pwd {
            background: transparent;
            border: none;
            color: #9A8C98;
            font-size: 1.25rem;
            padding: 0 14px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .toggle-pwd:hover {
            color: #C8A96E;
        }

        .btn-login {
            width: 100%;
            padding: 1rem 1.5rem;
            margin-top: 0.75rem;
            background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
            color: #121526;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(200, 169, 110, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(200, 169, 110, 0.5);
            background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 50%, #C8A96E 100%);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.88rem;
        }

        .error-message ul {
            list-style: none;
        }

        @media (max-width: 900px) {
            .login-showcase {
                display: none;
            }

            .login-container {
                max-width: 480px;
            }

            .login-form-section {
                padding: 2.5rem 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <div class="login-container">
        <!-- Left Showcase -->
        <div class="login-showcase">
            <div>
                <img src="{{ asset('auth/logofilmgalaxyTransparente.png') }}" alt="FilmGalaxy" class="brand-logo" onerror="this.src='{{ asset('auth/logo2.png') }}'">
                <div class="badge-vip">
                    <i class="mdi mdi-shield-crown-outline"></i>
                    PANEL DE CONTROL
                </div>
                <h2 class="showcase-title">
                    FilmGalaxy <br>
                    <span class="text-gold-gradient">Administració</span>
                </h2>
                <p class="showcase-description">
                    Accedeix a la gestió integral de sales, cartellera, sessions i reserva de butaques VIP.
                </p>

                <div class="perks-list">
                    <div class="perk-item">
                        <div class="perk-icon">
                            <i class="mdi mdi-filmstrip"></i>
                        </div>
                        <div class="perk-text">
                            <strong>Gestió de Cartellera</strong>
                            <span>Programació i horaris de projecció</span>
                        </div>
                    </div>

                    <div class="perk-item">
                        <div class="perk-icon">
                            <i class="mdi mdi-seat-recline-extra"></i>
                        </div>
                        <div class="perk-text">
                            <strong>Control de Sales & Butaques</strong>
                            <span>Disponibilitat i gestió d'aforament</span>
                        </div>
                    </div>

                    <div class="perk-item">
                        <div class="perk-icon">
                            <i class="mdi mdi-chart-box-outline"></i>
                        </div>
                        <div class="perk-text">
                            <strong>Estadístiques & Vendes</strong>
                            <span>Informes de recaptació en temps real</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Form -->
        <div class="login-form-section">
            <div class="login-header">
                <h1 class="login-title">Benvingut! 👋</h1>
                <p class="login-subtitle">Inicieu la vostra sessió d'administrador</p>
            </div>

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                @if($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li><i class="mdi mdi-alert-circle-outline"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email" class="form-label">Correu electrònic</label>
                    <div class="input-wrapper">
                        <i class="mdi mdi-email-outline"></i>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="administrador@filmgalaxy.cat" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contrasenya</label>
                    <div class="input-wrapper">
                        <i class="mdi mdi-lock-outline"></i>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-pwd" id="togglePasswordBtn">
                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="mdi mdi-login"></i>
                    Iniciar sessió
                </button>
            </form>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
            });
        }
    </script>
</body>

</html>