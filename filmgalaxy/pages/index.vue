<template>
  <v-app>
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-cream custom-bg">
      <!-- Navbar -->
      <Navbar />

      <main class="relative pt-16">
        <!-- Error Alert -->
        <div v-if="movieStore.error"
          class="fixed top-4 right-4 z-50 max-w-md bg-red-500/90 text-light py-36 rounded-lg backdrop-blur-sm animate-fade-in">
          <p class="font-medium">{{ movieStore.error }}</p>
        </div>

        <!-- Loading Overlay -->
        <div v-if="movieStore.loading"
          class="fixed inset-0 bg-primary/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Featured Movies Carousel -->
        <section v-if="movieStore.featuredMovies.length > 0" class="relative">
          <div class="relative h-[420px] md:h-[600px]">
            <div v-for="(movie, index) in movieStore.featuredMovies" :key="movie.id"
              class="absolute inset-0 transition-opacity duration-700" :class="{ 'opacity-0': currentSlide !== index }">
              <div class="relative h-full">
                <img :src="movie.poster" :alt="movie.titulo" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/50 to-transparent">
                  <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12 space-y-4">
                    <h2 class="text-3xl md:text-5xl font-bold text-light animate-fade-up">
                      {{ movie.titulo }}
                    </h2>

                    <div class="flex items-center space-x-4 animate-fade-up" style="animation-delay: 200ms">
                      <span class="text-gold text-xl md:text-2xl font-bold">
                        {{ movie.calificacion }}/10
                      </span>
                      <div class="flex">
                        <template v-for="n in 5" :key="n">
                          <svg v-if="n <= Math.floor(movie.calificacion / 2)" class="w-5 h-5 text-gold"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                          <svg v-else class="w-5 h-5 text-gold/40" fill="currentColor" viewBox="0 0 20 20">
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        </template>
                      </div>
                    </div>

                    <div class="flex space-x-4 animate-fade-up" style="animation-delay: 400ms">
                      <button class="btn-primary">
                        Comprar Entradas
                      </button>
                      <button class="btn-secondary"
                        @click="movieStore.navigateToMovieDetails(movieStore.featuredMovies[currentSlide], router)">
                        Detalls
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Carousel Controls -->
          <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
            <button v-for="(_, index) in movieStore.featuredMovies" :key="index" @click="currentSlide = index"
              class="w-2 h-2 rounded-full transition-all duration-300"
              :class="currentSlide === index ? 'bg-gold w-8' : 'bg-light/50 hover:bg-light'"></button>
          </div>
        </section>

        <!-- Now Playing Movies -->
        <section class="container mx-auto px-4 py-12">
          <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h2 class="text-2xl md:text-4xl font-bold text-light mb-4 md:mb-0">
              Películas de Hoy
            </h2>
            <button class="btn-primary" @click="$router.push('/movies')">
              Ver Más Películas
            </button>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <div v-for="movie in movieStore.nowPlayingMovies" :key="movie.id"
              class="group relative bg-light/10 rounded-xl overflow-hidden backdrop-blur-sm transition-transform duration-300 hover:scale-105">
              <!-- Movie Poster -->
              <div class="relative aspect-[2/3]">
                <img :src="movie.imagen" :alt="movie.titulo" class="w-full h-full object-cover" />

                <div
                  class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-light text-sm mb-2">
                      {{ formatDuration(movie.duracion) }} • {{ movie.genero }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Movie Info -->
              <div class="p-4 space-y-3">
                <h3 class="text-lg font-bold text-light line-clamp-2">
                  {{ movie.titulo }}
                </h3>

                <div class="flex gap-2">
                  <button
                    class="flex-1 bg-gold hover:bg-gold/80 text-primary font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300">
                    Comprar
                  </button>
                  <button
                    class="flex-1 bg-accent hover:bg-accent/80 text-light font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300"
                    @click="movieStore.navigateToMovieDetails(movie, router)">
                    Detalls
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Próximamente -->
        <section class="container mx-auto px-4 py-12">
          <h2 class="text-3xl md:text-4xl font-bold text-light text-center mb-8">
            Próximamente
          </h2>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <div v-for="movie in movieStore.comingSoonMovies" :key="movie.id"
              class="group relative bg-light/10 rounded-xl overflow-hidden backdrop-blur-sm transition-transform duration-300 hover:scale-105">
              <!-- Movie Poster -->
              <div class="relative aspect-[2/3]">
                <img :src="movie.imagen" :alt="movie.titulo" class="w-full h-full object-cover" />

                <div class="absolute top-4 right-4 bg-gold text-primary px-3 py-1 rounded-full text-sm font-medium">
                  {{ formatReleaseDate(movie.fecha_estreno) }}
                </div>

                <div
                  class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-light text-sm">{{ movie.genero }}</p>
                  </div>
                </div>
              </div>

              <div class="p-4">
                <h3 class="text-lg font-bold text-light line-clamp-2">
                  {{ movie.titulo }}
                </h3>
                <button
                  class="w-full mt-3 bg-accent hover:bg-accent/80 text-light font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300"
                  @click="movieStore.navigateToMovieDetails(movie, router)">
                  Detalls
                </button>
              </div>
            </div>
          </div>
        </section>

        <Footer />
      </main>
    </div>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMovieStore } from '~/stores/movieStore';

const router = useRouter();
const movieStore = useMovieStore();
const currentSlide = ref(0);

// Auto advance carousel
let carouselInterval;
onMounted(() => {
  movieStore.fetchMovies();
  startCarousel();
});

function startCarousel() {
  carouselInterval = setInterval(() => {
    if (movieStore.featuredMovies.length > 0) {
      currentSlide.value = (currentSlide.value + 1) % movieStore.featuredMovies.length;
    }
  }, 5000);
}

// Helpers para formatear fechas y duración
const formatReleaseDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

const formatDuration = (minutes) => {
  if (!minutes) return '2h 00min';
  let mins = typeof minutes === 'string' ? parseInt(minutes) : minutes;
  if (isNaN(mins)) return '2h 00min';
  const hours = Math.floor(mins / 60);
  const remainingMins = mins % 60;
  return `${hours}h ${remainingMins}min`;
};
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}

/* Animación para fade-up */
@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-up {
  animation: fade-up 0.6s ease-out forwards;
}

/* Animación para fade-in */
@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

/* Barra de desplazamiento personalizada */
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #1a202c;
}

::-webkit-scrollbar-thumb {
  background: #d4af37;
  border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(212, 175, 55, 0.8);
}

* {
  scrollbar-width: thin;
  scrollbar-color: #d4af37 #1a202c;
}

/* Truncado de texto en 2 líneas */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
