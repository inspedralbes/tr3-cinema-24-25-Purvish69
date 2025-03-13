<!-- pages/movieDetails/_id.vue -->
<template>
  <v-app>
    <div class="min-h-screen bg-gradient-to-r from-primary to-cream text-white">
      <!-- Navbar -->
      <Navbar />

      <div class="container mx-auto px-4 py-8">
        <!-- Loading state and error handling -->
        <div v-if="loading" class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-gold"></div>
        </div>

        <div v-else-if="error" class="bg-red-500 text-white p-4 rounded-lg mb-6">
          {{ error }}
        </div>

        <!-- Movie details content -->
        <div v-else-if="movie" class="mb-12">
          <!-- Movie header with poster and basic info -->
          <div class="flex flex-col md:flex-row gap-8 mb-8">
            <!-- Movie poster -->
            <div class="w-full md:w-1/3 lg:w-1/4">
              <img :src="movie.poster" :alt="movie.titulo" class="w-full h-auto rounded-lg shadow-lg">
              
              <!-- Movie rating badge -->
              <div class="mt-4 bg-primary rounded-full w-16 h-16 flex items-center justify-center border-2 border-gold shadow-lg mx-auto md:mx-0">
                <span class="text-gold font-bold text-xl">{{ movie.calificacion }}</span>
              </div>
            </div>

            <!-- Movie details -->
            <div class="w-full md:w-2/3 lg:w-3/4">
              <h1 class="text-4xl font-bold mb-4 text-light">{{ movie.titulo }}</h1>
              
              <!-- Movie metadata -->
              <div class="flex flex-wrap gap-4 mb-6">
                <span class="bg-secondary px-3 py-1 rounded-full text-sm">{{ movie.genero }}</span>
                <span class="bg-secondary px-3 py-1 rounded-full text-sm">{{ movie.duracion }} min</span>
                <span class="bg-secondary px-3 py-1 rounded-full text-sm">{{ movie.lenguaje }}</span>
                <span class="bg-secondary px-3 py-1 rounded-full text-sm">{{ formatDate(movie.fecha_estreno) }}</span>
              </div>
              
              <!-- Movie description -->
              <p class="text-light mb-6 leading-relaxed">{{ movie.descripcion }}</p>
              
              <!-- Director and cast -->
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-neutral mb-2">Director:</h3>
                <p class="text-light">{{ movie.director || 'No disponible' }}</p>
              </div>
              
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-neutral mb-2">Reparto:</h3>
                <p class="text-light">{{ movie.actores || 'No disponible' }}</p>
              </div>
              
              <!-- Buy tickets button -->
              <button 
                @click="goToBuy(movie.id)" 
                class="bg-gold hover:bg-amber-600 text-primary font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center gap-2"
              >
                <span>Comprar Entradas</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Trailer section -->
          <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-light border-b-2 border-gold pb-2">Trailer</h2>
            <div class="aspect-w-16 aspect-h-9 bg-dark rounded-lg overflow-hidden shadow-xl">
              <div v-if="movie.trailer" class="w-full h-0 pb-[56.25%] relative">
                <iframe 
                  :src="getEmbedUrl(movie.trailer)" 
                  class="absolute top-0 left-0 w-full h-full" 
                  frameborder="0" 
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                  allowfullscreen>
                </iframe>
              </div>
              <div v-else class="flex justify-center items-center h-64 bg-dark">
                <p class="text-neutral">No hay trailer disponible</p>
              </div>
            </div>
          </div>

          <!-- Horarios section -->
          <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-light border-b-2 border-gold pb-2">Horarios</h2>
            <div v-if="sesiones && sesiones.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div 
                v-for="sesion in sesiones" 
                :key="sesion.id" 
                class="bg-secondary rounded-lg p-4 shadow-lg hover:shadow-xl transition duration-300"
              >
                <div class="font-bold text-neutral mb-2">{{ formatDate(sesion.fecha) }}</div>
                <div class="text-light text-lg">{{ formatTime(sesion.hora) }}</div>
                <div class="text-accent text-sm mb-4">Sala {{ sesion.sala }}</div>
                <button 
                  @click="selectSesion(sesion.id)" 
                  class="bg-gold hover:bg-amber-600 text-primary font-bold py-2 px-4 rounded-lg transition duration-300 w-full"
                >
                  Seleccionar
                </button>
              </div>
            </div>
            <div v-else class="bg-secondary rounded-lg p-6 shadow-lg">
              <p class="text-light">No hay sesiones disponibles para esta película</p>
            </div>
          </div>

          <!-- Similar movies section -->
          <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-light border-b-2 border-gold pb-2">Películas similares</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
              <div 
                v-for="item in similarMovies" 
                :key="item.id" 
                class="bg-white text-black rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300"
              >
                <img :src="item.poster" :alt="item.titulo" class="w-full h-48 object-cover">
                <div class="p-4">
                  <h3 class="font-bold text-lg mb-2 truncate">{{ item.titulo }}</h3>
                  <p class="text-sm text-gray-600 mb-4">{{ item.genero }}</p>
                  <button 
                    @click="goToDetails(item.id)" 
                    class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-lg transition duration-300 w-full"
                  >
                    Ver Detalles
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <Footer />
    </div>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Navbar from '~/components/Navbar.vue';
