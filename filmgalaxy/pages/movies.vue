<template>
  <v-app class="cinema-app-root">
    <div class="cinema-page-wrapper">
      <!-- Barra de navegación -->
      <Navbar />

      <div class="movies-page-content">
        <!-- Encabezado principal -->
        <div class="page-header">
          <div class="page-header-inner">
            <div class="header-tag">
              <i class="mdi mdi-film"></i>
              <span>CARTELLERA</span>
            </div>
            <h1 class="page-title">Pel·lícules en Cartellera</h1>
            <p class="page-subtitle">Descobreix les millors estrenes del moment</p>

            <!-- Secció per cerca i filtres -->
            <div class="search-bar-wrapper">
              <div class="search-row">
                <!-- Barra de cerca -->
                <div class="search-input-wrap">
                  <i class="mdi mdi-magnify search-icon"></i>
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Quina pel·lícula busques?"
                    class="search-input"
                  />
                </div>

                <!-- Filtre per idioma -->
                <div class="filter-select-wrap">
                  <select v-model="filterLenguaje" class="filter-select">
                    <option value="">Tots els idiomes</option>
                    <option value="Español">Espanyol</option>
                    <option value="Inglés">Anglès</option>
                    <option value="Subtitulada">Subtitulada</option>
                  </select>
                  <i class="mdi mdi-chevron-down select-icon"></i>
                </div>

                <!-- Botó netejar -->
                <button @click="resetFilters" class="btn-reset">
                  <i class="mdi mdi-refresh"></i>
                  <span>Netejar</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Indicador mentre es carreguen les dades -->
        <div v-if="movieStore.loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p class="loading-text">Carregant pel·lícules...</p>
        </div>

        <!-- Missatge d'error -->
        <div v-else-if="movieStore.error" class="error-state">
          <i class="mdi mdi-alert-circle-outline error-icon"></i>
          <p>{{ movieStore.error }}</p>
        </div>

        <!-- Avís quan no hi ha resultats -->
        <div v-else-if="filteredMovies.length === 0" class="empty-state">
          <i class="mdi mdi-movie-search-outline empty-icon"></i>
          <p class="empty-title">No s'han trobat pel·lícules</p>
          <p class="empty-sub">Intenta amb altres filtres de cerca</p>
          <button @click="resetFilters" class="btn-reset-empty">
            <i class="mdi mdi-refresh"></i>
            Netejar Filtres
          </button>
        </div>

        <!-- Cuadrícula de pel·lícules -->
        <div v-else class="movies-grid-wrapper">
          <div class="movies-grid">
            <div
              v-for="(movie, index) in filteredMovies"
              :key="movie.id"
              class="movie-card"
              :style="{ animationDelay: `${index * 60}ms` }"
            >
              <!-- Pòster -->
              <div class="card-poster">
                <img
                  :src="movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'"
                  :alt="movie.titulo"
                  class="card-poster-img"
                />

                <!-- Gradient overlay always visible at bottom -->
                <div class="card-poster-gradient"></div>

                <!-- Rating badge top-right -->
                <div class="card-rating-badge">
                  <i class="mdi mdi-star"></i>
                  <span>{{ movie.calificacion || 'N/D' }}</span>
                </div>

                <!-- Language badge top-left -->
                <div class="card-lang-badge" v-if="movie.lenguaje">
                  {{ movie.lenguaje }}
                </div>

                <!-- Hover overlay with quick info -->
                <div class="card-hover-overlay">
                  <p class="hover-genre">{{ movie.genero || 'Gènere desconegut' }}</p>
                  <p class="hover-synopsis">{{ truncateSynopsis(movie.sinopsis) }}</p>
                  <div class="hover-meta">
                    <span v-if="movie.duracion">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ formatDuration(movie.duracion) }}
                    </span>
                    <span v-if="movie.fecha_estreno">
                      <i class="mdi mdi-calendar"></i>
                      {{ formatDate(movie.fecha_estreno) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Card body -->
              <div class="card-body">
                <h2 class="card-title">{{ movie.titulo }}</h2>

                <div class="card-meta-row">
                  <span class="card-duration" v-if="movie.duracion">
                    <i class="mdi mdi-clock-outline"></i>
                    {{ movie.duracion }} min
                  </span>
                  <span class="card-genre" v-if="movie.genero">{{ movie.genero }}</span>
                </div>

                <!-- Stars -->
                <div class="card-stars" v-if="movie.calificacion">
                  <i
                    v-for="n in 5"
                    :key="n"
                    class="mdi"
                    :class="n <= Math.floor(movie.calificacion / 2) ? 'mdi-star star-on' : 'mdi-star-outline star-off'"
                  ></i>
                  <span class="card-score">{{ movie.calificacion }}/10</span>
                </div>

                <!-- Action buttons -->
                <div class="card-actions">
                  <button @click="goToDetails(movie.id)" class="btn-details">
                    <i class="mdi mdi-information-outline"></i>
                    <span>Detalls</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Results count -->
          <p class="results-count">
            {{ filteredMovies.length }} pel·lícula{{ filteredMovies.length !== 1 ? 's' : '' }} trobada{{ filteredMovies.length !== 1 ? 's' : '' }}
          </p>
        </div>
      </div>

      <!-- Peu de pàgina -->
      <Footer />
    </div>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMovieStore } from '~/stores/movieStore';

const movieStore = useMovieStore();
const router = useRouter();

const searchQuery = ref('');
const filterLenguaje = ref('');

onMounted(async () => {
  if (movieStore.allMovies.length === 0) {
    await movieStore.fetchMovies();
  }
});

const filteredMovies = computed(() => {
  if (!Array.isArray(movieStore.allMovies)) {
    console.error("allMovies no és un array:", movieStore.allMovies);
    return [];
  }

  return movieStore.allMovies.filter(movie => {
    const searchMatch = !searchQuery.value ||
      (movie.titulo?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (movie.genero?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (movie.sinopsis?.toLowerCase().includes(searchQuery.value.toLowerCase()));

    const matchLenguaje = !filterLenguaje.value || (movie.lenguaje === filterLenguaje.value);

    return searchMatch && matchLenguaje;
  });
});

const resetFilters = () => {
  searchQuery.value = '';
  filterLenguaje.value = '';
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  return date.toLocaleDateString('ca-ES', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatDuration = (minutes) => {
  if (!minutes) return '';
  const mins = typeof minutes === 'string' ? parseInt(minutes) : minutes;
  if (isNaN(mins)) return '';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return `${h}h ${m.toString().padStart(2, '0')}min`;
};

const truncateSynopsis = (text, maxLen = 100) => {
  if (!text) return '';
  return text.length > maxLen ? text.slice(0, maxLen) + '…' : text;
};

const goToDetails = (id) => {
  router.push(`/movieDetails/${id}`);
};
</script>

<style scoped>
/* ─── ROOT ─────────────────────────────────────────────────────────────────── */
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

.movies-page-content {
  flex: 1;
  padding-top: 74px; /* navbar initial height */
}

/* ─── PAGE HEADER ───────────────────────────────────────────────────────────── */
.page-header {
  position: relative;
  padding: 1.5rem 1.5rem 2rem;
  text-align: center;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: 0; left: 50%;
  transform: translateX(-50%);
  width: 600px; height: 260px;
  background: radial-gradient(ellipse, rgba(200,169,110,0.12) 0%, transparent 70%);
  pointer-events: none;
}

.page-header-inner {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
}

.header-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.85rem;
  background: rgba(200, 169, 110, 0.14);
  border: 1px solid rgba(200, 169, 110, 0.35);
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  color: #F5D78E;
  margin-bottom: 0.65rem;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 900;
  color: #F2E9E4;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
  text-shadow: 0 4px 20px rgba(0,0,0,0.5);
}

@media (min-width: 768px) {
  .page-title {
    font-size: 3.25rem;
  }
}

.page-subtitle {
  font-size: 1rem;
  color: #9A8C98;
  margin-bottom: 1.4rem;
}

/* ─── SEARCH BAR ───────────────────────────────────────────────────────────── */
.search-bar-wrapper {
  max-width: 860px;
  margin: 0 auto;
  background: rgba(22, 26, 46, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-radius: 18px;
  padding: 1rem 1.25rem;
  backdrop-filter: blur(16px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.search-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.search-input-wrap {
  flex: 1;
  min-width: 200px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 14px;
  font-size: 1.15rem;
  color: #C8A96E;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.72rem 1rem 0.72rem 2.75rem;
  border-radius: 12px;
  border: 1.5px solid rgba(200, 169, 110, 0.25);
  background: rgba(255, 255, 255, 0.05);
  color: #F2E9E4;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.25s, box-shadow 0.25s;
}

.search-input::placeholder {
  color: #6c7293;
}

.search-input:focus {
  border-color: #C8A96E;
  box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.15);
}

.filter-select-wrap {
  position: relative;
  min-width: 160px;
}

.filter-select {
  width: 100%;
  padding: 0.72rem 2.2rem 0.72rem 1rem;
  border-radius: 12px;
  border: 1.5px solid rgba(255, 255, 255, 0.12);
  background: rgba(255, 255, 255, 0.05);
  color: #F2E9E4;
  font-size: 0.9rem;
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
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c7293;
  pointer-events: none;
  font-size: 1rem;
}

.btn-reset {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.72rem 1.25rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-weight: 700;
  font-size: 0.9rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(200, 169, 110, 0.3);
}

.btn-reset:hover {
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 100%);
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(200, 169, 110, 0.45);
}

/* ─── LOADING / ERROR / EMPTY ──────────────────────────────────────────────── */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 5rem 1rem;
  gap: 1.2rem;
}

.loading-spinner {
  width: 56px;
  height: 56px;
  border: 4px solid rgba(200, 169, 110, 0.2);
  border-top-color: #C8A96E;
  border-radius: 50%;
  animation: spin 0.9s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-text {
  color: #9A8C98;
  font-size: 0.95rem;
}

.error-state {
  max-width: 400px;
  margin: 4rem auto;
  text-align: center;
  color: #f87171;
  padding: 2rem;
}

.error-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
}

.empty-state {
  text-align: center;
  padding: 5rem 1rem;
}

.empty-icon {
  font-size: 4rem;
  color: #2e3255;
  display: block;
  margin-bottom: 1rem;
}

.empty-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.4rem;
}

.empty-sub {
  color: #9A8C98;
  margin-bottom: 1.5rem;
}

.btn-reset-empty {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.75rem 1.5rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.4);
  color: #F5D78E;
  font-size: 0.9rem;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s;
}

.btn-reset-empty:hover {
  background: rgba(200, 169, 110, 0.25);
  transform: translateY(-1px);
}

/* ─── GRID ──────────────────────────────────────────────────────────────────── */
.movies-grid-wrapper {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0.75rem 1.5rem 3rem;
}

.movies-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.4rem;
}

@media (min-width: 640px) {
  .movies-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.6rem;
  }
}

