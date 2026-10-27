<!DOCTYPE html>
<html lang="ca">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Tauler Administratiu')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons & FontAwesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Fons general amb degradat entre dos tons foscos */
    body {
      background: linear-gradient(135deg, #1b1b2a 0%, #2b2d42 50%, #3a3f58 100%);
      color: #EAE0D5;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    /* Estils per a la barra de navegació Cinema */
    .navbar-cinema {
      background: rgba(18, 20, 32, 0.96);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(200, 169, 110, 0.22);
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.45);
      padding: 0.75rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-brand-cinema {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: #F5D78E !important;
      font-weight: 800;
      font-size: 1.55rem;
      letter-spacing: 0.5px;
      transition: transform 0.25s ease;
    }

    .navbar-brand-cinema:hover {
      transform: scale(1.02);
    }

    .navbar-brand-cinema .brand-logo-img {
      height: 38px;
      width: auto;
      filter: drop-shadow(0 2px 8px rgba(200, 169, 110, 0.4));
    }

    .navbar-brand-cinema .brand-badge {
      background: linear-gradient(135deg, rgba(200, 169, 110, 0.2) 0%, rgba(200, 169, 110, 0.08) 100%);
      border: 1px solid rgba(200, 169, 110, 0.5);
      color: #F5D78E;
      font-size: 0.68rem;
      font-weight: 800;
      padding: 0.2rem 0.6rem;
      border-radius: 999px;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      box-shadow: 0 2px 8px rgba(200, 169, 110, 0.15);
    }

    /* Navigation Links */
    .nav-links-cinema {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .nav-link-cinema {
      color: rgba(234, 224, 213, 0.8) !important;
      font-size: 0.94rem;
      font-weight: 600;
      letter-spacing: 0.2px;
      padding: 0.55rem 1.05rem;
      border-radius: 12px;
      border: 1px solid transparent;
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link-cinema i {
      font-size: 1.15rem;
      opacity: 0.85;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .nav-link-cinema:hover {
      color: #F5D78E !important;
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(200, 169, 110, 0.25);
      transform: translateY(-1px);
    }

    .nav-link-cinema:hover i {
      opacity: 1;
      transform: scale(1.1);
    }

    .nav-link-cinema.active {
      color: #F5D78E !important;
      background: linear-gradient(135deg, rgba(200, 169, 110, 0.22) 0%, rgba(200, 169, 110, 0.08) 100%);
      border: 1px solid rgba(200, 169, 110, 0.45);
      box-shadow: 0 4px 14px rgba(200, 169, 110, 0.2);
    }

    .nav-link-cinema.active i {
      opacity: 1;
      color: #F5D78E;
    }

    /* User Profile & Logout */
    .user-pill {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 999px;
      padding: 0.35rem 0.9rem 0.35rem 0.45rem;
      display: inline-flex;
      align-items: center;
      gap: 0.65rem;
      transition: all 0.2s ease;
    }

    .user-pill:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(200, 169, 110, 0.3);
    }

    .user-avatar-sm {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #a47e34, #d4af37);
      color: #ffffff;
      font-weight: 800;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(164, 126, 52, 0.4);
    }

    .user-info-text {
      display: flex;
      flex-direction: column;
      line-height: 1.15;
    }

    .user-name-text {
      color: #F2E9E4;
      font-weight: 700;
      font-size: 0.85rem;
    }

    .user-role-text {
      color: #C8A96E;
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .logout-btn-cinema {
      background: rgba(220, 53, 69, 0.1);
      border: 1px solid rgba(220, 53, 69, 0.35);
      color: #fca5a5 !important;
      border-radius: 10px;
      padding: 0.5rem 1rem;
      font-weight: 600;
      font-size: 0.88rem;
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      transition: all 0.25s ease;
      cursor: pointer;
    }

    .logout-btn-cinema:hover {
      background: #dc3545;
      border-color: #dc3545;
      color: #ffffff !important;
      box-shadow: 0 4px 14px rgba(220, 53, 69, 0.4);
      transform: translateY(-1px);
    }

    .navbar-toggler {
      border-color: #C8A96E;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23C8A96E' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Contenidor principal amb acabat modern */
    main {
      background-color: #ffffff;
      color: #1f2430;
      border-radius: 16px;
      padding: 2.25rem;
      margin: 2rem auto;
      max-width: 1360px;
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
    }

    @media (max-width: 991px) {
      .nav-links-cinema {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
        margin: 1rem 0 !important;
      }
      .nav-link-cinema {
        padding: 0.75rem 1rem;
      }
      main {
        margin: 1rem;
        padding: 1.25rem;
        border-radius: 12px;
      }
    }
  </style>
  @yield('styles')
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-cinema">
    <div class="container-fluid px-3 px-xl-5">
      <a class="navbar-brand-cinema" href="{{ route('users.index') }}">
        <img src="{{ asset('auth/logofilmgalaxyTransparente.png') }}" alt="FilmGalaxy" class="brand-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
        <i class="bi bi-film text-warning me-1" style="display:none; font-size: 1.6rem;"></i>
        <span>FilmGalaxy</span>
        <span class="brand-badge">ADMIN</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Commuta la navegació">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="nav-links-cinema mx-auto my-3 my-lg-0">
          <li>
            <a class="nav-link-cinema {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
              <i class="bi bi-people-fill"></i>
              <span>Usuaris</span>
            </a>
          </li>
          <li>
            <a class="nav-link-cinema {{ request()->is('movies*') ? 'active' : '' }}" href="{{ route('movies.index') }}">
              <i class="bi bi-camera-reels-fill"></i>
              <span>Pel·lícules</span>
            </a>
          </li>
          <li>
            <a class="nav-link-cinema {{ request()->is('movieSessions*') ? 'active' : '' }}" href="{{ route('movieSessions.index') }}">
              <i class="bi bi-calendar2-week-fill"></i>
              <span>Sessions</span>
            </a>
          </li>
          <li>
            <a class="nav-link-cinema {{ request()->is('tickets*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
              <i class="bi bi-ticket-perforated-fill"></i>
              <span>Entrades</span>
            </a>
          </li>
          <li>
            <a class="nav-link-cinema {{ request()->is('stats*') ? 'active' : '' }}" href="{{ route('stats.index') }}">
              <i class="bi bi-graph-up-arrow"></i>
              <span>Estadístiques</span>
            </a>
          </li>
        </ul>

        <div class="d-flex align-items-center gap-3 ms-lg-auto">
          @if(Auth::check())
            <div class="user-pill d-none d-sm-inline-flex">
              <div class="user-avatar-sm">
                {{ strtoupper(substr(Auth::user()->nombre ?? 'A', 0, 1)) }}
              </div>
              <div class="user-info-text">
                <span class="user-name-text">{{ Auth::user()->nombre ?? 'Admin' }}</span>
                <span class="user-role-text">Administrador</span>
              </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
              @csrf
              <button class="logout-btn-cinema" type="submit" title="Tancar Sessió">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-md-inline">Tancar Sessió</span>
              </button>
            </form>
          @else
            <a class="nav-link-cinema active" href="{{ route('login') }}">
              <i class="bi bi-box-arrow-in-right"></i>
              <span>Iniciar Sessió</span>
            </a>
          @endif
        </div>
      </div>
    </div>
  </nav>

  <main class="py-4">
    @yield('content')
  </main>

  <footer class="text-center py-4" style="font-size: 0.82rem;">
    <div class="container">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill" style="background: rgba(22, 26, 46, 0.85); border: 1px solid rgba(200, 169, 110, 0.3); color: #EAE0D5;">
        <i class="bi bi-info-circle-fill text-warning"></i>
        <span><strong>FilmGalaxy Admin</strong> &bull; Projecte Acadèmic DAW (Sense validesa comercial)</span>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>

</html>