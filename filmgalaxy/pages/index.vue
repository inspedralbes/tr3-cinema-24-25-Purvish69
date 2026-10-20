<template>
  <v-app class="cinema-app-root">
    <div class="cinema-page-wrapper">
      <!-- Navbar (Floats transparently over the hero) -->
      <Navbar />

      <main class="relative hero-main">
        <!-- Error Alert -->
        <div v-if="movieStore.error"
          class="fixed top-20 right-4 z-50 max-w-md bg-red-500/90 text-white p-4 rounded-xl backdrop-blur-md shadow-2xl animate-fade-in border border-red-400/40">
          <p class="font-medium text-sm flex items-center gap-2">
            <i class="mdi mdi-alert-circle text-lg"></i>
            {{ movieStore.error }}
          </p>
        </div>

        <!-- Loading Overlay -->
        <div v-if="movieStore.loading"
          class="fixed inset-0 bg-[#080a14]/70 backdrop-blur-md flex items-center justify-center z-50">
          <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-[#C8A96E]/20 border-t-[#C8A96E] rounded-full animate-spin"></div>
            <p class="text-sm font-semibold text-[#F5D78E] tracking-wide">Carregant el cinema...</p>
          </div>
        </div>

        <!-- FEATURED HERO CAROUSEL -->
        <section v-if="movieStore.featuredMovies.length > 0" class="hero-carousel-section">
          <div class="carousel-viewport">
            <div 
              v-for="(movie, index) in movieStore.featuredMovies" 
              :key="movie.id"
              class="carousel-slide" 
              :class="{ 'slide-active': currentSlide === index }"
            >
              <!-- Poster Image -->
              <img 
                :src="movie.poster" 
                :alt="movie.titulo" 
                class="carousel-img" 
              />

              <!-- Multi-directional Cinema Vignette Overlays -->
              <div class="vignette-overlay top-vignette"></div>
              <div class="vignette-overlay side-vignette"></div>
              <div class="vignette-overlay bottom-vignette"></div>

              <!-- Hero Content Info -->
              <div class="hero-content-wrapper">
                <div class="hero-content">
                  <!-- VIP Premiere Tag -->
                  <div class="hero-badge animate-fade-up">
                    <i class="mdi mdi-star-shooting text-amber-300"></i>
                    <span>ESTRENA DESTACADA EN CARTELLERA</span>
                  </div>

                  <!-- Movie Title -->
                  <h1 class="hero-title animate-fade-up">
                    {{ movie.titulo }}
                  </h1>

                  <!-- Rating & Meta Info -->
                  <div class="hero-meta-row animate-fade-up" style="animation-delay: 150ms">
                    <div class="rating-badge">
                      <span class="rating-num">{{ movie.calificacion }}</span>
                      <small class="text-xs text-amber-300">/10</small>
                    </div>

                    <div class="star-rating">
                      <i
                        v-for="n in 5"
                        :key="n"
                        :class="n <= Math.floor(movie.calificacion / 2)
                          ? 'mdi mdi-star star-filled'
                          : 'mdi mdi-star-outline star-empty'"
                      ></i>
                    </div>

                    <span class="meta-dot">•</span>
                    <span class="meta-item">Cinema 4K & VIP</span>
                  </div>

                  <!-- CTA Actions -->
                  <div class="hero-actions-row animate-fade-up" style="animation-delay: 300ms">
                    <button 
                      class="btn-hero-primary"
                      @click="movieStore.navigateToMovieDetails(movieStore.featuredMovies[currentSlide], router)"
                    >
                      <i class="mdi mdi-ticket-confirmation"></i>
                      <span>Comprar entrades</span>
                    </button>
                    <button 
                      class="btn-hero-secondary"
                      @click="movieStore.navigateToMovieDetails(movieStore.featuredMovies[currentSlide], router)"
                    >
                      <i class="mdi mdi-information-outline"></i>
                      <span>Veure Detalls</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Left / Right Carousel Nav Arrows -->
            <button 
              type="button" 
              class="carousel-nav-arrow arrow-left" 
              @click="prevSlide" 
              aria-label="Anterior"
            >
              <i class="mdi mdi-chevron-left"></i>
            </button>
            <button 
              type="button" 
              class="carousel-nav-arrow arrow-right" 
              @click="nextSlide" 
              aria-label="Següent"
            >
              <i class="mdi mdi-chevron-right"></i>
            </button>

            <!-- Carousel Pagination Indicators -->
            <div class="carousel-indicators">
              <button 
                v-for="(_, index) in movieStore.featuredMovies" 
                :key="index" 
                @click="currentSlide = index"
                class="carousel-dot"
                :class="{ active: currentSlide === index }"
                :aria-label="`Slide ${index + 1}`"
              ></button>
            </div>
          </div>
        </section>

        <!-- WHY FILMGALAXY PERKS BAR -->
        <section class="perks-banner-section">
          <div class="section-container">
            <div class="perks-banner-grid">
              <div class="perk-box">
                <div class="perk-box-icon gold">
                  <i class="mdi mdi-ticket-percent-outline"></i>
                </div>
                <div class="perk-box-text">
                  <strong>Dia de l'Espectador</strong>
                  <span>Preu reduït de 4€ tots els dimecres</span>
                </div>
              </div>

              <div class="perk-box">
                <div class="perk-box-icon purple">
                  <i class="mdi mdi-seat-recline-extra"></i>
                </div>
                <div class="perk-box-text">
                  <strong>Butaques VIP Reclinables</strong>
                  <span>Màxim confort a totes les nostres sales</span>
                </div>
              </div>

              <div class="perk-box">
                <div class="perk-box-icon blue">
                  <i class="mdi mdi-surround-sound"></i>
                </div>
                <div class="perk-box-text">
                  <strong>So Dolby Atmos & 4K</strong>
                  <span>Una experiència acústica i visual pura</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- SESSIONS DE PEL·LÍCULA (NOW PLAYING) -->
        <section class="sessions-section">
          <div class="section-container">
            <!-- Header -->
            <div class="section-header">
              <div class="header-left">
                <div class="section-tag">
                  <i class="mdi mdi-clock-outline"></i>
                  <span>HORARIS DISPONIBLES</span>
                </div>
                <h2 class="section-heading">
                  Sessions de pel·lícula
                </h2>
              </div>
              <button class="btn-view-all" @click="router.push('/movieDetails/moviSession')">
                <span>Veure totes les sessions</span>
                <i class="mdi mdi-arrow-right"></i>
              </button>
            </div>

            <!-- Sessions Grid -->
            <div class="movies-cards-grid">
              <div 
                v-for="session in combinedSessions" 
                :key="session.id"
                class="movie-card group"
              >
                <!-- Poster Thumbnail -->
                <div class="card-poster-wrapper">
                  <img 
                    :src="session.movie.imagen" 
                    :alt="session.movie.titulo" 
                    class="card-poster-img" 
                  />

                  <!-- Time Badge -->
                  <div class="card-time-badge">
                    <i class="mdi mdi-clock-time-four-outline"></i>
                    <span>{{ session.hora }}</span>
                  </div>

                  <!-- Ending Soon Badge -->
                  <div 
                    v-if="isSessionEndingSoon(session)"
                    class="card-ending-badge animate-pulse"
                  >
                    Acaba aviat
                  </div>

                  <!-- Hover Backdrop Overlay -->
                  <div class="card-hover-overlay">
                    <p class="hover-info-line">
                      {{ formatDuration(session.movie.duracion) }} • {{ session.movie.genero }}
                    </p>
                    <p class="hover-info-sub">
                      {{ formatDateHeader(session.fecha) }}
                    </p>
                  </div>
                </div>

                <!-- Info and Actions -->
                <div class="card-body">
                  <h3 class="card-movie-title line-clamp-2">
                    {{ session.movie.titulo }}
                  </h3>
                  <p class="card-date-line">
                    <i class="mdi mdi-calendar-blank-outline"></i>
                    <span>{{ formatDateHeader(session.fecha) }} • {{ session.hora }}</span>
                  </p>

                  <div class="card-buttons-row">
                    <button 
                      @click="router.push(`/billets/${session.id}`)"
                      class="btn-card-buy"
                    >
                      <i class="mdi mdi-ticket"></i>
                      <span>Comprar</span>
                    </button>
                    <button 
                      class="btn-card-details"
                      @click="movieStore.navigateToMovieDetails(session.movie, router)"
                    >
                      <span>Detalls</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- PRÒXIMAMENT (COMING SOON) -->
        <section class="coming-soon-section">
          <div class="section-container">
            <!-- Header -->
            <div class="section-header">
              <div class="header-left">
                <div class="section-tag">
                  <i class="mdi mdi-filmstrip"></i>
                  <span>PROPERS LLANÇAMENTS</span>
                </div>
                <h2 class="section-heading">
                  Pròximament al cinema
                </h2>
              </div>
              <button class="btn-view-all" @click="router.push('/movies')">
                <span>Veure cartellera completa</span>
                <i class="mdi mdi-arrow-right"></i>
              </button>
            </div>

            <!-- Coming Soon Grid -->
            <div class="movies-cards-grid">
              <div 
                v-for="movie in movieStore.comingSoonMovies" 
                :key="movie.id"
                class="movie-card group"
              >
                <!-- Poster Thumbnail -->
                <div class="card-poster-wrapper">
                  <img 
                    :src="movie.imagen" 
                    :alt="movie.titulo" 
                    class="card-poster-img" 
                  />

                  <!-- Release Date Badge -->
                  <div class="card-release-badge">
                    <i class="mdi mdi-calendar-star"></i>
                    <span>Estrena: {{ formatReleaseDate(movie.fecha_estreno) }}</span>
                  </div>

                  <!-- Hover Backdrop Overlay -->
                  <div class="card-hover-overlay">
                    <p class="hover-info-line">{{ movie.genero }}</p>
                  </div>
                </div>

                <!-- Info and Actions -->
                <div class="card-body">
                  <h3 class="card-movie-title line-clamp-2">
                    {{ movie.titulo }}
                  </h3>
                  <button
                    class="btn-card-details-full"
                    @click="movieStore.navigateToMovieDetails(movie, router)"
                  >
                    <i class="mdi mdi-information-outline"></i>
                    <span>Més informació</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Redesigned Modern Cinema Footer -->
        <Footer />
      </main>
    </div>
  </v-app>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMovieStore } from '~/stores/movieStore';