@media (min-width: 900px) {
  .movies-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.8rem;
  }
}

@media (min-width: 1200px) {
  .movies-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }
}

.results-count {
  margin-top: 2rem;
  text-align: center;
  color: #6c7293;
  font-size: 0.85rem;
}

/* ─── MOVIE CARD ────────────────────────────────────────────────────────────── */
.movie-card {
  background: rgba(20, 24, 44, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 22px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.35);
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
              border-color 0.3s ease,
              box-shadow 0.35s ease;

  /* Entry animation */
  animation: card-enter 0.55s ease-out both;
}

@keyframes card-enter {
  from {
    opacity: 0;
    transform: translateY(28px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.movie-card:hover {
  transform: translateY(-8px) scale(1.015);
  border-color: rgba(200, 169, 110, 0.45);
  box-shadow:
    0 20px 50px rgba(0, 0, 0, 0.5),
    0 0 30px rgba(200, 169, 110, 0.12);
}

/* ─── POSTER ─────────────────────────────────────────────────────────────────── */
.card-poster {
  position: relative;
  /* 2:3 poster ratio */
  aspect-ratio: 2 / 3;
  overflow: hidden;
  flex-shrink: 0;
}

.card-poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  transition: transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.movie-card:hover .card-poster-img {
  transform: scale(1.08);
}

/* Gradient at the bottom of the poster */
.card-poster-gradient {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 55%;
  background: linear-gradient(
    0deg,
    rgba(12, 15, 28, 0.97) 0%,
    rgba(12, 15, 28, 0.5) 50%,
    transparent 100%
  );
  pointer-events: none;
}

/* Rating badge */
.card-rating-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.28rem 0.65rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.8rem;
  font-weight: 800;
  border-radius: 9999px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.45);
  z-index: 3;
}

.card-rating-badge .mdi {
  font-size: 0.85rem;
}

/* Language badge */
.card-lang-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 0.28rem 0.65rem;
  background: rgba(12, 15, 28, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #C9ADA7;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 9999px;
  backdrop-filter: blur(8px);
  z-index: 3;
  letter-spacing: 0.5px;
}

/* Hover overlay (synopsis & details) */
.card-hover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    0deg,
    rgba(10, 12, 26, 0.97) 0%,
    rgba(10, 12, 26, 0.75) 55%,
    rgba(10, 12, 26, 0.1) 100%
  );
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.1rem;
  opacity: 0;
  transition: opacity 0.35s ease;
  z-index: 2;
}

