<template>
  <v-app>
    <!-- NavBar -->
    <Navbar />
    <!-- Página Principal con scroll -->
    <v-main class="pa-0 ma-0 backgroundIndex">
      <v-container fluid class="pa-0 ma-0">
        <!-- Alerta de error si hay problemas -->
        <v-alert v-if="movieStore.error" type="error" closable title="Error al cargar películas"
          class="ma-2 text-caption">
          {{ movieStore.error }}
        </v-alert>

        <!-- Indicador de carga -->
        <v-overlay v-if="movieStore.loading" :value="movieStore.loading" class="align-center justify-center">
          <v-progress-circular indeterminate color="gold" size="48"></v-progress-circular>
        </v-overlay>

        <!-- Carousel de películas destacadas -->
        <v-row no-gutters>
          <v-col cols="12">
            <v-carousel v-if="movieStore.featuredMovies.length > 0" cycle :height="isMobile ? '420' : '600'"
              hide-delimiter-background show-arrows="hover" class="featured-movies">
              <v-carousel-item v-for="movie in movieStore.featuredMovies" :key="movie.id" :src="movie.poster" cover>
                <div class="featured-overlay">
                  <h2 class="featured-title">{{ movie.titulo }}</h2>
                  <div class="featured-rating">
                    <span class="rating-number">{{ movie.calificacion }}/10</span>
                    <div class="star-rating">
                      <v-icon v-for="n in Math.floor(movie.calificacion / 2)" :key="n" color="gold"
                        size="24">mdi-star</v-icon>
                      <v-icon v-if="movie.calificacion % 2 >= 1" color="gold" size="24">mdi-star-half</v-icon>
                      <v-icon v-for="n in (5 - Math.ceil(movie.calificacion / 2))" :key="`empty-${n}`" color="gold"
                        size="24">mdi-star-outline</v-icon>
                    </div>
                  </div>
                  <div class="featured-buttons">
                    <v-btn color="gold" :size="isMobile ? 'small' : 'x-large'"
                      class="text-dark font-weight-bold px-4 me-2" elevation="4">
                      Comprar Entradas
                    </v-btn>
                    <v-btn color="grey-lighten-1" :size="isMobile ? 'small' : 'x-large'" class="font-weight-bold px-4"
                      elevation="4">
                      Ver Info
                    </v-btn>
                  </div>
                </div>
              </v-carousel-item>
            </v-carousel>
          </v-col>
        </v-row>

        <!-- Sección de Películas en cartelera -->
        <v-row class="py-6 px-2">
          <v-col cols="12">
            <div class="d-flex flex-column flex-sm-row justify-space-between align-center mb-4">
              <h2 class="text-h4 text-eae0d5 font-weight-bold movies-section-title">
                Películas de Hoy
              </h2>
              <v-btn color="gold" :size="isMobile ? 'medium' : 'large'"
                class="text-dark font-weight-bold mt-2 mt-sm-0" to="/movies" elevation="4">
                Ver Más Películas
              </v-btn>
            </div>


            <v-row>
              <v-col v-for="movie in movieStore.nowPlayingMovies" :key="movie.id" cols="6" sm="4" md="3">
                <v-card class="mx-auto movie-card" color="white" elevation="8">
                  <v-img :src="movie.imagen" :height="isMobile ? '200' : '400'" cover class="movie-poster">
                    <template v-slot:placeholder>
                      <v-row class="fill-height ma-0" align="center" justify="center">
                        <v-progress-circular indeterminate color="gold"
                          :size="isMobile ? 24 : 36"></v-progress-circular>
                      </v-row>
                    </template>
                  </v-img>
                  <v-card-title class="text-dark pt-2 movie-title">{{ movie.titulo }}</v-card-title>
                  <v-card-text class="text-accent pb-0 movie-details">
                    {{ formatDuration(movie.duracion) }} • {{ movie.genero }}
                  </v-card-text>
                  <v-card-actions class="pa-2">
                    <v-btn color="gold" variant="elevated" :size="isMobile ? 'small' : 'default'"
                      class="text-dark font-weight-bold me-1">
                      Comprar
                    </v-btn>
                    <v-btn color="grey-lighten-1" variant="elevated" :size="isMobile ? 'small' : 'default'"
                      class="font-weight-bold">
                      Info
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <!-- Sección de Próximos estrenos -->
        <v-row class="py-6 px-2">
          <v-col cols="12">
            <h2 class="movies-section-title text-h3 text-eae0d5 text-center font-weight-bold mb-10">Próximamente</h2>

            <v-row>
              <v-col v-for="movie in movieStore.comingSoonMovies" :key="movie.id" cols="6" sm="4" md="3">
                <v-card class="mx-auto movie-card" color="white" elevation="8">
                  <v-img :src="movie.imagen" :height="isMobile ? '200' : '400'" cover class="movie-poster">
                    <div class="release-date-badge">
                      {{ formatReleaseDate(movie.fecha_estreno) }}
                    </div>
                  </v-img>
                  <v-card-title class="text-dark pt-2 movie-title">{{ movie.titulo }}</v-card-title>
                  <v-card-text class="text-accent movie-details">
                    {{ movie.genero }}
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <Footer />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { onMounted, ref, computed, onBeforeMount } from 'vue';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';
import { useMovieStore } from '../stores/movieStore';
import { useDisplay } from 'vuetify';