import { useSessionsStore } from '~/stores/sessionsStore';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

const router = useRouter();
const movieStore = useMovieStore();
const sessionsStore = useSessionsStore();
const currentSlide = ref(0);

// Auto advance carousel
let carouselInterval;
let checkSessionsInterval;

const nextSlide = () => {
  if (movieStore.featuredMovies.length > 0) {
    currentSlide.value = (currentSlide.value + 1) % movieStore.featuredMovies.length;
  }
};

const prevSlide = () => {
  if (movieStore.featuredMovies.length > 0) {
    currentSlide.value = (currentSlide.value - 1 + movieStore.featuredMovies.length) % movieStore.featuredMovies.length;
  }
};

function startCarousel() {
  if (carouselInterval) clearInterval(carouselInterval);
  carouselInterval = setInterval(nextSlide, 5000);
}

// Sessions imminents per als propers 4 dies
const combinedSessions = computed(() => {
  const now = new Date();
  const today = new Date(now);
  today.setHours(0, 0, 0, 0);

  let maxDate = new Date(today);
  maxDate.setDate(maxDate.getDate() + 4);

  let sessions = sessionsStore.availableSessions.filter(session => {
    const sessionDate = new Date(session.fecha);
    const sessionTime = session.hora.split(':');
    const sessionDateTime = new Date(sessionDate);
    sessionDateTime.setHours(
      parseInt(sessionTime[0]),
      parseInt(sessionTime[1]),
      0, 0
    );

    const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
    const sessionEndTime = new Date(sessionDateTime);
    sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

    return sessionEndTime > now && sessionDate <= maxDate;
  });

  if (sessions.length < 4) {
    let extendedDays = 5;
    while (sessions.length < 4 && extendedDays <= 30) {
      maxDate = new Date(today);
      maxDate.setDate(maxDate.getDate() + extendedDays);

      sessions = sessionsStore.availableSessions.filter(session => {
        const sessionDate = new Date(session.fecha);
        const sessionTime = session.hora.split(':');
        const sessionDateTime = new Date(sessionDate);
        sessionDateTime.setHours(
          parseInt(sessionTime[0]),
          parseInt(sessionTime[1]),
          0, 0
        );

        const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
        const sessionEndTime = new Date(sessionDateTime);
        sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

        return sessionEndTime > now && sessionDate <= maxDate;
      });

      extendedDays++;
    }
  }

  sessions.sort((a, b) => {
    const dateA = new Date(a.fecha);
    const dateB = new Date(b.fecha);
    if (dateA.getTime() !== dateB.getTime()) {
      return dateA - dateB;
    }
    return a.hora.localeCompare(b.hora);
  });

  return sessions.slice(0, 8);
});

