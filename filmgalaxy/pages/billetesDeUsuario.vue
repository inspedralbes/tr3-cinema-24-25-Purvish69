<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-dark custom-bg pt-16">
      <div class="container mx-auto px-4 py-8">
        <!-- Estado de carga -->
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Estado de error -->
        <div v-else-if="error" class="max-w-md mx-auto bg-red-500/80 text-light p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ error }}</p>
          <button @click="router.push('/movies')"
            class="mt-4 bg-light text-dark px-6 py-2 rounded-full hover:bg-light/80">
            Ver películas
          </button>
        </div>

        <!-- Sin billetes -->
        <div v-else-if="!tickets.length" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl text-center">
          <h2 class="text-2xl font-bold text-gold mb-4">No tienes entradas</h2>
          <p class="mb-6">¡Compra tus entradas ahora y disfruta del mejor cine!</p>
          <button @click="router.push('/movies')" class="bg-gold text-dark px-6 py-2 rounded-full hover:bg-gold/80">
            Ver películas
          </button>
        </div>

        <!-- Lista de billetes -->
        <div v-else class="max-w-4xl mx-auto">
          <h1 class="text-5xl font-bold text-gold mb-8 text-center">Mis Entradas</h1>
          <div class="space-y-6">
            <div v-for="ticket in tickets" :key="ticket.id" class="glass-effect rounded-xl overflow-hidden">
              <div class="md:flex">
                <!-- Póster de la película - Solución mejorada para evitar errores de carga -->
                <!-- <div class="md:w-1/4 bg-dark/60">
                  <div class="w-full h-full">
                    <img v-if="ticket.movie_session?.movie?.imagen || ticket.movie?.imagen" 
                      :src="ticket.movie_session?.movie?.imagen || ticket.movie?.imagen"
                      :alt="ticket.movie_session?.movie?.titulo || ticket.movie?.titulo || 'Película'" 
                      class="w-full h-full object-cover"
                      @error="handleImageError($event, ticket)" />
                    <div v-else class="w-full h-full flex items-center justify-center bg-dark">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gold/50" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                      </svg>
                    </div>
                  </div>
                </div> -->
                <!-- Detalles del billete -->
                <div class="p-6 md:w-3/4">
                  <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-light">
                      {{ ticket.movie_session?.movie?.titulo || ticket.movie?.titulo || 'Película sin título' }}
                    </h2>
                    <span class="bg-gold/20 text-gold px-3 py-1 rounded-full text-sm">
                      Confirmado
                    </span>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-light">
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span class="font-medium">
                        {{ formatDayAndDate(ticket.movie_session?.fecha || ticket.session?.fecha) }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="font-medium">
                        {{ ticket.movie_session?.hora || ticket.session?.hora }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span class="font-medium">
                        Sala {{ ticket.movie_session?.sala || ticket.session?.sala || 1 }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span class="font-medium">
                        Asiento {{ ticket.seat?.fila }}{{ ticket.seat?.numero }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                      </svg>
                      <span class="font-medium">{{ ticket.payment?.payment_method || 'Tarjeta' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="font-medium">{{ ticket.precio }}€</span>
                    </div>
                  </div>
                  <div class="mt-4 pt-4 border-t border-light/20">
                    <div class="flex justify-between items-center">
                      <div class="text-sm text-light/70">
                        Comprado el {{ formatDate(ticket.created_at) }}
                      </div>
                      <div class="text-sm text-gold">
                        Código: {{ ticket.codigo_confirmacion ? ticket.codigo_confirmacion.substring(0, 8) : 'N/A' }}
                      </div>
                    </div>
                  </div>
                </div>
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
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '~/composables/useAuth';
import { usePeliculas } from '~/composables/communicationManagerPelicula';

const router = useRouter();
const { userId, isAuthenticated } = useAuth();
const { getTickets, getPeliculaById, getSesiones } = usePeliculas();

const loading = ref(true);
const error = ref(null);
const tickets = ref([]);

// Función para manejar errores de carga de imagen
const handleImageError = (event, ticket) => {
  // Sustituir con un placeholder y prevenir intentos repetidos de carga
  event.target.src = 'https://via.placeholder.com/300x450?text=No+Image';
  event.target.onerror = null; // Prevenir recursión infinita

  // Actualizar imagen en los objetos de datos
  if (ticket.movie_session?.movie) {
    ticket.movie_session.movie.imagen = 'https://via.placeholder.com/300x450?text=No+Image';
  }
  if (ticket.movie) {
    ticket.movie.imagen = 'https://via.placeholder.com/300x450?text=No+Image';
  }
};

// Función mejorada para formatear la fecha a un formato legible
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  try {
    // Asegurarse de que la fecha se interpreta correctamente
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Fecha no disponible';
    }
    return date.toLocaleDateString('es-ES', options);
  } catch (e) {
    console.error('Error al formatear la fecha:', e);
    return 'Fecha no disponible';
  }
}

// Función para formatear fecha para mostrar solo el día de la semana y la fecha
const formatDayAndDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  const options = { weekday: 'long', day: 'numeric', month: 'long' };
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Fecha no disponible';
    }
    return date.toLocaleDateString('es-ES', options);
  } catch (e) {
    console.error('Error al formatear el día y fecha:', e);
    return 'Fecha no disponible';
  }
};

// Obtener el ID del usuario de manera segura
const getCurrentUserId = computed(() => {
  // Primero intentar obtener de la composable
  if (userId.value) return userId.value;
  
  // Luego revisar localStorage como respaldo
  if (typeof window !== 'undefined') {
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
    return storedUser.id;
  }
  
  return null;
});

// Cargar los tickets del usuario
onMounted(async () => {
  if (!isAuthenticated.value) {
    error.value = 'Debes iniciar sesión para ver tus entradas';
    loading.value = false;
    return;
  }

  try {
    const userIdValue = getCurrentUserId.value;
    
    if (!userIdValue) {
      error.value = 'No se pudo identificar al usuario';
      loading.value = false;
      return;
    }

    // Obtener todos los tickets y filtrar por el usuario actual
    const allTickets = await getTickets();
    
    if (!allTickets || allTickets.error) {
      console.error('Error al obtener tickets:', allTickets?.error);
      error.value = 'Error al cargar tus entradas. Por favor, inténtalo de nuevo más tarde.';
      loading.value = false;
      return;
    }
    
    // Filtrar solo los tickets del usuario actual
    let userTickets = allTickets.filter(ticket => ticket.user_id === userIdValue);
    
    if (!userTickets.length) {
      loading.value = false;
      return;
    }
    
    // Procesamos los tickets para asegurarnos que tienen toda la información correcta
    // según la estructura exacta del JSON que estamos recibiendo
    userTickets = userTickets.map(ticket => {
      // Asegurémonos de que todos los campos necesarios estén presentes
      const enhancedTicket = { ...ticket };
      
      // Asegurarse de que movie_session tenga datos, incluso si está anidado de otra manera
      if (!enhancedTicket.movie_session && enhancedTicket.movieSession_id) {
        // Si tenemos movieSession_id pero no movie_session, podríamos intentar buscar 
        // o crear un objeto movie_session básico
        enhancedTicket.movie_session = {
          id: enhancedTicket.movieSession_id,
          fecha: enhancedTicket.session?.fecha || new Date().toISOString().split('T')[0],
          hora: enhancedTicket.session?.hora || '19:00',
          sala: enhancedTicket.session?.sala || '1'
        };
      }
      
      // Asegurarse de que movie_session.movie tenga datos
      if (enhancedTicket.movie_session && !enhancedTicket.movie_session.movie) {
        enhancedTicket.movie_session.movie = {};
      }
      
      return enhancedTicket;
    });
    
    console.log('Datos de tickets de usuario procesados:', userTickets);
    tickets.value = userTickets;
  } catch (err) {
    console.error('Error al cargar tickets:', err);
    error.value = 'Error al cargar tus entradas. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
});
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 20%, #9f8f7e 90%);
}

.glass-effect {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}
</style>