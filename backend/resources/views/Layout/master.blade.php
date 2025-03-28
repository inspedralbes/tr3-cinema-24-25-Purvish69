<!DOCTYPE html>
<html lang="ca">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Tauler Administratiu')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Fons general amb degradat entre dos tons foscos */
    body {
      background: linear-gradient(135deg, #22223B, #4A4E69);
      color: #EAE0D5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* Estils per a la barra de navegació */
    .navbar {
      background-color: rgba(28, 28, 29, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }

    .navbar:hover {
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .navbar-brand {
      color: #C8A96E !important;
      font-weight: bold;
      font-size: 1.75rem;
      display: flex;
      align-items: center;
      transition: transform 0.3s ease;
    }

    .navbar-brand:hover {
      transform: scale(1.05);
    }

    .navbar-brand img {
      margin-right: 10px;
      height: 40px;
    }

    .navbar-nav .nav-link {
      color: #EAE0D5 !important;
      font-weight: 500;
      position: relative;
      transition: color 0.3s ease;
      margin: 0 10px;
      padding: 0.5rem 0;
    }

    .navbar-nav .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 50%;
      background-color: #C8A96E;
      transition: all 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      color: #C8A96E !important;
    }

    .navbar-nav .nav-link:hover::after {
      width: 100%;
      left: 0;
    }

    .navbar-toggler {
      border-color: #C8A96E;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23C8A96E' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .logout-btn {
      background-color: transparent;
      color: #EAE0D5 !important;
      border: 1px solid #C8A96E;
      border-radius: 5px;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #C8A96E;
      color: #1C1C1C !important;
    }

    /* Contenidor principal amb fons clar i subtil ombra */
    main {
      background-color: #F2E9E4;
      color: #22223B;
      border-radius: 8px;
      padding: 2rem;
      margin: 2rem auto;
      max-width: 1200px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 991px) {
      .navbar-nav .nav-link {
        text-align: center;
        margin: 10px 0;
      }
    }
  </style>
  @yield('styles')
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <!-- You could add a logo here -->
        FilmGalaxy
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Commuta la navegació">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">Usuaris</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('movies.index') }}">Pel·lícules</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('movieSessions.index') }}">Crear Sessió</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('tickets.index') }}">Entrades</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('stats.index') }}">Estadístiques</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          @if(Auth::check())
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn logout-btn" type="submit">Tancar Sessió</button>
              </form>
            </li>
          @else
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">Iniciar Sessió</a>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    @yield('content')
  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>

</html>