const checkExpiredSessions = () => {
  const now = new Date();
  const expiredSessionsCount = sessionsStore.sessions.filter(session => {
    const sessionDate = new Date(session.fecha);
    const sessionTime = session.hora.split(':');
    const sessionDateTime = new Date(sessionDate);
    sessionDateTime.setHours(
      parseInt(sessionTime[0]),
      parseInt(sessionTime[1]),
      0, 0
    );

    const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
    const sessionEndTime = new Date(sessionDateTime);
    sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

    return sessionEndTime <= now;
  }).length;

  if (expiredSessionsCount > sessionsStore.sessions.length / 2 || combinedSessions.value.length === 0) {
    sessionsStore.fetchSessions();
  }
};

onMounted(() => {
  movieStore.fetchMovies();
  sessionsStore.fetchSessions();
  startCarousel();

  checkSessionsInterval = setInterval(checkExpiredSessions, 300000);
  checkExpiredSessions();
});

onUnmounted(() => {
  if (carouselInterval) clearInterval(carouselInterval);
  if (checkSessionsInterval) clearInterval(checkSessionsInterval);
});

const formatReleaseDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('ca-ES', {
    day: 'numeric', month: 'short'
  });
};

const formatDateHeader = (dateString) => {
  const date = new Date(dateString);
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);

  const dayAfterTomorrow = new Date(today);
  dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);

  const dateOnly = new Date(date);
  dateOnly.setHours(0, 0, 0, 0);

  if (dateOnly.getTime() === today.getTime()) {
    return 'Avui - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'short', day: 'numeric', month: 'short'
    });
  } else if (dateOnly.getTime() === tomorrow.getTime()) {
    return 'Demà - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'short', day: 'numeric', month: 'short'
    });
  } else if (dateOnly.getTime() === dayAfterTomorrow.getTime()) {
    return 'Passat demà - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'short', day: 'numeric', month: 'short'
    });
  } else {
    return date.toLocaleDateString('ca-ES', {
      weekday: 'short', day: 'numeric', month: 'short'
    });
  }
};

