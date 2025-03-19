<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br custom-bg py-8">
      <div class="container mx-auto px-4">
        <!-- Estado de carga -->
        <div v-if="loading" class="flex justify-center items-center h-[70vh]">
          <div class="animate-pulse-slow">
            <div class="w-16 h-16 border-4 border-gold border-t-transparent rounded-full animate-spin"></div>
          </div>
        </div>

        <!-- Muestra error en caso de fallo -->
        <div v-else-if="error" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl">
          <p class="text-center text-red-500">{{ error }}</p>
        </div>

        <!-- Contenido principal -->
        <div v-else class="max-w-6xl mx-auto mt-12">
          <!-- Tarjeta de información de la película y la sesión (ancho reducido a 800px) -->
          <div class="glass-effect rounded-xl overflow-hidden mb-8 max-w-[800px] mx-auto">
            <div class="flex flex-col md:flex-row">
              <!-- Cartel de la película -->
              <div class="md:w-1/3">
                <img :src="currentMovie?.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'"
                  :alt="currentMovie?.titulo" class="w-full h-full object-cover" />
              </div>

              <!-- Detalles de la sesión -->
              <div class="md:w-4/3 p-6 bg-primary/20">
                <h1 class="text-3xl font-bold text-gold mb-4">{{ currentMovie?.titulo }}</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-light">
                  <!-- Información de la película -->
                  <div class="space-y-2">
                    <div class="flex items-center space-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <p><span class="font-semibold">Duración:</span> {{ currentMovie?.duracion }} min</p>
                    </div>

                    <div class="flex items-center space-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.2-6.3 4.2 2.3-7.2-6-4.6h7.6z" />
                      </svg>
                      <p><span class="font-semibold">Calificación:</span> {{ currentMovie?.calificacion }}/10</p>
                    </div>
                  </div>

                  <!-- Información de la sesión -->
                  <div class="space-y-2">
                    <div class="flex items-center space-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p><span class="font-semibold">Fecha:</span> {{ formatDate(selectedSession?.fecha) }}</p>
                    </div>

                    <div class="flex items-center space-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <p><span class="font-semibold">Hora:</span> {{ selectedSession?.hora }}</p>
                    </div>

                    <div class="flex items-center space-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                      </svg>
                      <p><span class="font-semibold">Sala:</span> {{ selectedSession?.sala || 'Sala Principal' }}</p>
                    </div>

                    <div v-if="selectedSession?.dia_espectador" class="mt-2">
                      <span class="bg-blue-500/30 text-blue-300 text-sm font-medium px-4 py-2 rounded-full">
                        ¡Día del espectador!
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Selección de asientos -->
          <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-2xl font-semibold text-gold mb-4">Seleccionar Asientos</h2>

            <!-- Leyenda de estados -->
            <div class="flex flex-wrap gap-6 mb-6">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gray-500 rounded"></div>
                <span class="text-light">Disponible</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-red-500 rounded"></div>
                <span class="text-light">Ocupado</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-green-500 rounded"></div>
                <span class="text-light">Seleccionado</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gold rounded"></div>
                <span class="text-light">VIP</span>
              </div>
            </div>

            <!-- Pantalla de la sala -->
            <div class="w-full h-8 bg-gray-800 rounded-t-full mb-8 text-center text-sm text-light/50">
              PANTALLA
            </div>

            <!-- Cuadrícula de asientos -->
            <div class="grid grid-cols-10 gap-2 max-w-3xl mx-auto mb-8">
              <template v-for="seat in seats" :key="seat.id">
                <button @click="toggleSeat(seat)" :disabled="seat.estado === 'ocupada'"
                  class="w-8 h-8 rounded transition-all" :class="[
                    seat.estado === 'ocupada' ? 'bg-red-500' :
                      selectedSeats.includes(seat) ? 'bg-green-500' :
                        seat.tipo === 'vip' ? 'bg-gold' : 'bg-gray-500'
                  ]">
                  <span class="text-xs text-light">{{ seat.fila }}{{ seat.numero }}</span>
                </button>
              </template>
            </div>

            <!-- Resumen de precios -->
            <div class="p-6 rounded-xl">
              <h3 class="text-xl font-semibold text-gold mb-4">Resumen</h3>
              <div class="space-y-2 text-light">
                <p>Asientos normales: {{ normalSeatsCount }} x €{{ prices.normal }} = €{{ normalSeatsTotal }}</p>
                <p>Asientos VIP: {{ vipSeatsCount }} x €{{ prices.vip }} = €{{ vipSeatsTotal }}</p>
                <div class="border-t border-gray-900 pt-2 mt-4">
                  <p class="text-xl font-bold">Total: €{{ total }}</p>
                </div>
              </div>
            </div>

            <!-- Botón para comprar entradas -->
            <div class="mt-6 text-center">
              <button @click="purchaseTickets" :disabled="selectedSeats.length === 0"
                class="bg-gold text-dark px-8 py-3 rounded-full font-semibold transition-all"
                :class="selectedSeats.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gold/80'">
                Comprar Entradas
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePeliculas } from '~/composables/communicationManagerPelicula'
import { useSessionsStore } from '~/stores/sessionsStore'