.movie-card:hover .card-hover-overlay {
  opacity: 1;
}

.hover-genre {
  color: #F5D78E;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 0.4rem;
}

.hover-synopsis {
  color: #C9ADA7;
  font-size: 0.8rem;
  line-height: 1.5;
  margin-bottom: 0.75rem;
}

.hover-meta {
  display: flex;
  gap: 1rem;
  color: #9A8C98;
  font-size: 0.78rem;
}

.hover-meta span {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}

.hover-meta .mdi {
  color: #C8A96E;
}

/* ─── CARD BODY ──────────────────────────────────────────────────────────────── */
.card-body {
  padding: 1.1rem 1.15rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
  flex: 1;
}

.card-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #F2E9E4;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta-row {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.card-duration {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  color: #9A8C98;
  font-size: 0.8rem;
}

.card-duration .mdi {
  color: #C8A96E;
  font-size: 0.9rem;
}

.card-genre {
  display: inline-block;
  padding: 0.18rem 0.6rem;
  background: rgba(200, 169, 110, 0.12);
  border: 1px solid rgba(200, 169, 110, 0.25);
  color: #C8A96E;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
}

.card-stars {
  display: flex;
  align-items: center;
  gap: 2px;
}

.star-on {
  color: #F5D78E;
  font-size: 1rem;
}