const formatDuration = (minutes) => {
  if (!minutes) return '2h 00min';
  let mins = typeof minutes === 'string' ? parseInt(minutes) : minutes;
  if (isNaN(mins)) return '2h 00min';
  const hours = Math.floor(mins / 60);
  const remainingMins = mins % 60;
  return `${hours}h ${remainingMins.toString().padStart(2, '0')}min`;
};

const isSessionEndingSoon = (session) => {
  const now = new Date();
  const sessionDate = new Date(session.fecha);
  const sessionTime = session.hora.split(':');
  const sessionDateTime = new Date(sessionDate);
  sessionDateTime.setHours(
    parseInt(sessionTime[0]),
    parseInt(sessionTime[1]),
    0, 0
  );

  const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
  const sessionEndTime = new Date(sessionDateTime);
  sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

  const timeDiff = (sessionEndTime - now) / 1000 / 60;
  return timeDiff < 30;
};
</script>

<style scoped>
/* Page Root */
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
}

/* Hero Carousel */
.hero-main {
  position: relative;
  /* Negative margin so hero starts under the transparent navbar */
  margin-top: -76px;
}

.hero-carousel-section {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.carousel-viewport {
  position: relative;
  width: 100%;
  height: 560px;
}

@media (min-width: 768px) {
  .carousel-viewport {
    height: 640px;
  }
}

.carousel-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.carousel-slide.slide-active {
  opacity: 1;
  pointer-events: auto;
}

.carousel-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
}

