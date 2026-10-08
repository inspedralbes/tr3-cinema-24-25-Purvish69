<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-cream bg-custom">
      <div class="container mx-auto px-6" style="padding-top: 120px; padding-bottom: 60px;">
        <h1 class="text-3xl md:text-5xl font-bold text-gold text-center mb-8 animate-fade-in">
          Sesiones de Películas
        </h1>

        <!-- Loading state -->
        <div v-if="sessionsStore.loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Error state -->
        <div v-else-if="sessionsStore.error"
          class="max-w-md mx-auto bg-red-500/80 text-light p-4 rounded-lg backdrop-blur-sm">
          <p class="text-center">{{ sessionsStore.error }}</p>
        </div>

        <!-- No sessions available -->
        <div v-else-if="!sessionsStore.sessions.length" class="text-center py-8">
          <div class="text-light text-lg mb-4">No hay sesiones disponibles en este momento.</div>
          <button @click="refreshSessions"
            class="px-6 py-3 bg-gold hover:bg-gold/80 text-primary rounded-lg font-medium transition-colors duration-300 shadow-md">
            Actualizar sesiones
          </button>
        </div>

        <!-- Sessions list -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="session in sessionsStore.sessions" :key="session.id"
            class="bg-light/10 backdrop-blur-md rounded-lg overflow-hidden shadow-lg transform hover:scale-102 transition-all duration-300 movie-card">
            <!-- Imagen de la película -->
            <div class="relative h-44 overflow-hidden">
              <img :src="session.movie?.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'"
                :alt="session.movie?.titulo" class="w-full h-full object-cover" />
              <div class="absolute bottom-0 left-0 right-0 bg-primary/90 p-2">
                <h3 class="text-base font-bold text-light truncate">{{ session.movie?.titulo || 'Película' }}</h3>
              </div>
            </div>

            <!-- Información de la sesión -->
            <div class="p-3 space-y-2 bg-primary/40">
              <!-- Fecha y Hora -->
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-light text-sm">{{ formatDate(session.fecha) }}</p>
              </div>

              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-light text-sm">{{ session.hora }}</p>
              </div>

              <!-- Calificación de la película -->
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold" fill="currentColor"
                  viewBox="0 0 24 24">
                  <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.2-6.3 4.2 2.3-7.2-6-4.6h7.6z" />
                </svg>
                <p class="text-light text-sm">Calificación: <span class="text-gold font-bold">{{
                  session.movie?.calificacion || 'N/A' }}/10</span></p>
              </div>

              <!-- Estado de la sesión -->
              <div class="flex items-center flex-wrap gap-1">
                <span class="px-2 py-1 rounded-full text-xs font-bold" :class="{
                  'bg-green-500/30 text-green-300': session.estado === 'disponible',
                  'bg-red-500/30 text-red-300': session.estado === 'cancelada',
                  'bg-gray-500/30 text-gray-300': session.estado === 'finalizada'
                }">
                  {{ session.estado?.toUpperCase() || 'DISPONIBLE' }}
                </span>

                <span v-if="session.dia_espectador"
                  class="bg-blue-500/30 text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                  Día espectador
                </span>
              </div>

              <!-- Botón de compra -->
              <div class="pt-2">
                <button v-if="session.estado === 'disponible' || !session.estado" @click="comprarEntradas(session)"
                  class="w-full bg-gold hover:bg-gold/80 text-primary font-medium py-2 px-3 rounded-md transition-colors duration-300 flex items-center justify-center space-x-2 shadow-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                  </svg>
                  <span class="text-sm">Comprar Entradas</span>
                </button>

                <p v-else class="text-center text-gray-400 py-2 text-xs">
                  Sesión no disponible
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </v-app>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSessionsStore } from '@/stores/sessionsStore'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const sessionsStore = useSessionsStore()
const { isAuthenticated } = useAuth()

// Format date to a more readable format
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  try {
    return new Date(dateString).toLocaleDateString('es-ES', options);
  } catch (error) {
    console.error('Error al formatear la fecha:', error);
    return dateString;
  }
}

// Handle buying tickets - check authentication first
const comprarEntradas = (session) => {
  // Verificar si el usuario está autenticado
  if (!isAuthenticated.value) {
    // Si no está autenticado, redirigir a la página de login
    router.push({
      path: '/login',
      query: { redirect: `/billets/${session.id}` }
    });

    // Mostrar mensaje de alerta
    alert('Debes iniciar sesión para comprar entradas');
    return;
  }

  // Si está autenticado, redirigir a la página de compra
  if (session && session.id) {
    router.push(`/billets/${session.id}`);
  }
}

// Refresh sessions
const refreshSessions = async () => {
  if (route.params.movieId) {
    await sessionsStore.fetchSessionsByMovieId(route.params.movieId);
  } else {
    await sessionsStore.fetchSessions();
  }
}

onMounted(async () => {
  // If we have a movie_id in the route params, fetch sessions for that movie
  if (route.params.movieId) {
    await sessionsStore.fetchSessionsByMovieId(route.params.movieId);
  }
  // Otherwise, fetch all sessions
  else {
    await sessionsStore.fetchSessions();
  }
})
</script>

<style>
/* CSS puro para el gradiente de fondo y animaciones */
.bg-custom {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
  min-height: 100vh;
}

.animate-fade-in {
  animation: fadeIn 0.8s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* CSS personalizado para las tarjetas de película */
.movie-card {
  display: flex;
  flex-direction: column;
  height: auto;
  max-height: 380px;
}

/* Añadimos hover personalizado con CSS puro */
.movie-card:hover {
  box-shadow: 0 10px 25px -5px rgba(234, 224, 213, 0.3);
}

/* Media queries para ajustar tamaños en dispositivos más pequeños */
@media (max-width: 640px) {
  .movie-card {
    max-height: none;
  }
}

/* Animación personalizada para el botón de compra */
button.bg-gold:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease;
}
</style>