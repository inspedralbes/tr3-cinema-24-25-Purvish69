<template>
  <v-app class="cinema-app-root bg-[#0c0e1a]">
    <Navbar />
    
    <div class="min-h-screen bg-gradient-to-b from-[#0c0e1a] via-[#111528] to-[#0c0e1a] pt-4 pb-20 text-[#F2E9E4] relative overflow-hidden">
      <!-- Ambient cinema glow effects -->
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-gold/10 rounded-full blur-[140px] pointer-events-none"></div>
      <div class="absolute top-1/2 -right-40 w-96 h-96 bg-purple-600/10 rounded-full blur-[140px] pointer-events-none"></div>

      <div class="container mx-auto px-4 max-w-6xl relative z-10">
        <!-- Back Link Navigation -->
        <div class="mb-6">
          <NuxtLink 
            to="/movies" 
            class="inline-flex items-center gap-2 text-sm text-gold/80 hover:text-gold transition-colors font-medium px-3 py-1.5 rounded-lg hover:bg-gold/10"
          >
            <i class="mdi mdi-arrow-left text-lg"></i>
            <span>Tornar a la Cartellera</span>
          </NuxtLink>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex flex-col justify-center items-center h-[65vh]">
          <div class="w-14 h-14 border-4 border-gold/20 border-t-gold rounded-full animate-spin mb-4"></div>
          <p class="text-gold/90 font-medium tracking-wide text-sm">Carregant detalls de la pel·lícula...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="max-w-md mx-auto bg-[#161a2e]/90 border border-red-500/30 text-[#F2E9E4] p-8 rounded-2xl text-center shadow-2xl backdrop-blur-md">
          <i class="mdi mdi-alert-circle-outline text-4xl text-red-400 mb-3 inline-block"></i>
          <p class="text-base mb-4">{{ error }}</p>
          <NuxtLink to="/movies" class="btn-primary inline-block">
            Veure altres pel·lícules
          </NuxtLink>
        </div>

        <!-- Movie Details Card -->
        <div v-else class="space-y-10">
          <div class="relative bg-[#161a2e]/90 border border-gold/20 backdrop-blur-md rounded-2xl overflow-hidden shadow-2xl">
            <!-- Background Movie Poster with dark cinema veil -->
            <div 
              class="absolute inset-0 bg-cover bg-center opacity-10 filter blur-sm scale-105 pointer-events-none"
              :style="{ backgroundImage: `url(${movie.imagen || 'https://via.placeholder.com/800x600?text=No+Poster'})` }"
            ></div>

            <div class="relative flex flex-col md:flex-row gap-6 p-6 md:p-8">
              <!-- Movie Poster -->
              <div class="md:w-1/3 flex justify-center items-start">
                <div class="relative group rounded-xl overflow-hidden shadow-2xl border border-white/10 w-full max-w-[340px]">
                  <img 
                    :src="movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'" 
                    :alt="movie.titulo"
                    class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-105" 
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-[#0c0e1a]/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                    <span class="text-xs text-gold font-semibold uppercase tracking-wider">FilmGalaxy VIP</span>
                  </div>
                </div>
              </div>

              <!-- Movie Details Info -->
              <div class="md:w-2/3 flex flex-col justify-between">
                <div>
                  <!-- Rating & Badge -->
                  <div class="flex flex-wrap items-center gap-3 mb-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold/15 border border-gold/30 text-gold font-bold text-sm">
                      <i class="mdi mdi-star text-base"></i>
                      <span>{{ movie.calificacion || '8.5' }}/10</span>
                    </div>
                    <span v-if="movie.clasificacion" class="px-2.5 py-0.5 rounded-md bg-white/10 border border-white/10 text-xs font-medium text-slate-300">
                      {{ movie.clasificacion }}
                    </span>
                    <span v-if="movie.duracion" class="inline-flex items-center gap-1 text-xs text-slate-400">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ movie.duracion }} min
                    </span>
                  </div>

                  <!-- Title -->
                  <h1 class="text-3xl md:text-5xl font-extrabold text-[#F2E9E4] mb-4 tracking-tight">
                    {{ movie.titulo }}
                  </h1>

                  <!-- Metadata Grid -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6 bg-white/[0.03] border border-white/5 p-4 rounded-xl text-sm">
                    <p><span class="text-gold/90 font-semibold">Gènere:</span> <span class="text-slate-300">{{ movie.genero || 'N/A' }}</span></p>
                    <p><span class="text-gold/90 font-semibold">Director:</span> <span class="text-slate-300">{{ movie.director || 'N/A' }}</span></p>
                    <p><span class="text-gold/90 font-semibold">Idioma:</span> <span class="text-slate-300">{{ movie.lenguaje || 'N/A' }}</span></p>
                    <p><span class="text-gold/90 font-semibold">Actors:</span> <span class="text-slate-300">{{ movie.actores || 'N/A' }}</span></p>
                  </div>

                  <!-- Synopsis -->
                  <div class="mb-6">
                    <h3 class="text-base font-bold text-gold uppercase tracking-wider mb-2 flex items-center gap-2">
                      <i class="mdi mdi-text-box-outline"></i>
                      Sinopsi
                    </h3>
                    <p class="text-slate-300 leading-relaxed text-sm md:text-base">
                      {{ movie.descripcion || 'Sense descripció disponible en aquest moment.' }}
                    </p>
                  </div>
                </div>

                <!-- CTA Action Button -->
                <div class="pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
                  <div class="flex items-center gap-2 text-xs text-slate-400">
                    <i class="mdi mdi-shield-check-outline text-gold text-lg"></i>
                    <span>Butaques numerades i so Dolby Atmos</span>
                  </div>
                  <button 
                    @click="goToSessions(movie.id)"
                    class="px-6 py-3.5 bg-gradient-to-r from-[#D4AF37] to-[#C8A96E] hover:from-[#E5C158] hover:to-[#D4AF37] text-[#0c0e1a] font-bold rounded-xl shadow-lg shadow-gold/20 hover:shadow-gold/40 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer"
                  >
                    <i class="mdi mdi-calendar-clock text-xl"></i>
                    <span>Veure Sessions</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Trailer Section -->
          <div v-if="trailerEmbedUrl" class="bg-[#161a2e]/90 border border-gold/20 p-6 md:p-8 rounded-2xl shadow-2xl backdrop-blur-md">
            <h2 class="text-2xl font-bold mb-4 text-[#F2E9E4] flex items-center gap-2">
              <i class="mdi mdi-youtube text-red-500 text-3xl"></i>
              <span>Tràiler Oficial</span>
            </h2>
            <div class="relative pb-[56.25%] overflow-hidden rounded-xl border border-white/10 shadow-2xl bg-black">
              <iframe 
                :src="trailerEmbedUrl" 
                class="absolute top-0 left-0 w-full h-full" 
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </v-app>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useMovieStore } from '~/stores/movieStore';

const route = useRoute();
const router = useRouter();
const movieStore = useMovieStore();

const movie = ref({});
const loading = ref(true);
const error = ref(null);

const trailerEmbedUrl = computed(() => {
  if (!movie.value.trailer) return null;
  const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
  const match = movie.value.trailer.match(youtubeRegex);
  if (match && match[1]) {
    return `https://www.youtube.com/embed/${match[1]}`;
  }
  return movie.value.trailer;
});

const fetchMovieDetails = async () => {
  try {
    const id = route.params.id;
    if (movieStore.allMovies.length === 0) {
      await movieStore.fetchMovies();
    }
    movie.value = movieStore.allMovies.find(m => m.id == id) || {};
    if (!movie.value || Object.keys(movie.value).length === 0) {
      error.value = 'La pel·lícula no s\'ha trobat';
    }
  } catch (err) {
    error.value = 'Error en carregar els detalls de la pel·lícula';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchMovieDetails();
});

const goToSessions = (id) => {
  router.push({ path: '/movieDetails/moviSession', query: { movieId: id } });
};
</script>