/* Vignette Gradients */
.vignette-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.top-vignette {
  background: linear-gradient(180deg, rgba(6, 7, 14, 0.7) 0%, transparent 40%);
}

.side-vignette {
  background: linear-gradient(90deg, rgba(6, 7, 14, 0.9) 0%, rgba(6, 7, 14, 0.4) 45%, transparent 100%);
}

.bottom-vignette {
  background: linear-gradient(0deg, #0c0f1c 0%, rgba(12, 15, 28, 0.8) 25%, transparent 70%);
}

/* Hero Content */
.hero-content-wrapper {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: flex-end;
  max-width: 1350px;
  margin: 0 auto;
  padding: 0 2rem 5rem 2rem;
  z-index: 5;
}

.hero-content {
  max-width: 650px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.35rem 0.85rem;
  background: rgba(200, 169, 110, 0.18);
  border: 1px solid rgba(200, 169, 110, 0.4);
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: #F5D78E;
  margin-bottom: 1rem;
  backdrop-filter: blur(8px);
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 900;
  line-height: 1.15;
  color: #F2E9E4;
  margin-bottom: 0.85rem;
  text-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
}

@media (min-width: 768px) {
  .hero-title {
    font-size: 3.5rem;
  }
}

.hero-meta-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.rating-badge {
  display: inline-flex;
  align-items: baseline;
  gap: 2px;
  padding: 0.25rem 0.65rem;
  background: rgba(200, 169, 110, 0.2);
  border: 1px solid rgba(200, 169, 110, 0.35);
  border-radius: 8px;
}

.rating-num {
  font-size: 1.1rem;
  font-weight: 800;
  color: #F5D78E;
}

.star-rating {
  display: flex;
  gap: 2px;
}

.star-filled {
  color: #F5D78E;
  font-size: 1.15rem;
}

.star-empty {
  color: rgba(200, 169, 110, 0.3);
  font-size: 1.15rem;
}

.meta-dot {
  color: #6c7293;
}

.meta-item {
  color: #C9ADA7;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Hero CTA Buttons */
.hero-actions-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-hero-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.85rem 1.6rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
  color: #121526;
  font-weight: 800;
  font-size: 0.95rem;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(200, 169, 110, 0.35);
  transition: all 0.25s ease;
}

.btn-hero-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(200, 169, 110, 0.5);
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 50%, #C8A96E 100%);
}

.btn-hero-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.85rem 1.4rem;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #F2E9E4;
  font-weight: 600;
  font-size: 0.95rem;
  border-radius: 14px;
  cursor: pointer;
  backdrop-filter: blur(8px);
  transition: all 0.25s ease;
}

.btn-hero-secondary:hover {
  background: rgba(255, 255, 255, 0.16);
  border-color: #F5D78E;
  color: #F5D78E;
  transform: translateY(-2px);
}

/* Carousel Navigation Arrows */
.carousel-nav-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(12, 15, 28, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #F2E9E4;
  font-size: 1.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  backdrop-filter: blur(8px);
  transition: all 0.25s ease;
}

.carousel-nav-arrow:hover {
  background: rgba(200, 169, 110, 0.3);
  border-color: #C8A96E;
  color: #F5D78E;
}

.arrow-left { left: 1.5rem; }
.arrow-right { right: 1.5rem; }

/* Carousel Dots */
.carousel-indicators {
  position: absolute;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 10;
}

.carousel-dot {
  width: 8px;
  height: 8px;
  border-radius: 9999px;
  background: rgba(255, 255, 255, 0.35);
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.carousel-dot.active {
  width: 28px;
  background: #C8A96E;
  box-shadow: 0 0 8px #C8A96E;
}

/* PERKS BANNER SECTION */
.perks-banner-section {
  position: relative;
  z-index: 10;
  margin-top: -2rem;
  margin-bottom: 2rem;
}

.section-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.perks-banner-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
}

