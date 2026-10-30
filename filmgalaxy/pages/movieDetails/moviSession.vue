<template>
  <v-app class="cinema-app-root">
    <div class="cinema-page-wrapper">
      <Navbar />

      <main class="sessions-page-content">
        <!-- Header con estética cinemática -->
        <header class="page-header">
          <div class="page-header-inner">
            <div class="header-badge">
              <i class="mdi mdi-movie-open-play"></i>
              <span>CARTELERA & SESIONES</span>
            </div>
            <h1 class="page-title">Sesiones de Cine</h1>
            <p class="page-subtitle">
              Elige tu película favorita, selecciona tu horario y reserva tus mejores butacas
            </p>

            <!-- Barra de búsqueda y filtros -->
            <div class="filters-panel">
              <div class="filters-row">
                <!-- Buscador de película -->
                <div class="search-input-wrap">
                  <i class="mdi mdi-magnify search-icon"></i>
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Buscar por película o género..."
                    class="search-input"
                  />
                  <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="clear-search-btn"
                    title="Borrar búsqueda"
                  >
                    <i class="mdi mdi-close"></i>
                  </button>
                </div>

                <!-- Selector de fecha rápida -->
                <div class="filter-select-wrap">
                  <select v-model="selectedDateFilter" class="filter-select">
                    <option value="">Todas las fechas</option>
                    <option v-for="d in uniqueDates" :key="d" :value="d">
                      {{ formatSelectDate(d) }}
                    </option>
                  </select>
                  <i class="mdi mdi-chevron-down select-icon"></i>
                </div>

                <!-- Selector de horario -->
                <div class="filter-select-wrap">
                  <select v-model="selectedHourFilter" class="filter-select">
                    <option value="">Todos los horarios</option>
                    <option value="16:00">16:00 hrs</option>
                    <option value="18:00">18:00 hrs</option>
                    <option value="20:00">20:00 hrs</option>
                  </select>
                  <i class="mdi mdi-chevron-down select-icon"></i>
                </div>

                <!-- Botón de reset / refrescar -->
                <button @click="resetFilters" class="btn-refresh" title="Restablecer filtros">
                  <i class="mdi mdi-refresh"></i>
                  <span>Limpiar</span>
                </button>
              </div>
            </div>
          </div>
        </header>

        <!-- Loading State -->
        <div v-if="sessionsStore.loading" class="state-container">
          <div class="loading-spinner"></div>
          <p class="state-text">Cargando sesiones disponibles...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="sessionsStore.error" class="error-container">
          <i class="mdi mdi-alert-circle-outline error-icon"></i>
          <p class="error-text">{{ sessionsStore.error }}</p>
          <button @click="refreshSessions" class="btn-action-gold mt-4">
            <i class="mdi mdi-reload"></i>
            <span>Reintentar</span>
          </button>
        </div>

        <!-- Empty State (No sessions in DB or no matches after filter) -->
        <div v-else-if="filteredSessions.length === 0" class="empty-container">
          <div class="empty-icon-box">
            <i class="mdi mdi-ticket-outline"></i>
          </div>
          <h2 class="empty-title">
            {{ sessionsStore.sessions.length === 0 ? 'No hay sesiones disponibles en este momento' : 'No se encontraron sesiones con estos filtros' }}
          </h2>
          <p class="empty-desc">
            {{ sessionsStore.sessions.length === 0 ? 'Pronto añadiremos nuevas sesiones a la cartelera.' : 'Prueba a cambiar la fecha o el término de búsqueda.' }}
          </p>
          <button @click="resetFilters" class="btn-action-gold">
            <i class="mdi mdi-refresh"></i>
            <span>Restablecer filtros</span>
          </button>
        </div>

        <!-- Grid de Sesiones -->
        <div v-else class="sessions-grid-wrapper">
          <!-- Contador y filtro activo -->
          <div class="sessions-header-meta">
            <span class="sessions-counter">
              <i class="mdi mdi-filmstrip"></i>
              Mostrando <strong>{{ filteredSessions.length }}</strong> {{ filteredSessions.length === 1 ? 'sesión' : 'sesiones' }}
            </span>
            <button
              v-if="searchQuery || selectedDateFilter || selectedHourFilter"
              @click="resetFilters"
              class="clear-all-link"
            >
              Quitar filtros
            </button>
          </div>

          <div class="sessions-grid">
            <article
              v-for="(session, index) in filteredSessions"
              :key="session.id"
              class="session-card"
              :style="{ animationDelay: `${Math.min(index * 45, 600)}ms` }"
            >
              <!-- Poster y cabecera visual -->
              <div class="card-poster-wrapper">
                <img
                  :src="session.movie?.imagen || 'https://via.placeholder.com/500x750?text=FilmGalaxy'"
                  :alt="session.movie?.titulo || 'Película'"
                  class="card-poster-img"
                  loading="lazy"
                />

                <!-- Vignette / degradados cinematográficos -->
                <div class="card-poster-gradient"></div>

                <!-- Insignia Horario (Destacada en dorado) -->
                <div class="card-time-badge">
                  <i class="mdi mdi-clock-time-four-outline"></i>
                  <span>{{ session.hora }}</span>
                </div>

                <!-- Insignia Calificación -->
                <div v-if="session.movie?.calificacion" class="card-rating-badge">
                  <i class="mdi mdi-star"></i>
                  <span>{{ session.movie.calificacion }}</span>
                </div>

                <!-- Insignias de características especiales sobre la imagen -->
                <div class="card-perks-stack">
                  <span v-if="session.dia_espectador" class="perk-pill blue">
                    <i class="mdi mdi-tag-outline"></i>
                    Día del espectador
                  </span>
                  <span v-if="session.fila_vip_activa" class="perk-pill purple">
                    <i class="mdi mdi-seat-recline-extra"></i>
                    Fila VIP
                  </span>
                </div>

                <!-- Overlay interactivo con sinopsis y detalles al pasar el cursor -->
                <div class="card-hover-overlay">
                  <div class="hover-content">
                    <span v-if="session.movie?.genero" class="hover-genre">
                      {{ session.movie.genero }}
                    </span>
                    <p v-if="session.movie?.sinopsis" class="hover-synopsis">
                      {{ truncateText(session.movie.sinopsis, 120) }}
                    </p>
                    <div class="hover-footer-meta">
                      <span v-if="session.movie?.duracion">
                        <i class="mdi mdi-timer-outline"></i>
                        {{ session.movie.duracion }} min
                      </span>
                      <span v-if="session.movie?.clasificacion">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        +{{ session.movie.clasificacion }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cuerpo de la tarjeta con información detallada -->
              <div class="card-body">
                <!-- Título de la película -->
                <h2 class="card-title" :title="session.movie?.titulo">
                  {{ session.movie?.titulo || 'Película' }}
                </h2>

                <!-- Fecha y Estado -->
                <div class="card-date-row">
                  <div class="date-badge">
                    <i class="mdi mdi-calendar-blank-outline"></i>
                    <span>{{ formatFriendlyDate(session.fecha) }}</span>
                  </div>

                  <span
                    class="status-pill"
                    :class="{
                      'status-available': session.estado === 'disponible',
                      'status-cancelled': session.estado === 'cancelada',
                      'status-ended': session.estado === 'finalizada'
                    }"
                  >
                    <span class="status-dot"></span>
                    {{ session.estado ? session.estado.toUpperCase() : 'DISPONIBLE' }}
                  </span>
                </div>

                <!-- Estrellas y Meta -->
                <div class="card-stars-row" v-if="session.movie?.calificacion">
                  <div class="stars-group">
                    <i
                      v-for="n in 5"
                      :key="n"
                      class="mdi"
                      :class="n <= Math.floor(session.movie.calificacion / 2) ? 'mdi-star star-filled' : 'mdi-star-outline star-empty'"
                    ></i>
                  </div>
                  <span class="score-text">{{ session.movie.calificacion }}/10</span>
                  <span class="dot-separator">•</span>
                  <span class="genre-text">{{ session.movie.genero || 'Cine' }}</span>
                </div>

                <!-- Acciones: Botón de Compra y Botón de Detalles -->
                <div class="card-actions">
                  <button
                    v-if="session.estado === 'disponible' || !session.estado"
                    @click="comprarEntradas(session)"
                    class="btn-buy-session"
                  >
                    <i class="mdi mdi-ticket-confirmation"></i>
                    <span>Comprar Entradas</span>
                  </button>

                  <div v-else class="session-unavailable-banner">
                    <i class="mdi mdi-cancel"></i>
                    <span>No disponible para reserva</span>
                  </div>

                  <button
                    v-if="session.movie?.id"
                    @click="goToMovieDetails(session.movie.id)"
                    class="btn-details-ghost"
                    title="Ver ficha de la película"
                  >
                    <i class="mdi mdi-information-outline"></i>
                    <span>Ficha</span>
                  </button>
                </div>
              </div>
            </article>
          </div>
        </div>
      </main>

      <Footer />
    </div>

    <!-- Modal de inicio de sesión requerido -->
    <div
      v-if="showLoginModal"
      class="modal-backdrop"
      role="dialog"
      aria-modal="true"
      aria-labelledby="login-modal-title"
      @click.self="closeLoginModal"
    >
      <div class="modal-card">
        <div class="modal-icon-wrap">
          <i class="mdi mdi-account-lock-outline"></i>
        </div>
        <h2 id="login-modal-title" class="modal-title">Inicia sesión para continuar</h2>
        <p class="modal-desc">
          Necesitas disponer de una cuenta de usuario en FilmGalaxy para poder reservar tus butacas y entradas.
        </p>
        <div class="modal-buttons">
          <button type="button" class="btn-modal-primary" @click="goToLogin">
            <i class="mdi mdi-login"></i>
            <span>Iniciar sesión</span>
          </button>
          <button type="button" class="btn-modal-secondary" @click="closeLoginModal">
            <span>Ahora no</span>
          </button>
        </div>
      </div>
    </div>
  </v-app>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSessionsStore } from '@/stores/sessionsStore'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const sessionsStore = useSessionsStore()
