<template>
    <v-app>
        <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-cream custom-bg pt-8">
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl md:text-5xl font-bold text-light text-center mb-8 animate-fade-in">
          Sesiones de Películas
        </h1>
      
        <!-- Loading state -->
        <div v-if="sessionsStore.loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>
        
        <!-- Error state -->
        <div v-else-if="sessionsStore.error" class="max-w-md mx-auto bg-red-500/80 text-light p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ sessionsStore.error }}</p>
        </div>
        
        <!-- No sessions available -->
        <div v-else-if="!sessionsStore.sessions.length" class="text-center py-12">
          <div class="text-light text-xl mb-4">No hay sesiones disponibles en este momento.</div>
          <button @click="refreshSessions" class="px-6 py-3 bg-gold hover:bg-gold/80 text-primary rounded-lg font-medium transition-colors duration-300">
            Actualizar sesiones
          </button>
        </div>
        
        <!-- Sessions list -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="session in sessionsStore.sessions" 
            :key="session.id" 
            class="bg-light/10 backdrop-blur-md rounded-xl overflow-hidden shadow-lg transform hover:scale-105 transition-all duration-300"
          >
            <!-- Imagen de la película -->
            <div class="relative h-48 overflow-hidden">
              <img 
                :src="session.movie?.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'" 
                :alt="session.movie?.titulo" 
                class="w-full h-full object-cover"
              />
              <div class="absolute bottom-0 left-0 right-0 bg-primary/80 p-3">
                <h3 class="text-xl font-bold text-light truncate">{{ session.movie?.titulo || 'Película' }}</h3>
              </div>
            </div>
            
            <!-- Información de la sesión -->
            <div class="p-5 space-y-3 bg-primary/40">
              <!-- Fecha y Hora -->
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-light">{{ formatDate(session.fecha) }}</p>
              </div>
              
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-light">{{ session.hora }}</p>
              </div>
              
              <!-- Calificación de la película -->
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.2-6.3 4.2 2.3-7.2-6-4.6h7.6z"/>
                </svg>
                <p class="text-light">Calificación: <span class="text-gold font-bold">{{ session.movie?.calificacion || 'N/A' }}/10</span></p>
              </div>
              
              <!-- Estado de la sesión -->
              <div class="flex items-center space-x-2">
                <span 
                  class="px-3 py-1 rounded text-xs font-bold"
                  :class="{
                    'bg-green-500/30 text-green-300': session.estado === 'disponible',
                    'bg-red-500/30 text-red-300': session.estado === 'cancelada',
                    'bg-gray-500/30 text-gray-300': session.estado === 'finalizada'
                  }"
                >
                  {{ session.estado?.toUpperCase() || 'DISPONIBLE' }}
                </span>
                
                <span 
                  v-if="session.dia_espectador" 
                  class="bg-blue-500/30 text-blue-300 text-xs font-medium px-2 py-1 rounded"
                >
                  Día del espectador
                </span>
              </div>
              
              <!-- Botón de compra -->
              <div class="pt-3">
                <button 
                  v-if="session.estado === 'disponible' || !session.estado" 
                  @click="comprarEntradas(session)" 
                  class="w-full bg-gold hover:bg-gold/80 text-primary font-medium py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center space-x-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                  </svg>
                  <span>Comprar Entradas</span>
                </button>
                
                <p v-else class="text-center text-gray-400 py-3">
                  Esta sesión no está disponible
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
    // Guardamos la sesión ID como query parameter para redirigir de vuelta después del login
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
/* Clase para el gradiente de fondo */
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}

.animate-fade-in {
  animation: fadeIn 0.8s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>