.perk-box {
  background: rgba(22, 26, 46, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.15rem;
  backdrop-filter: blur(16px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  transition: all 0.25s ease;
}

.perk-box:hover {
  background: rgba(22, 26, 46, 0.95);
  border-color: rgba(200, 169, 110, 0.35);
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
}

.perk-box-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.perk-box-icon.gold {
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.3);
  color: #C8A96E;
}

.perk-box-icon.purple {
  background: rgba(147, 51, 234, 0.15);
  border: 1px solid rgba(147, 51, 234, 0.3);
  color: #c084fc;
}

.perk-box-icon.blue {
  background: rgba(59, 130, 246, 0.15);
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: #60a5fa;
}

.perk-box-text {
  display: flex;
  flex-direction: column;
}

.perk-box-text strong {
  font-size: 0.95rem;
  font-weight: 700;
  color: #F2E9E4;
}

.perk-box-text span {
  font-size: 0.8rem;
  color: #9A8C98;
}

/* SESSIONS & COMING SOON SECTIONS */
.sessions-section,
.coming-soon-section {
  padding: 3rem 0;
}

.section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.section-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #C8A96E;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 0.4rem;
}

.section-heading {
  font-size: 1.85rem;
  font-weight: 800;
  color: #F2E9E4;
  letter-spacing: -0.3px;
}

.btn-view-all {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #F5D78E;
  font-size: 0.88rem;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-view-all:hover {
  background: rgba(200, 169, 110, 0.15);
  border-color: #C8A96E;
  transform: translateX(2px);
}

/* CARDS GRID */
.movies-cards-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
}

@media (min-width: 768px) {
  .movies-cards-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1024px) {
  .movies-cards-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
  }
}

.movie-card {
  background: rgba(22, 26, 46, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(12px);
  display: flex;
  flex-direction: column;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.movie-card:hover {
  transform: translateY(-5px);
  border-color: rgba(200, 169, 110, 0.4);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 25px rgba(200, 169, 110, 0.1);
}

.card-poster-wrapper {
  position: relative;
  aspect-ratio: 2/3;
  overflow: hidden;
}

.card-poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.movie-card:hover .card-poster-img {
  transform: scale(1.06);
}

.card-time-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.8rem;
  font-weight: 800;
  border-radius: 9999px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
  z-index: 2;
}

.card-ending-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: #f43f5e;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.25rem 0.6rem;
  border-radius: 8px;
  z-index: 2;
  box-shadow: 0 2px 8px rgba(244, 63, 94, 0.5);
}

.card-release-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.65rem;
  background: rgba(18, 21, 38, 0.85);
  border: 1px solid rgba(200, 169, 110, 0.4);
  color: #F5D78E;
  font-size: 0.775rem;
  font-weight: 700;
  border-radius: 9999px;
  backdrop-filter: blur(8px);
  z-index: 2;
}

.card-hover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(12, 15, 28, 0.95) 0%, rgba(12, 15, 28, 0.4) 50%, transparent 100%);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.25rem;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.movie-card:hover .card-hover-overlay {
  opacity: 1;
}

.hover-info-line {
  color: #F5D78E;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
}

.hover-info-sub {
  color: #C9ADA7;
  font-size: 0.78rem;
}

/* Card Body */
.card-body {
  padding: 1.15rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-movie-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.4rem;
  line-height: 1.35;
}

.card-date-line {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  color: #9A8C98;
  font-size: 0.825rem;
  margin-bottom: 1rem;
}

.card-buttons-row {
  display: flex;
  gap: 0.6rem;
  margin-top: auto;
}

.btn-card-buy {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  padding: 0.55rem 0.85rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.85rem;
  font-weight: 700;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-card-buy:hover {
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 100%);
  transform: translateY(-1px);
}

.btn-card-details {
  flex: 1;
  padding: 0.55rem 0.85rem;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #F2E9E4;
  font-size: 0.85rem;
  font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-card-details:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: #F5D78E;
  color: #F5D78E;
}

.btn-card-details-full {
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.65rem 1rem;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #F2E9E4;
  font-size: 0.85rem;
  font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  margin-top: auto;
  transition: all 0.2s ease;
}

.btn-card-details-full:hover {
  background: rgba(200, 169, 110, 0.15);
  border-color: #C8A96E;
  color: #F5D78E;
}

/* Animations */
@keyframes fade-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-up {
  animation: fade-up 0.6s ease-out forwards;
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .perks-banner-grid {
    grid-template-columns: 1fr;
  }

  .hero-content-wrapper {
    padding-bottom: 4rem;
  }
}
</style>