const { isAuthenticated } = useAuth()

const showLoginModal = ref(false)
const pendingSessionId = ref(null)

// Filtros reactivos
const searchQuery = ref('')
const selectedDateFilter = ref('')
const selectedHourFilter = ref('')

// Fechas únicas para el desplegable
const uniqueDates = computed(() => {
  const dates = new Set()
  sessionsStore.sessions.forEach((s) => {
    if (s.fecha) {
      const dateKey = s.fecha.split('T')[0]
      dates.add(dateKey)
    }
  })
  return Array.from(dates).sort()
})

// Filtrado de sesiones
const filteredSessions = computed(() => {
  const matchesFilters = sessionsStore.sessions.filter((session) => {
    // Filtro por texto (título de película o género)
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const titleMatch = session.movie?.titulo?.toLowerCase().includes(q)
      const genreMatch = session.movie?.genero?.toLowerCase().includes(q)
      if (!titleMatch && !genreMatch) return false
    }

    // Filtro por fecha
    if (selectedDateFilter.value) {
      const sessionDateKey = session.fecha ? session.fecha.split('T')[0] : ''
      if (sessionDateKey !== selectedDateFilter.value) return false
    }

    // Filtro por hora
    if (selectedHourFilter.value) {
      if (session.hora !== selectedHourFilter.value) return false
    }

    return true
  })

  // Evita que una misma película ocupe más de dos tarjetas en esta cartelera.
  const sessionsPerMovie = new Map()
  return matchesFilters
    .slice()
    .sort((a, b) => {
      const dateTimeA = `${a.fecha || ''} ${a.hora || ''}`
      const dateTimeB = `${b.fecha || ''} ${b.hora || ''}`
      return dateTimeA.localeCompare(dateTimeB)
    })
    .filter((session) => {
      const movieKey = session.movie_id ?? session.movie?.id ?? `session-${session.id}`
      const count = sessionsPerMovie.get(movieKey) || 0
      if (count >= 2) return false
      sessionsPerMovie.set(movieKey, count + 1)
      return true
    })
})

