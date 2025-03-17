<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-cream custom-bg pt-16">
      <div class="container mx-auto px-4 py-8">
        <!-- Loading state -->
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="max-w-md mx-auto bg-red-500/80 text-light p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ error }}</p>
        </div>

        <!-- Content -->
        <div v-else-if="session" class="max-w-4xl mx-auto">
          <!-- Movie Info -->
          <div class="bg-light/10 backdrop-blur-md rounded-xl overflow-hidden shadow-lg mb-8">
            <div class="md:flex">
              <!-- Movie Poster -->
              <div class="md:w-1/3">
                <img 
                  :src="session.movie?.imagen" 
                  :alt="session.movie?.titulo"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Movie Details -->
              <div class="p-6 md:w-2/3">
                <h1 class="text-3xl font-bold text-light mb-4">{{ session.movie?.titulo }}</h1>
                
                <div class="space-y-4">
                  <!-- Session Info -->
                  <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-light">{{ formatDate(session.fecha) }}</span>
                  </div>

                  <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-light">{{ session.hora }}</span>
                  </div>

                  <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.2-6.3 4.2 2.3-7.2-6-4.6h7.6z"/>
                    </svg>
                    <span class="text-light">Calificación: <span class="text-gold font-bold">{{ session.movie?.calificacion }}/10</span></span>
                  </div>

                  <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-light">Duración: {{ formatDuration(session.movie?.duracion) }}</span>
                  </div>

                  <!-- Price Info -->
                  <div class="mt-6 p-4 bg-primary/30 rounded-lg">
                    <div class="flex justify-between items-center">
                      <span class="text-light">Precio por entrada:</span>
                      <span class="text-gold font-bold text-xl">{{ session.precio || '9.50' }}€</span>
                    </div>
                    <div v-if="session.dia_espectador" class="text-blue-300 text-sm mt-2">
                      ¡Día del espectador! Precio especial
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ticket Selection -->
          <div class="bg-light/10 backdrop-blur-md rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold text-light mb-6">Seleccionar Entradas</h2>
            
            <div class="space-y-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <label for="tickets" class="text-light">Número de entradas:</label>
                  <select 
                    v-model="numberOfTickets" 
                    id="tickets"
                    class="bg-primary/50 text-light border border-gold/30 rounded-lg px-3 py-2 focus:outline-none focus:border-gold"
                  >
                    <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
                  </select>
                </div>
                <div class="text-right">
                  <div class="text-light">Total:</div>
                  <div class="text-gold font-bold text-2xl">{{ ((session.precio || 9.5) * numberOfTickets).toFixed(2) }}€</div>
                </div>
              </div>

              <button 
                @click="processPurchase"
                class="w-full bg-gold hover:bg-gold/80 text-primary font-bold py-4 px-6 rounded-lg transition-colors duration-300 flex items-center justify-center space-x-2"
              >
                <span>Confirmar Compra</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- No session found -->
        <div v-else class="text-center py-12">
          <p class="text-light text-xl mb-4">No se encontró la sesión especificada</p>
          <button 
            @click="router.push('/movies')" 
            class="bg-gold hover:bg-gold/80 text-primary font-medium py-2 px-6 rounded-lg transition-colors duration-300"
          >
            Ver todas las películas
          </button>
        </div>
      </div>
    </div>
    <Footer />
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSessionsStore } from '@/stores/sessionsStore';

const route = useRoute();
const router = useRouter();
const sessionsStore = useSessionsStore();

const loading = ref(true);
const error = ref(null);
const session = ref(null);
const numberOfTickets = ref(1);

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

const formatDuration = (minutes) => {
  if (!minutes) return 'Duración no disponible';
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours}h ${mins}min`;
};

const processPurchase = () => {
  // Aquí iría la lógica para procesar la compra
  alert(`Procesando compra de ${numberOfTickets.value} entradas para ${session.value.movie.titulo}`);
};

onMounted(async () => {
  try {
    loading.value = true;
    const sessionId = route.params.id;
    if (!sessionId) {
      throw new Error('No se especificó un ID de sesión');
    }

    // Obtener la sesión específica
    await sessionsStore.fetchSessions();
    session.value = sessionsStore.sessions.find(s => s.id === sessionId);
    
    if (!session.value) {
      throw new Error('Sesión no encontrada');
    }
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
});
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}
</style>