// Configuración de rutas y navegación
const route = useRoute()
const router = useRouter()
const { getPeliculaById, getSesiones, getAsientos, getSessionTickets, createTicket } = usePeliculas()
const sessionsStore = useSessionsStore()

// Variables de estado
const loading = ref(true)
const error = ref(null)
const movie = ref(null)
const currentMovie = ref(null)
const sessions = ref([])
const selectedSession = ref(null)
const seats = ref([])
const selectedSeats = ref([])
const occupiedSeats = ref([])

// Precios de los asientos
const prices = ref({
  normal: 6,
  vip: 8
})

// Cálculos para los asientos y totales
const normalSeatsCount = computed(() =>
  selectedSeats.value.filter(seat => seat.tipo === 'normal').length
)

const vipSeatsCount = computed(() =>
  selectedSeats.value.filter(seat => seat.tipo === 'vip').length
)

const normalSeatsTotal = computed(() =>
  normalSeatsCount.value * prices.value.normal
)

const vipSeatsTotal = computed(() =>
  vipSeatsCount.value * prices.value.vip
)

const total = computed(() =>
  normalSeatsTotal.value + vipSeatsTotal.value
)

// Función para formatear fechas en español
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}

// Alterna la selección de un asiento
const toggleSeat = (seat) => {
  if (seat.estado === 'ocupada') return

  const index = selectedSeats.value.findIndex(s => s.id === seat.id)
  if (index === -1) {
    // Verifica si ya se ha alcanzado el límite de 10 asientos
    if (selectedSeats.value.length >= 10) {
      alert('No puedes seleccionar más de 10 asientos')
      return
    }
    selectedSeats.value.push(seat)
  } else {
    selectedSeats.value.splice(index, 1)
  }
}

// Procesa la compra de entradas
const purchaseTickets = async () => {
  if (!selectedSeats.value.length) return

  try {
    loading.value = true

    const ticketDetails = {
      movie: currentMovie.value,
      session: selectedSession.value,
      seats: selectedSeats.value,
      prices: prices.value,
      total: total.value,
      normalSeatsCount: normalSeatsCount.value,
      vipSeatsCount: vipSeatsCount.value
    }

    // Guardar en localStorage para acceder desde la página de tickets
    localStorage.setItem('ticketPurchaseDetails', JSON.stringify(ticketDetails))
    
    router.push('/tickets')
  } catch (err) {
    error.value = 'Error al procesar la compra'
    console.error('Error:', err)
  } finally {
    loading.value = false
  }
}

// Carga inicial de datos al montar el componente
onMounted(async () => {
  try {
    const sessionId = route.params.id

    // Primero, obtenemos la sesión desde el store
    await sessionsStore.fetchSessionById(sessionId)

    // Si la sesión existe en el store, la usamos
    if (sessionsStore.currentSession) {
      selectedSession.value = sessionsStore.currentSession

      // Obtenemos la película asociada a la sesión
      if (selectedSession.value.movie) {
        currentMovie.value = selectedSession.value.movie
      } else {
        // Si no viene la película, la solicitamos por su ID
        const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
        if (movieId) {
          const movieData = await getPeliculaById(movieId)
          currentMovie.value = movieData
        }
      }
    } else {
      // Si la sesión no está en el store, se obtiene por API
      const sessionsData = await getSesiones()
      sessions.value = sessionsData.sessions || []

      // Buscamos la sesión por su ID
      selectedSession.value = sessions.value.find(s => s.id.toString() === sessionId.toString())

      if (!selectedSession.value) {
        throw new Error('No se encontró la sesión solicitada')
      }

      // Obtenemos la película asociada
      const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
      if (movieId) {
        const movieData = await getPeliculaById(movieId)
        currentMovie.value = movieData
      } else if (selectedSession.value.movie) {
        currentMovie.value = selectedSession.value.movie
      }
    }

    // Ajusta los precios si es Día del Espectador
    if (selectedSession.value.dia_espectador) {
      prices.value = {
        normal: 4,
        vip: 6
      }
    }

    // Carga los asientos de la sesión
    const seatsData = await getAsientos(sessionId)
    
    // Intentamos obtener los tickets de la sesión, si falla asumimos que no hay tickets
    let occupiedSeatIds = []
    try {
      const sessionTickets = await getSessionTickets(sessionId)
      if (sessionTickets && Array.isArray(sessionTickets)) {
        occupiedSeatIds = sessionTickets.map(ticket => ticket.seat_id)
      }
    } catch (err) {
      console.warn('No se pudieron cargar los tickets de la sesión:', err)
    }

    // Marca los asientos ocupados
    seats.value = (seatsData.seats || []).map(seat => ({
      ...seat,
      estado: occupiedSeatIds.includes(seat.id) ? 'ocupada' : 'disponible'
    }))

    console.log('Sesión cargada:', selectedSession.value)
    console.log('Película cargada:', currentMovie.value)
  } catch (err) {
    console.error('Error:', err)
    error.value = 'Error al cargar los datos de la sesión'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 110%);
}

.glass-effect {
  background: #4A4E69;
  backdrop-filter: blur(10px);
}

.animate-pulse-slow {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