const resetFilters = () => {
  searchQuery.value = ''
  selectedDateFilter.value = ''
  selectedHourFilter.value = ''
  refreshSessions()
}

// Formateador amigable de fecha (ej: "Hoy, 20 oct" o "Miércoles, 21 oct")
const formatFriendlyDate = (dateString) => {
  if (!dateString) return 'Fecha no disp.'
  try {
    const sessionDate = new Date(dateString)
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const tomorrow = new Date(today)
    tomorrow.setDate(tomorrow.getDate() + 1)

    const sessionDay = new Date(sessionDate)
    sessionDay.setHours(0, 0, 0, 0)

    if (sessionDay.getTime() === today.getTime()) {
      return 'Hoy, ' + sessionDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
    } else if (sessionDay.getTime() === tomorrow.getTime()) {
      return 'Mañana, ' + sessionDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
    } else {
      return sessionDate.toLocaleDateString('es-ES', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
      })
    }
  } catch (e) {
    return dateString
  }
}

// Formato legible para el select
const formatSelectDate = (dateKey) => {
  try {
    const [year, month, day] = dateKey.split('-').map(Number)
    const d = new Date(year, month - 1, day)
    return d.toLocaleDateString('es-ES', {
      weekday: 'long',
      day: 'numeric',
      month: 'long'
    })
  } catch (e) {
    return dateKey
  }
}