// Declarar la variable para el entorno de desarrollo
const isDev = import.meta.env.DEV;

// Inicializar el store de películas
const movieStore = useMovieStore();

// Detectar tamaño de pantalla para responsive
const { mobile, xs, sm } = useDisplay();
const isMobile = computed(() => mobile.value || xs.value);

// Función para formatear la fecha de estreno
const formatReleaseDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Función para formatear la duración
const formatDuration = (minutes) => {
  if (!minutes) return '2h 00min';

  let mins = minutes;
  if (typeof minutes === 'string') {
    mins = parseInt(minutes);
  }

  if (isNaN(mins)) return '2h 00min';

  const hours = Math.floor(mins / 60);
  const remainingMins = mins % 60;
  return `${hours}h ${remainingMins}min`;
};

// Cargar películas al montar el componente
onMounted(() => {
  movieStore.fetchMovies();
});
</script>

<style>
/* Background Colors */
.white-bg {
  background-color: white;
}

.backgroundIndex {
  background: linear-gradient(135deg, #22223B 20%, #EAE0D5 100%);
}

.text-eae0d5 {
  color: #EAE0D5 !important;
}

.rojo-bg {
  background-color: #f8d7da;
}

/* Featured Movies Carousel */
.featured-movies {
  margin-top: -64px;
  /* Ajusta según la altura de tu navbar */
}

.featured-movies .featured-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 6rem;
  background: linear-gradient(transparent, rgba(28, 28, 28, 0.95));
  color: #EAE0D5;
}

.featured-title {
  font-size: 55px !important;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Estilos para la calificación y estrellas */
.featured-rating {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
}

.rating-number {
  font-size: 2rem;
  font-weight: bold;
  margin-right: 1rem;
  color: #FFD700;
}

.star-rating {
  display: flex;
  align-items: center;
}

.featured-buttons {
  display: flex;
  gap: 1rem;
}

/* Quitar márgenes y padding entre Navbar y contenido */
.v-application {
  margin: 0 !important;
  padding: 0 !important;
}

.v-main {
  padding: 0 !important;
  margin: 0 !important;
}

/* Estilos de las tarjetas de películas */
.movie-card {
  transition: all 0.3s ease;
  overflow: hidden;
  border-radius: 12px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3) !important;
}

.movie-card .movie-poster {
  transition: transform 0.3s ease;
}

.movie-card:hover .movie-poster {
  transform: scale(1.05);
}

/* Badge para la fecha de estreno */
.movie-card .release-date-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background-color: var(--v-gold-base);
  color: var(--v-dark-base);
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Botón de recarga */
.reload-btn {
  position: fixed;
  top: 80px;
  right: 20px;
  z-index: 999;
}

/* ===== Personalización del Scrollbar (global) ===== */
/* Para navegadores basados en WebKit (Chrome, Safari, etc.) */
::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  background: #EAE0D5;
  margin: 5px;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #22223B;
  border-radius: 10px;
  border: 2px solid #EAE0D5;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Para Firefox */
html {
  scrollbar-width: thin;
  scrollbar-color: #22223B #EAE0D5;
}

/* ===== Responsive ajustes para Móvil y Desktop ===== */

/* Móvil (hasta 600px) */
@media (max-width: 600px) {
  .v-main {
    margin: 10px;
  }

  .featured-title {
    font-size: 1.5rem !important;
    margin-bottom: 0.5rem !important;
  }

  .featured-movies .featured-overlay {
    padding: 1.5rem;
  }

  .featured-rating {
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 1rem;
  }

  .rating-number {
    font-size: 1.2rem;
    margin-bottom: 0.2rem;
  }

  .star-rating {
    margin-bottom: 0.5rem;
  }

  .movies-section-title {
    font-size: 1.5rem !important;
    margin-bottom: 0.5rem !important;
  }

  .movie-title {
    font-size: 0.875rem !important;
    line-height: 1.2;
    padding: 8px;
  }

  .movie-details {
    font-size: 0.75rem !important;
    padding: 4px 8px !important;
  }

  .release-date-badge {
    font-size: 0.7rem !important;
    padding: 4px 8px !important;
    top: 8px !important;
    right: 8px !important;
  }

  .v-card-actions {
    padding: 4px !important;
  }
}

/* Desktop (desde 601px en adelante) */
@media (min-width: 601px) {
  .v-main {
    margin: 20px;
  }

}
</style>