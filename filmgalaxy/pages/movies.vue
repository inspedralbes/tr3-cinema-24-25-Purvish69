<!-- pages/movies.vue -->
<template>
  <v-app>
    <div class="min-h-screen bg-gradient-to-r from-[#22223B] to-[#EAE0D5] text-white">
      <!-- Incluye tu Navbar (adaptado a Tailwind) -->
      <Navbar />

      <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Películas</h1>
        
        <!-- Sección de filtros -->
        <div class="mb-6 flex flex-col md:flex-row gap-4">
          <input
            type="time"
            v-model="filterHora"
            class="p-2 rounded border border-gray-300 text-black"
            placeholder="Hora"
          />
          <select v-model="filterGenero" class="p-2 rounded border border-gray-300 text-black">
            <option value="">Todos los géneros</option>
            <option value="Accion">Acción</option>
            <option value="Drama">Drama</option>
          </select>
          <select v-model="filterLenguaje" class="p-2 rounded border border-gray-300 text-black">
            <option value="">Todos los lenguajes</option>
            <option value="Español">Español</option>
            <option value="Inglés">Inglés</option>
          </select>
        </div>
        
        <!-- Lista de películas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="movie in filteredMovies"
            :key="movie.id"
            class="bg-white text-black rounded shadow-lg overflow-hidden"
          >
            <img :src="movie.poster" alt="Poster" class="w-full h-64 object-cover">
            <div class="p-4">
              <h2 class="text-xl font-bold mb-2">{{ movie.titulo }}</h2>
              <p class="text-sm mb-4">{{ movie.genero }}</p>
              <div class="flex justify-between">
                <button
                  @click="goToDetails(movie.id)"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                  Ver Detalles
                </button>
                <button
                  @click="goToBuy(movie.id)"
                  class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                >
                  Comprar Entradas
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Incluye tu Footer -->
      <Footer />
    </div>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Navbar from '~/components/Navbar.vue';
import Footer from '~/components/Footer.vue';
import { usePeliculas } from '~/composables/communicationManagerPelicula';

const { getPeliculas } = usePeliculas();
const router = useRouter();

const movies = ref([]);
const filterHora = ref('');
const filterGenero = ref('');
const filterLenguaje = ref('');

onMounted(async () => {
  const res = await getPeliculas();
  if (!res.error) {
    movies.value = res;
  } else {
    console.error(res.error);
  }
});

const filteredMovies = computed(() => {
  return movies.value.filter(movie => {
    const matchHora = filterHora.value ? movie.hora === filterHora.value : true;
    const matchGenero = filterGenero.value ? movie.genero === filterGenero.value : true;
    const matchLenguaje = filterLenguaje.value ? movie.lenguaje === filterLenguaje.value : true;
    return matchHora && matchGenero && matchLenguaje;
  });
});

const goToDetails = (id) => {
  router.push(`/movieDetails/${id}`);
};

const goToBuy = (id) => {
  router.push(`/comprarEntrada/${id}`);
};
</script>