const truncateText = (text, maxLength = 100) => {
  if (!text) return ''
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text
}

// Comprar entradas con control de autenticación
const comprarEntradas = (session) => {
  if (!isAuthenticated.value) {
    pendingSessionId.value = session?.id || null
    showLoginModal.value = true
    return
  }

  if (session && session.id) {
    router.push(`/billets/${session.id}`)
  }
}

const goToMovieDetails = (movieId) => {
  if (movieId) {
    router.push(`/movieDetails/${movieId}`)
  }
}

const closeLoginModal = () => {
  showLoginModal.value = false
  pendingSessionId.value = null
}

const goToLogin = () => {
  const redirect = pendingSessionId.value
    ? `/billets/${pendingSessionId.value}`
    : '/movieDetails/moviSession'
  router.push({
    path: '/login',
    query: { redirect }
  })
}

// Recargar sesiones
const refreshSessions = async () => {
  const targetMovieId = route.params.movieId || route.query.movieId
  if (targetMovieId) {
    await sessionsStore.fetchSessionsByMovieId(targetMovieId)
  } else {
    await sessionsStore.fetchSessions()
  }
}

onMounted(async () => {
  const targetMovieId = route.params.movieId || route.query.movieId
  if (targetMovieId) {
    await sessionsStore.fetchSessionsByMovieId(targetMovieId)
  } else {
    await sessionsStore.fetchSessions()
  }
})
</script>

<style scoped>
/* ─── BASE & WRAPPER ────────────────────────────────────────────────────────── */
.cinema-app-root {
  background: #080a14 !important;
}

:deep(.v-application__wrap) {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: transparent !important;
}

.cinema-page-wrapper {
  min-height: 100vh;
  background: radial-gradient(circle at 50% 0%, #1a203a 0%, #0c0f1c 45%, #06070e 100%);
  color: #F2E9E4;
  display: flex;
  flex-direction: column;
}

.sessions-page-content {
  flex: 1;
  padding-top: 74px; /* Ajuste limpio bajo la Navbar */
}

/* ─── HEADER Y FILTROS ─────────────────────────────────────────────────────── */
.page-header {
  position: relative;
  padding: 2rem 1.5rem 2.25rem;
  text-align: center;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 700px;
  height: 280px;
  background: radial-gradient(ellipse, rgba(200, 169, 110, 0.14) 0%, transparent 70%);
  pointer-events: none;
}

.page-header-inner {
  position: relative;
  max-width: 960px;
  margin: 0 auto;
}

.header-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.35rem 0.95rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.35);
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 1.5px;
  color: #F5D78E;
  margin-bottom: 0.85rem;
  backdrop-filter: blur(10px);
}