.star-off {
  color: rgba(200, 169, 110, 0.28);
  font-size: 1rem;
}

.card-score {
  margin-left: 0.4rem;
  color: #9A8C98;
  font-size: 0.78rem;
}

/* ─── ACTION BUTTONS ─────────────────────────────────────────────────────────── */
.card-actions {
  display: flex;
  gap: 0.65rem;
  margin-top: auto;
  padding-top: 0.4rem;
}

.btn-details {
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.7rem 0.5rem;
  border-radius: 12px;
  font-size: 0.88rem;
  font-weight: 700;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.14);
  color: #F2E9E4;
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-details:hover {
  background: rgba(200, 169, 110, 0.15);
  border-color: rgba(200, 169, 110, 0.5);
  color: #F5D78E;
  transform: translateY(-1px);
}

/* ─── RESPONSIVE TWEAKS ──────────────────────────────────────────────────────── */
@media (max-width: 480px) {
  .page-title {
    font-size: 1.9rem;
  }

  .search-row {
    flex-direction: column;
  }

  .search-input-wrap,
  .filter-select-wrap,
  .btn-reset {
    width: 100%;
  }

  .card-body {
    padding: 0.9rem;
  }

  .card-title {
    font-size: 0.95rem;
  }

  .btn-details {
    font-size: 0.82rem;
    padding: 0.6rem 0.4rem;
  }
}

/* ─── SELECT OVERRIDE ────────────────────────────────────────────────────────── */
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: none !important;
}
</style>
