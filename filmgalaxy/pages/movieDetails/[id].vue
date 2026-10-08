<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary to-secondary py-12">
      <div class="container mx-auto px-4">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-[70vh]">
          <div class="animate-pulse">
            <div class="w-16 h-16 border-4 border-gold border-t-transparent rounded-full animate-spin"></div>
          </div>
        </div>
        <!-- Error State -->
        <div v-else-if="error" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl">
          <p class="text-center">{{ error }}</p>
        </div>
        <!-- Movie Details -->
        <div v-else class="space-y-12">
          <!-- Movie Info Card -->
          <div class="relative bg-secondary/80 backdrop-blur-sm rounded-xl overflow-hidden shadow-lg mt-24">
            <!-- Imagen de Fondo -->
            <div class="absolute inset-0 bg-cover bg-center opacity-10"
                 :style="{ backgroundImage: `url(${movie.imagen || 'https://via.placeholder.com/800x600?text=No+Poster'})` }">
            </div>
            <div class="relative flex flex-col md:flex-row">
              <!-- Poster de la Película -->
              <div class="md:w-1/3 p-4 flex justify-center items-center">
                <img :src="movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'" :alt="movie.titulo"
                     class="w-full h-auto rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
              </div>
              <!-- Información de la Película -->
              <div class="md:w-2/3 p-6 flex flex-col justify-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gold mb-4">{{ movie.titulo }}</h1>
                <div class="flex items-center mb-4">
                  <span class="text-2xl font-bold text-gold mr-2">{{ movie.calificacion }}/10</span>
                </div>
                <div class="grid grid-cols-2 gap-y-2 text-light mb-4">
                  <p><span class="font-semibold">Actores:</span> {{ movie.actores || 'N/A' }}</p>
                  <p><span class="font-semibold">Director:</span> {{ movie.director || 'N/A' }}</p>
                  <p><span class="font-semibold">Género:</span> {{ movie.genero || 'N/A' }}</p>
                  <p><span class="font-semibold">Idioma:</span> {{ movie.lenguaje || 'N/A' }}</p>
                  <p><span class="font-semibold">Duración:</span> {{ movie.duracion || 'N/A' }} min</p>
                  <p><span class="font-semibold">Clasificación:</span> {{ movie.clasificacion || 'N/A' }}</p>
                </div>
                <div class="mb-4">
                  <h3 class="text-xl font-semibold text-gold mb-2">Sinopsis:</h3>
                  <p class="text-light">{{ movie.descripcion || 'Sin descripción disponible' }}</p>
                </div>
                <div class="flex justify-end">
                  <button @click="goToBuy(movie.id)" class="btn-primary">
                    Comprar Entradas
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- Trailer -->
          <div v-if="trailerEmbedUrl" class="bg-secondary/80 p-6 max-w-4xl mx-auto rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold mb-4 text-gold">Trailer</h2>
            <div class="relative pb-[56.25%]">
              <iframe :src="trailerEmbedUrl" class="absolute top-0 left-0 w-full h-full rounded-lg" frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
              </iframe>
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
      error.value = 'La película no se encontró';
    }
  } catch (err) {
    error.value = 'Error al cargar los detalles de la película';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchMovieDetails();
});

const goToBuy = (id) => {
  router.push(`/billets/${id}`);
};
</script>