.page-title {
  font-size: 2.4rem;
  font-weight: 900;
  color: #F2E9E4;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
}

@media (min-width: 768px) {
  .page-title {
    font-size: 3.25rem;
  }
}

.page-subtitle {
  font-size: 1rem;
  color: #9A8C98;
  max-width: 620px;
  margin: 0 auto 1.75rem;
  line-height: 1.5;
}

/* Panel de Filtros */
.filters-panel {
  background: rgba(22, 26, 46, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 1rem 1.25rem;
  backdrop-filter: blur(16px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
}

.filters-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.search-input-wrap {
  flex: 1.5;
  min-width: 220px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 14px;
  font-size: 1.2rem;
  color: #C8A96E;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.75rem 2.2rem 0.75rem 2.8rem;
  border-radius: 12px;
  border: 1.5px solid rgba(200, 169, 110, 0.25);
  background: rgba(255, 255, 255, 0.05);
  color: #F2E9E4;
  font-size: 0.92rem;
  outline: none;
  transition: all 0.25s ease;
}

.search-input::placeholder {
  color: #6c7293;
}

.search-input:focus {
  border-color: #C8A96E;
  background: rgba(255, 255, 255, 0.08);
  box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.18);
}

.clear-search-btn {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  color: #9A8C98;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: color 0.2s;
}

.clear-search-btn:hover {
  color: #F5D78E;
}

.filter-select-wrap {
  position: relative;
  flex: 1;
  min-width: 170px;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 2.2rem 0.75rem 1rem;
  border-radius: 12px;
  border: 1.5px solid rgba(255, 255, 255, 0.12);
  background: rgba(255, 255, 255, 0.05);
  color: #F2E9E4;
  font-size: 0.88rem;
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: border-color 0.25s;
}

.filter-select:focus {
  border-color: #C8A96E;
}

.filter-select option {
  background: #161a2e;
  color: #F2E9E4;
}

.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c7293;
  pointer-events: none;
  font-size: 1.1rem;
}

.btn-refresh {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-weight: 700;
  font-size: 0.88rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.25s ease;
  box-shadow: 0 4px 14px rgba(200, 169, 110, 0.3);
}

.btn-refresh:hover {
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 100%);
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(200, 169, 110, 0.45);
}

/* ─── ESTADOS: LOADING / ERROR / EMPTY ─────────────────────────────────────── */
.state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 5rem 1rem;
  gap: 1.25rem;
}

.loading-spinner {
  width: 54px;
  height: 54px;
  border: 4px solid rgba(200, 169, 110, 0.2);
  border-top-color: #C8A96E;
  border-radius: 50%;
  animation: spin 0.85s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.state-text {
  color: #9A8C98;
  font-size: 0.95rem;
}

.error-container {
  max-width: 440px;
  margin: 4rem auto;
  text-align: center;
  padding: 2rem;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 18px;
  backdrop-filter: blur(12px);
}

.error-icon {
  font-size: 3rem;
  color: #f87171;
  display: block;
  margin-bottom: 0.75rem;
}

.error-text {
  color: #fca5a5;
  font-size: 0.95rem;
}

.empty-container {
  text-align: center;
  padding: 5rem 1rem;
  max-width: 520px;
  margin: 0 auto;
}

.empty-icon-box {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.25rem;
  background: rgba(200, 169, 110, 0.1);
  border: 1px solid rgba(200, 169, 110, 0.25);
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: #C8A96E;
}

.empty-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.5rem;
}

.empty-desc {
  color: #9A8C98;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.btn-action-gold {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.6rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-weight: 800;
  font-size: 0.92rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(200, 169, 110, 0.35);
}

.btn-action-gold:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(200, 169, 110, 0.5);
}

/* ─── GRID DE SESIONES ─────────────────────────────────────────────────────── */
.sessions-grid-wrapper {
  max-width: 1420px;
  margin: 0 auto;
  padding: 1rem 1.5rem 4rem;
}

.sessions-header-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}

.sessions-counter {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  color: #9A8C98;
  font-size: 0.88rem;
}

.sessions-counter strong {
  color: #F5D78E;
}

