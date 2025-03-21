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
          <button @click="router.push('/movies')" class="mt-4 bg-light text-dark px-6 py-2 rounded-full hover:bg-light/80">
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
          <h1 class="text-3xl font-bold text-gold mb-8 text-center">Mis Entradas</h1>
          <div class="space-y-6">
            <div v-for="ticket in tickets" :key="ticket.id" class="glass-effect rounded-xl overflow-hidden">
              <div class="md:flex">
                <!-- Póster de la película - Solución mejorada para evitar errores de carga -->
                <div class="md:w-1/4 bg-dark/60">
                  <div class="w-full h-full">
                    <img 
                      v-if="ticket.movie && ticket.movie.imagen" 
                      :src="ticket.movie.imagen" 
                      :alt="ticket.movie?.titulo || 'Película'" 
                      class="w-full h-full object-cover"
                      @error="handleImageError($event, ticket)"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-dark">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gold/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                      </svg>
                    </div>
                  </div>
                </div>
                <!-- Detalles del billete -->
                <div class="p-6 md:w-3/4">
                  <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-light">{{ ticket.movie?.titulo || 'Película sin título' }}</h2>
                    <span class="bg-gold/20 text-gold px-3 py-1 rounded-full text-sm">
                      {{ ticket.status || 'Activo' }}
                    </span>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-light">
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span class="font-medium">{{ formatDayAndDate(ticket.session?.fecha) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="font-medium">{{ ticket.session?.hora || getSessionTime(ticket) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span class="font-medium">Sala {{ ticket.session?.sala || getSavedSessionData(ticket)?.sala || '1' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span class="font-medium">
                        Asiento {{ ticket.seat?.fila || getAsientoData(ticket)?.fila || 'A' }}{{ ticket.seat?.numero || getAsientoData(ticket)?.numero || '1' }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                      </svg>
                      <span class="font-medium">{{ ticket.payment?.payment_method || 'Tarjeta' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="font-medium">{{ ticket.precio || '8.50' }}€</span>
                    </div>
                  </div>
                  <div class="mt-4 pt-4 border-t border-light/20">
                    <div class="text-sm text-light/70">
                      Comprado el {{ formatDate(ticket.created_at) }}
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
const { getUserTickets, getTickets, getPeliculaById, getSesiones } = usePeliculas();

const loading = ref(true);
const error = ref(null);
const tickets = ref([]);

// Función para manejar errores de carga de imagen
const handleImageError = (event, ticket) => {
  // Sustituir con un placeholder y prevenir intentos repetidos de carga
  event.target.src = 'https://via.placeholder.com/300x450?text=No+Image';
  event.target.onerror = null; // Prevenir recursión infinita
  
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

// Función para obtener datos de sesión almacenados en localStorage
const getSavedSessionData = (ticket) => {
  try {
    // Intentar recuperar los datos de sesión desde localStorage
    if (typeof window !== 'undefined') {
      const savedSessions = JSON.parse(localStorage.getItem('sessionData') || '{}');
      // Buscar por session_id si está disponible
      if (ticket.session_id && savedSessions[ticket.session_id]) {
        return savedSessions[ticket.session_id];
      }
      
      // Buscar por clave de película y fecha si está disponible
      if (ticket.movie_id && ticket.fecha) {
        const sessionKey = `${ticket.movie_id}_${ticket.fecha}`;
        if (savedSessions[sessionKey]) {
          return savedSessions[sessionKey];
        }
      }
      
      // Buscar cualquier sesión relacionada con la película
      if (ticket.movie_id) {
        const movieSessions = Object.values(savedSessions).filter(
          session => session.movie_id === ticket.movie_id
        );
        
        if (movieSessions.length > 0) {
          return movieSessions[0];
        }
      }
    }
    
    return null;
  } catch (e) {
    console.error('Error al recuperar datos de sesión:', e);
    return null;
  }
};

// Función para obtener la hora de la sesión desde localStorage o datos alternativos
const getSessionTime = (ticket) => {
  // Primero intentar obtener de ticket.session
  if (ticket.session?.hora) {
    return ticket.session.hora;
  }
  
  // Luego intentar obtener de localStorage
  const savedSession = getSavedSessionData(ticket);
  if (savedSession?.hora) {
    return savedSession.hora;
  }
  
  // Si hay hora directamente en el ticket
  if (ticket.hora) {
    return ticket.hora;
  }
  
  // Valor por defecto
  return '19:00';
};

// Función para obtener datos del asiento desde localStorage
const getAsientoData = (ticket) => {
  try {
    if (typeof window !== 'undefined') {
      // Intentar recuperar datos de asientos seleccionados
      const savedSeats = JSON.parse(localStorage.getItem('selectedSeats') || '{}');
      
      // Buscar por ticket_id si está disponible
      if (ticket.id && savedSeats[ticket.id]) {
        return savedSeats[ticket.id];
      }
      
      // Buscar por session_id si está disponible
      if (ticket.session_id && savedSeats[ticket.session_id]) {
        return savedSeats[ticket.session_id];
      }
      
      // Usar datos del ticket si están disponibles
      if (ticket.seat_row && ticket.seat_number) {
        return {
          fila: ticket.seat_row,
          numero: ticket.seat_number
        };
      }
    }
    
    return null;
  } catch (e) {
    console.error('Error al recuperar datos de asiento:', e);
    return null;
  }
};

// Función para cargar datos completos de los tickets
const loadCompleteTicketData = async (ticketsList) => {
  const completeTickets = [];
  
  for (const ticket of ticketsList) {
    try {
      // Crear una copia del ticket original
      const enhancedTicket = { ...ticket };
      
      // Cargar datos de la película si existe movie_id
      if (ticket.movie_id) {
        try {
          const movieData = await getPeliculaById(ticket.movie_id);
          
          if (movieData && !movieData.error) {
            enhancedTicket.movie = movieData;
            
            // Verificar que la imagen existe y es accesible
            if (movieData.imagen) {
              // Si la URL de la imagen no empieza con http, añadir la URL base
              if (!movieData.imagen.startsWith('http')) {
                enhancedTicket.movie.imagen = `${import.meta.env.VITE_API_URL || ''}${movieData.imagen}`;
              }
            }
          }
        } catch (movieError) {
          console.warn('Error al cargar datos de película:', movieError);
          // Crear datos por defecto para la película
          enhancedTicket.movie = {
            id: ticket.movie_id,
            titulo: 'Película no disponible',
            imagen: 'https://via.placeholder.com/300x450?text=No+Image'
          };
        }
      }
      
      // Intentar cargar datos de sesión desde la API
      let sessionLoaded = false;
      
      if (ticket.session_id) {
        try {
          const sessionData = await getSesiones(ticket.movie_id);
          
          if (sessionData && !sessionData.error && Array.isArray(sessionData)) {
            // Encontrar la sesión específica
            const session = sessionData.find(s => s.id === ticket.session_id);
            if (session) {
              enhancedTicket.session = session;
              sessionLoaded = true;
            }
          }
        } catch (sessionError) {
          console.warn('Error al cargar datos de sesión desde API:', sessionError);
        }
      }
      
      // Si no se cargó la sesión desde la API, intentar recuperarla desde localStorage
      if (!sessionLoaded) {
        const savedSession = getSavedSessionData(ticket);
        
        if (savedSession) {
          enhancedTicket.session = {
            id: ticket.session_id || savedSession.id,
            fecha: savedSession.fecha || ticket.fecha || new Date().toISOString().split('T')[0],
            hora: savedSession.hora || ticket.hora || '19:00',
            sala: savedSession.sala || ticket.sala || '1'
          };
        } else {
          // Crear una sesión por defecto si no hay datos
          enhancedTicket.session = {
            id: ticket.session_id,
            fecha: ticket.fecha || new Date().toISOString().split('T')[0],
            hora: ticket.hora || '19:00',
            sala: ticket.sala || '1'
          };
        }
      }
      
      // Asegurarse de que el asiento tiene información
      const seatData = getAsientoData(ticket);
      
      if (seatData) {
        enhancedTicket.seat = seatData;
      } else if (!enhancedTicket.seat || !enhancedTicket.seat.fila || !enhancedTicket.seat.numero) {
        enhancedTicket.seat = {
          fila: ticket.seat_row || 'A',
          numero: ticket.seat_number || '1'
        };
      }
      
      completeTickets.push(enhancedTicket);
    } catch (e) {
      console.error(`Error al cargar datos completos para el ticket ${ticket.id}:`, e);
      // Agregar el ticket con datos por defecto mínimos
      const defaultTicket = { 
        ...ticket,
        movie: ticket.movie || { 
          titulo: 'Película no disponible',
          imagen: 'https://via.placeholder.com/300x450?text=No+Image'
        },
        session: {
          fecha: ticket.fecha || new Date().toISOString().split('T')[0],
          hora: ticket.hora || '19:00',
          sala: ticket.sala || '1'
        },
        seat: {
          fila: ticket.seat_row || 'A',
          numero: ticket.seat_number || '1'
        }
      };
      completeTickets.push(defaultTicket);
    }
  }
  
  return completeTickets;
};

// Obtener el ID del usuario de manera segura
const getCurrentUserId = computed(() => {
  // Primero intentar obtener de la composable
  if (userId.value) return userId.value;
  
  // Luego intentar obtener de localStorage
  if (typeof window !== 'undefined') {
    const storedId = localStorage.getItem('userId');
    if (storedId) return parseInt(storedId, 10);
  }
  
  return null;
});

onMounted(async () => {
  try {
    loading.value = true;

    // Si el usuario no está autenticado, redirige al login
    if (!isAuthenticated.value) {
      router.push('/login?redirect=/billetesDeUsuario');
      return;
    }
    
    // Obtener el ID del usuario
    const currentUserId = getCurrentUserId.value;
    
    if (!currentUserId) {
      error.value = 'No se pudo determinar el ID del usuario actual';
      return;
    }

    console.log('Cargando tickets para el usuario:', currentUserId);
    
    // Intentar obtener los billetes del usuario
    let userTickets;
    try {
      userTickets = await getUserTickets(currentUserId);
      
      if (userTickets.error) {
        throw new Error(userTickets.error);
      }
    } catch (err) {
      console.warn('Error al obtener billetes del usuario, se usa el fallback a todos los billetes:', err);
      
      try {
        const allTickets = await getTickets();
        
        if (allTickets.error) {
          throw new Error(allTickets.error);
        }
        
        userTickets = allTickets.filter(ticket => ticket.user_id === currentUserId);
      } catch (fallbackErr) {
        throw new Error('No se pudieron cargar los tickets: ' + fallbackErr.message);
      }
    }
    
    if (!userTickets || !Array.isArray(userTickets) || userTickets.length === 0) {
      tickets.value = [];
      return;
    }

    // Cargar datos completos para cada ticket
    const enhancedTickets = await loadCompleteTicketData(userTickets);
    tickets.value = enhancedTickets;

  } catch (e) {
    error.value = 'Error al cargar tus tickets: ' + e.message;
    console.error('Error completo:', e);
  } finally {
    loading.value = false;
  }
});

// Formatear fecha para mostrar solo el día de la semana y la fecha
const formatDayAndDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Fecha no disponible';
    }
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    return date.toLocaleDateString('es-ES', options);
  } catch (e) {
    console.error('Error al formatear día y fecha:', e);
    return 'Fecha no disponible';
  }
}
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