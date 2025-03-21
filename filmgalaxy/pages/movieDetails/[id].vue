<template>
  <v-app>
    <Navbar />
    <br><br>
    <div class="min-h-screen bg-gradient-to-br from-primary to-secondary">
      <div class="container mx-auto px-3 py-8 animate-fade-in">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-[70vh]">
          <div class="animate-pulse-slow">
            <div class="w-16 h-16 border-4 border-gold border-t-transparent rounded-full animate-spin"></div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl animate-slide-up">
          <p class="text-center">{{ error }}</p>
        </div>

        <!-- Movie Details -->
        <div v-else class="space-y-8 animate-slide-up">
          <!-- Movie Info Section -->
          <div class="relative mt-10">
            <!-- Fondo con Imagen de la Película -->
            <div class="absolute inset-0 h-full bg-cover bg-center opacity-20 rounded-xl overflow-hidden"
              :style="{ backgroundImage: `url(${movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'})` }">
            </div>
            <div class="relative flex flex-col lg:flex-row gap-8">
              <!-- Poster de la Película -->
              <div class="lg:w-1/3">
                <div class="movie-card overflow-hidden rounded-xl">
                  <img :src="movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'" :alt="movie.titulo"
                    class="w-full h-auto object-cover transition-transform duration-300 hover:brightness-110" />
                </div>
              </div>

              <!-- Información de la Película -->
              <div class="lg:w-2/3 flex flex-col justify-center">
                <div class="movie-card p-8 backdrop-blur-sm bg-secondary/70 rounded-xl">
                  <h1 class="text-4xl md:text-5xl font-bold mb-9 text-gold">{{ movie.titulo }}</h1>

                  <!-- Calificación -->
                  <div class="mb-6 flex items-center space-x-4">
                    <span class="text-2xl font-bold text-gold">{{ movie.calificacion }}/10</span>
                  </div>

                  <!-- Detalles -->
                  <div class="space-y-4 text-light">
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Actores:</span>
                      <span>{{ movie.actores || 'N/A' }}</span>
                    </p>
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Director:</span>
                      <span>{{ movie.director || 'N/A' }}</span>
                    </p>
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Género:</span>
                      <span>{{ movie.genero || 'N/A' }}</span>
                    </p>
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Idioma:</span>
                      <span>{{ movie.lenguaje || 'N/A' }}</span>
                    </p>
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Duración:</span>
                      <span>{{ movie.duracion || 'N/A' }} min</span>
                    </p>
                    <p class="flex items-center">
                      <span class="font-semibold w-32">Clasificación:</span>
                      <span>{{ movie.clasificacion || 'N/A' }}</span>
                    </p>
                  </div>

                  <!-- Sinopsis -->
                  <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-3 text-gold">Sinopsis:</h3>
                    <p class="text-light leading-relaxed">
                      {{ movie.descripcion || 'Sin descripción disponible' }}
                    </p>
                  </div>

                  <!-- Botón para Comprar Entradas -->
                  <div class="flex justify-center pt-8">
                    <button @click="goToBuy(movie.id)" class="btn-primary">
                      Comprar Entradas
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Trailer -->
          <div v-if="trailerEmbedUrl" class="movie-card p-6 max-w-3xl mx-auto rounded-xl">
            <h2 class="text-xl font-semibold mb-4 text-gold">Trailer</h2>
            <div class="relative pt-[45%]">
              <iframe :src="trailerEmbedUrl" class="absolute top-0 left-0 w-full h-full rounded-lg" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
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

const actorsList = computed(() => {
  if (!movie.value.actores) return [];
  try {
    return JSON.parse(movie.value.actores);
  } catch (e) {
    console.error('Error parsing actors list:', e);
    return [];
  }
});

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