.clear-all-link {
  background: transparent;
  border: none;
  color: #C8A96E;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
  transition: color 0.2s;
}

.clear-all-link:hover {
  color: #FFF0B8;
}

.sessions-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 600px) {
  .sessions-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.6rem;
  }
}

@media (min-width: 960px) {
  .sessions-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.85rem;
  }
}

@media (min-width: 1280px) {
  .sessions-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }
}

/* ─── TARJETA DE SESIÓN (ESTILO CINEMA VIP) ────────────────────────────────── */
.session-card {
  background: rgba(20, 24, 46, 0.82);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 22px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  backdrop-filter: blur(14px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
              border-color 0.3s ease,
              box-shadow 0.35s ease;
  animation: cardFadeIn 0.55s ease-out both;
}

@keyframes cardFadeIn {
  from {
    opacity: 0;
    transform: translateY(24px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.session-card:hover {
  transform: translateY(-8px) scale(1.015);
  border-color: rgba(200, 169, 110, 0.45);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55),
              0 0 25px rgba(200, 169, 110, 0.15);
}

/* PÓSTER */
.card-poster-wrapper {
  position: relative;
  aspect-ratio: 2 / 3;
  overflow: hidden;
  background: #0f1222;
}

.card-poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  transition: transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.session-card:hover .card-poster-img {
  transform: scale(1.08);
}

.card-poster-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    0deg,
    rgba(12, 15, 28, 0.96) 0%,
    rgba(12, 15, 28, 0.4) 45%,
    transparent 100%
  );
  pointer-events: none;
}

/* Insignia Horario */
.card-time-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.32rem 0.75rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.85rem;
  font-weight: 900;
  letter-spacing: 0.5px;
  border-radius: 9999px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
  z-index: 3;
}

/* Insignia Calificación */
.card-rating-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.3rem 0.65rem;
  background: rgba(12, 15, 28, 0.85);
  border: 1px solid rgba(200, 169, 110, 0.35);
  color: #F5D78E;
  font-size: 0.78rem;
  font-weight: 800;
  border-radius: 9999px;
  backdrop-filter: blur(8px);
  z-index: 3;
}

.card-rating-badge .mdi {
  color: #F5D78E;
}

/* Insignias de características */
.card-perks-stack {
  position: absolute;
  bottom: 12px;
  left: 12px;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  z-index: 3;
}

.perk-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.22rem 0.6rem;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  backdrop-filter: blur(8px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

.perk-pill.blue {
  background: rgba(59, 130, 246, 0.25);
  border: 1px solid rgba(96, 165, 250, 0.45);
  color: #93c5fd;
}

.perk-pill.purple {
  background: rgba(147, 51, 234, 0.25);
  border: 1px solid rgba(168, 85, 247, 0.45);
  color: #d8b4fe;
}

/* Hover Overlay */
.card-hover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    0deg,
    rgba(10, 12, 26, 0.98) 0%,
    rgba(10, 12, 26, 0.75) 55%,
    rgba(10, 12, 26, 0.15) 100%
  );
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.25rem;
  opacity: 0;
  transition: opacity 0.32s ease;
  z-index: 2;
}

.session-card:hover .card-hover-overlay {
  opacity: 1;
}

.hover-content {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}

.hover-genre {
  color: #F5D78E;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.hover-synopsis {
  color: #C9ADA7;
  font-size: 0.8rem;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.hover-footer-meta {
  display: flex;
  gap: 1rem;
  color: #9A8C98;
  font-size: 0.78rem;
  font-weight: 600;
  margin-top: 0.25rem;
}

.hover-footer-meta span {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}

.hover-footer-meta .mdi {
  color: #C8A96E;
}

/* ─── CUERPO DE LA TARJETA ─────────────────────────────────────────────────── */
.card-body {
  padding: 1.15rem;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
  flex: 1;
}

.card-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #F2E9E4;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.7rem;
}

.card-date-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.date-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #F5D78E;
  font-size: 0.85rem;
  font-weight: 600;
}

