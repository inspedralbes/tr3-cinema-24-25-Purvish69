<template>
  <v-app>
    <div class="min-h-screen custom-bg">
      <!-- Barra de navegación -->
      <Navbar />

      <div class="container mx-auto px-4 py-8">
        <!-- Encabezado principal -->
        <div class="container mx-auto px-6" style="padding-top: 60px; padding-bottom: 10px;">

          <h1 class="text-4x1 md:text-5xl font-bold text-gold text-center mb-6 animate-fade-in">
            Películas en Cartelera
          </h1>

          <!-- Sección para búsqueda y filtros - CON NUEVO DISEÑO DE BUSCADOR -->
          <div
            class="bg-light/10 backdrop-blur-md rounded-xl p-3 max-w-4xl mx-auto transform hover:shadow-2xl transition-all duration-300">
            <div class="flex flex-col md:flex-row items-end gap-3">
              <!-- Barra de búsqueda con nuevo diseño -->
              <div class="flex-grow">
                <div class="relative flex items-center">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" viewBox="0 0 20 20"
                      fill="currentColor">
                      <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" v-model="searchQuery" placeholder="¿Qué película buscas?"
                    class="w-full pl-10 pr-4 py-3 rounded-full border-2 border-gold/40 bg-light/10 text-light placeholder-gray-300 focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/30 transition-all duration-300 shadow-inner" />
                </div>
              </div>

              <!-- Filtro por idioma -->
              <div class="md:w-40 relative">
                <select v-model="filterLenguaje"
                  class="w-full p-2.5 rounded-lg bg-light/5 border-2 border-accent/30 text-light focus:border-accent focus:ring-2 focus:ring-accent/50 transition-all duration-300 appearance-none pl-4 pr-8">
                  <option value="">Todos</option>
                  <option value="Español">Español</option>
                  <option value="Inglés">Inglés</option>
                  <option value="Subtitulada">Subtitulada</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
              <!-- Botón para limpiar los filtros -->
              <div class="md:ml-1">
                <button @click="resetFilters"
                  class="w-full md:w-auto px-4 py-2.5 bg-gold hover:bg-gold/80 text-primary rounded-full font-medium transition-colors duration-300 flex items-center justify-center space-x-1 shadow-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v4a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                      clip-rule="evenodd" />
                  </svg>
                  <span>Limpiar</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Indicador mientras se cargan los datos -->
        <div v-if="movieStore.loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Mensaje de error -->
        <div v-else-if="movieStore.error"
          class="max-w-md mx-auto bg-red-500/80 text-light p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ movieStore.error }}</p>
        </div>

        <!-- Aviso cuando no hay resultados -->
        <div v-else-if="filteredMovies.length === 0" class="text-center py-12">
          <div class="text-light text-xl mb-4">No se encontraron películas con los filtros seleccionados</div>
          <button @click="resetFilters"
            class="px-6 py-3 bg-gold hover:bg-gold/80 text-primary rounded-lg font-medium transition-colors duration-300">
            Limpiar Filtros
          </button>
        </div>

        <!-- Cuadrícula de películas  -->
        <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-4 mt-8">
          <div v-for="movie in filteredMovies" :key="movie.id"
            class="group relative bg-light/10 rounded-lg overflow-hidden backdrop-blur-sm hover:scale-105 transition-all duration-300 animate-fade-in">
            <!-- Póster de la película (sin animación en hover) -->
            <div class="relative aspect-[2/3] overflow-hidden">
              <img :src="movie.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'" :alt="movie.titulo"
                class="w-full h-full object-cover" />

              <!-- Duración y calificación superpuestas en la parte inferior de la imagen -->
              <div class="absolute bottom-0 left-0 right-0 p-2 bg-primary/80">
                <div class="flex justify-between">
                  <p class="text-light text-xs">{{ movie.duracion || 'N/A' }} min</p>
                  <p class="text-gold text-xs flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                      fill="currentColor">
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ movie.calificacion || 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Información de la película (simplificada) -->
            <div class="p-2 space-y-1">
              <h2 class="text-sm font-bold text-light line-clamp-2">{{ movie.titulo }}</h2>
              <p class="text-xs text-gray-300">{{ movie.lenguaje || 'Idioma no especificado' }}</p>
              <p v-if="movie.genero" class="text-xs text-gray-400">{{ movie.genero }}</p>

              <!-- Botones para ver detalles o comprar entradas - AUMENTADOS DE TAMAÑO -->
              <div class="flex gap-1 pt-1">
                <button @click="goToDetails(movie.id)"
                  class="flex-1 bg-gold hover:bg-gold/80 text-primary font-medium py-2 px-2 rounded text-sm transition-colors duration-300">
                  Detalls
                </button>
                <button @click="goToBuy(movie.id)"
                  class="flex-1 bg-accent hover:bg-accent/80 text-light font-medium py-2 px-2 rounded text-sm transition-colors duration-300">
                  Comprar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pie de página -->
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

// Referencias reactivas para gestionar los filtros de búsqueda
const searchQuery = ref('');
// Eliminado filterGenero ya que se eliminó ese filtro
const filterLenguaje = ref('');

// Al montar el componente, obtenemos las películas si aún no se han cargado
onMounted(async () => {
  if (movieStore.allMovies.length === 0) {
    await movieStore.fetchMovies();
  }
});

// Propiedad computada que filtra las películas según la búsqueda y los filtros seleccionados
const filteredMovies = computed(() => {
  if (!Array.isArray(movieStore.allMovies)) {
    console.error("allMovies no es un array:", movieStore.allMovies);
    return [];
  }

  return movieStore.allMovies.filter(movie => {
    // Filtro de búsqueda: comprueba título, género o sinopsis
    const searchMatch = !searchQuery.value ||
      (movie.titulo?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (movie.genero?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (movie.sinopsis?.toLowerCase().includes(searchQuery.value.toLowerCase()));

    // Filtro solo por idioma, ya que se eliminó el filtro de género
    const matchLenguaje = !filterLenguaje.value || (movie.lenguaje === filterLenguaje.value);

    return searchMatch && matchLenguaje;
  });
});

// Función para restablecer todos los filtros
const resetFilters = () => {
  searchQuery.value = '';
  filterLenguaje.value = '';
};

// Función para formatear la fecha de estreno de la película
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

// Función para navegar a la vista de detalles de la película
const goToDetails = (id) => {
  router.push(`/movieDetails/${id}`);
};

// Función para navegar a la vista de compra de entradas
const goToBuy = (id) => {
  router.push(`/comprarEntrada/${id}`);
};
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}

/* Estilos de animación y transiciones */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out forwards;
}

/* Transiciones suaves para todos los cambios */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Manejo de la truncación de texto */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Asegurarse de ocultar completamente el desplegable nativo */
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: none !important;
}


/* Estilos específicos para dispositivos móviles */
@media (max-width: 640px) {
  .grid-cols-2>* {
    margin-bottom: 0.5rem;
  }

  .line-clamp-2 {
    -webkit-line-clamp: 1;
    line-clamp: 1;
  }

  /* Hacer que los botones sean más fáciles de tocar en dispositivos móviles */
  button {
    min-height: 40px;
  }

  /* Asegurar que los textos de los botones sean legibles */
  .text-sm {
    font-size: 0.8rem;
  }
}


/* Estilos específicos para el nuevo buscador */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
}
</style>