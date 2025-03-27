<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmGalaxy - Inici de Sessió</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #22223B;
        }

        .login-container {
            display: flex;
            width: 85%;
            max-width: 1200px;
            background-color: #EAE0D5;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            background: url('/api/placeholder/600/900') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(34, 34, 59, 0.7) 0%, rgba(202, 157, 110, 0.3) 100%);
        }

        .login-form {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-title {
            font-size: 2.5rem;
            color: #22223B;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #4A4E69;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #22223B;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #4A4E69;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #C8A96E;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background-color: #C8A96E;
            color: #1C1C1C;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.30s ease, color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #9A8C98;
            color: #F2E9E4;
        }

        .error-message {
            background-color: #C9ADA7;
            color: #1C1C1C;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            {{-- <a href="/" class="login-logo">
            <img src="{{ asset('auth/logo2.png') }}" alt="Logo FilmGalaxy">
            </a> --}}
        </div>
        <div class="login-form">
            <div class="login-header">
                <h1 class="login-title">FilmGalaxy</h1>
                <p class="login-subtitle">Benvingut! Inicieu la vostra sessió</p>
            </div>

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                @if($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email" class="form-label">Correu electrònic</label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Introduïu el vostre correu" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contrasenya</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Introduïu la vostra contrasenya" required>
                </div>

                <button type="submit" class="btn-login">Iniciar sessió</button>
            </form>
        </div>
    </div>
</body>

</html>