.date-badge .mdi {
  color: #C8A96E;
  font-size: 1rem;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.2rem 0.55rem;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 0.3px;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status-available {
  background: rgba(34, 197, 94, 0.16);
  color: #4ade80;
  border: 1px solid rgba(74, 222, 128, 0.3);
}

.status-available .status-dot {
  background: #4ade80;
  box-shadow: 0 0 6px #4ade80;
}

.status-cancelled {
  background: rgba(239, 68, 68, 0.16);
  color: #f87171;
  border: 1px solid rgba(248, 113, 113, 0.3);
}

.status-cancelled .status-dot {
  background: #f87171;
}

.status-ended {
  background: rgba(156, 163, 175, 0.16);
  color: #9ca3af;
  border: 1px solid rgba(156, 163, 175, 0.3);
}

.status-ended .status-dot {
  background: #9ca3af;
}

.card-stars-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  color: #9A8C98;
}

.stars-group {
  display: flex;
  gap: 1px;
}

.star-filled {
  color: #F5D78E;
  font-size: 0.95rem;
}

.star-empty {
  color: rgba(200, 169, 110, 0.28);
  font-size: 0.95rem;
}

.score-text {
  font-weight: 700;
  color: #F2E9E4;
}

.dot-separator {
  color: #6c7293;
}

.genre-text {
  max-width: 110px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ─── ACCIONES DE LA TARJETA ──────────────────────────────────────────────── */
.card-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: auto;
  padding-top: 0.5rem;
}

.btn-buy-session {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.72rem 0.85rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.88rem;
  font-weight: 800;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(200, 169, 110, 0.3);
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-buy-session:hover {
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(200, 169, 110, 0.5);
}

.btn-details-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.72rem 0.85rem;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.14);
  color: #F2E9E4;
  font-size: 0.85rem;
  font-weight: 700;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-details-ghost:hover {
  background: rgba(200, 169, 110, 0.15);
  border-color: #C8A96E;
  color: #F5D78E;
  transform: translateY(-2px);
}

.session-unavailable-banner {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.65rem;
  background: rgba(255, 255, 255, 0.04);
  border: 1px dashed rgba(255, 255, 255, 0.15);
  color: #9A8C98;
  font-size: 0.78rem;
  font-weight: 600;
  border-radius: 12px;
}

/* ─── MODAL LOGIN ─────────────────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 100;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-card {
  width: 100%;
  max-width: 440px;
  background: #14182e;
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 24px;
  padding: 2rem 1.75rem;
  text-align: center;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
  animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modalEnter {
  from {
    opacity: 0;
    transform: scale(0.92) translateY(12px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-icon-wrap {
  width: 64px;
  height: 64px;
  margin: 0 auto 1.25rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: #F5D78E;
}

.modal-title {
  font-size: 1.45rem;
  font-weight: 800;
  color: #F2E9E4;
  margin-bottom: 0.6rem;
}

.modal-desc {
  font-size: 0.92rem;
  color: #C9ADA7;
  line-height: 1.5;
  margin-bottom: 1.75rem;
}

.modal-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

@media (min-width: 480px) {
  .modal-buttons {
    flex-direction: row-reverse;
  }
}

.btn-modal-primary {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.8rem 1.25rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-weight: 800;
  font-size: 0.92rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-modal-primary:hover {
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 100%);
  transform: translateY(-1px);
}

.btn-modal-secondary {
  flex: 1;
  padding: 0.8rem 1.25rem;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #F2E9E4;
  font-weight: 600;
  font-size: 0.92rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-modal-secondary:hover {
  background: rgba(255, 255, 255, 0.12);
}

/* ─── AJUSTES RESPONSIVOS ─────────────────────────────────────────────────── */
@media (max-width: 640px) {
  .page-header {
    padding: 1.5rem 1rem;
  }

  .page-title {
    font-size: 1.95rem;
  }

  .filters-row {
    flex-direction: column;
  }

  .search-input-wrap,
  .filter-select-wrap,
  .btn-refresh {
    width: 100%;
  }

  .sessions-grid-wrapper {
    padding: 0.5rem 1rem 3rem;
  }
}

select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: none !important;
}
</style>