import Footer from '~/components/Footer.vue';
import { usePeliculas } from '~/composables/communicationManagerPelicula';

const router = useRouter();
const route = useRoute();
const movieId = computed(() => route.params.id);

const { getPeliculaById, getSesiones, loading: apiLoading, error: apiError } = usePeliculas();

const movie = ref(null);
const sesiones = ref([]);
const similarMovies = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    loading.value = true;
    
    // Get movie details
    const movieData = await getPeliculaById(movieId.value);
    if (movieData.error) {
      throw new Error(movieData.error);
    }
    
    movie.value = movieData;
    
    // Get movie sessions
    const sesionesData = await getSesiones(movieId.value);
    if (!sesionesData.error) {
      sesiones.value = sesionesData;
    }
    
    // Get similar movies (same genre)
    const allMovies = await getPeliculas();
    if (!allMovies.error) {
      similarMovies.value = allMovies
        .filter(m => m.id !== movieId.value && m.genero === movie.value.genero)
        .slice(0, 4);
      
      // If not enough similar movies, add some random ones
      if (similarMovies.value.length < 4) {
        const randomMovies = allMovies
          .filter(m => m.id !== movieId.value && !similarMovies.value.some(sm => sm.id === m.id))
          .slice(0, 4 - similarMovies.value.length);
        
        similarMovies.value = [...similarMovies.value, ...randomMovies];
      }
    }
    
  } catch (err) {
    error.value = err.message || 'Error al cargar los detalles de la película';
    console.error('Error:', err);
  } finally {
    loading.value = false;
  }
});

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric', 
      month: 'long', 
      day: 'numeric'
    });
  } catch (e) {
    return dateString;
  }
};

// Format time
const formatTime = (timeString) => {
  if (!timeString) return 'Hora no disponible';
  
  try {
    return timeString.substring(0, 5);
  } catch (e) {
    return timeString;
  }
};

// Get embedded URL for trailer
const getEmbedUrl = (url) => {
  if (!url) return '';
  
  // Handle YouTube URLs
  if (url.includes('youtube.com/watch')) {
    const videoId = url.split('v=')[1].split('&')[0];
    return `https://www.youtube.com/embed/${videoId}`;
  }
  
  // Handle YouTube shortened URLs
  if (url.includes('youtu.be')) {
    const videoId = url.split('youtu.be/')[1].split('?')[0];
    return `https://www.youtube.com/embed/${videoId}`;
  }
  
  // Handle Vimeo URLs
  if (url.includes('vimeo.com')) {
    const videoId = url.split('vimeo.com/')[1].split('?')[0];
    return `https://player.vimeo.com/video/${videoId}`;
  }
  
  return url;
};

// Navigation functions
const goToBuy = (id) => {
  router.push(`/comprarEntrada/${id}`);
};

const goToDetails = (id) => {
  router.push(`/movieDetails/${id}`);
};

const selectSesion = (sesionId) => {
  router.push(`/comprarEntrada/${movieId.value}?sesion=${sesionId}`);
};
</script>

<style scoped>
/* Add responsive aspect ratio for video */
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-w-16 > * {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Add custom styling for movie details page */
.bg-gradient-to-r {
  background-size: 200% 200%;
  animation: gradientAnimation 15s ease infinite;
}

@keyframes gradientAnimation {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>