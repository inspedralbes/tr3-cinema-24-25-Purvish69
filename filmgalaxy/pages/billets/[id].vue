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
            <div class="w-full h-8 bg-gray-800 rounded-t-full mb-12 text-center text-sm text-light/50">
              PANTALLA
            </div>

            <!-- Cuadrícula de asientos con filas verticales y números horizontales -->
            <div class="flex justify-center mb-12">
              <!-- Etiquetas de filas (A,B,C...) en el lado izquierdo -->
              <div class="flex flex-col mr-6">
                <div class="h-8 flex items-center justify-center"></div> <!-- Espacio vacío para alinear -->
                <template v-for="rowLetter in uniqueRows" :key="rowLetter">
                  <div class="h-10 w-8 flex items-center justify-center text-light font-bold mb-5">
                    {{ rowLetter }}
                  </div>
                </template>
              </div>

              <div>
                <!-- Números de asiento en la parte superior -->
                <div class="flex mb-4">
                  <div v-for="seatNumber in uniqueNumbers" :key="seatNumber" 
                       class="w-10 h-8 flex items-center justify-center text-light font-bold mx-2">
                    {{ seatNumber }}
                  </div>
                </div>

                <!-- Cuadrícula de asientos organizada por filas -->
                <div>
                  <div v-for="rowLetter in uniqueRows" :key="rowLetter" class="flex mb-5">
                    <template v-for="seatNumber in uniqueNumbers" :key="`${rowLetter}-${seatNumber}`">
                      <div v-if="getSeat(rowLetter, seatNumber)" class="mx-2">
                        <button 
                          @click="toggleSeat(getSeat(rowLetter, seatNumber))" 
                          :disabled="getSeat(rowLetter, seatNumber).estado === 'ocupada'"
                          class="w-10 h-10 relative rounded transition-all flex items-center justify-center"
                          :class="[
                            getSeat(rowLetter, seatNumber).estado === 'ocupada' ? 'bg-red-500' :
                              selectedSeats.includes(getSeat(rowLetter, seatNumber)) ? 'bg-green-500' :
                                getSeat(rowLetter, seatNumber).tipo === 'vip' ? 'bg-gold' : 'bg-gray-500'
                          ]">
                          <!-- Ícono de asiento más pequeño -->
                          <SeatIcon class="absolute inset-0 scale-75" />
                        </button>
                      </div>
                      <div v-else class="w-10 h-10 mx-2"></div> <!-- Espacio vacío si no hay asiento -->
                    </template>
                  </div>
                </div>
              </div>
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
import SeatIcon from '~/components/SeatIcon.vue'

const route = useRoute()
const router = useRouter()
const { getPeliculaById, getSesiones, getAsientos, getSessionTickets, createTicket } = usePeliculas()
const sessionsStore = useSessionsStore()

const loading = ref(true)
const error = ref(null)
const movie = ref(null)
const currentMovie = ref(null)
const sessions = ref([])
const selectedSession = ref(null)
const seats = ref([])
const selectedSeats = ref([])
const occupiedSeats = ref([])

const prices = ref({
  normal: 6,
  vip: 8
})

// Obtener filas únicas (A, B, C, etc.)
const uniqueRows = computed(() => {
  const rows = [...new Set(seats.value.map(seat => seat.fila))].sort()
  return rows
})

// Obtener números de asiento únicos (1, 2, 3, etc.)
const uniqueNumbers = computed(() => {
  const numbers = [...new Set(seats.value.map(seat => seat.numero))].sort((a, b) => a - b)
  return numbers
})

// Función para obtener un asiento específico por fila y número
const getSeat = (row, number) => {
  return seats.value.find(seat => seat.fila === row && seat.numero === number)
}

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

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}

const toggleSeat = (seat) => {
  if (seat.estado === 'ocupada') return

  const index = selectedSeats.value.findIndex(s => s.id === seat.id)
  if (index === -1) {
    if (selectedSeats.value.length >= 10) {
      alert('No puedes seleccionar más de 10 asientos')
      return
    }
    selectedSeats.value.push(seat)
  } else {
    selectedSeats.value.splice(index, 1)
  }
}

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

    localStorage.setItem('ticketPurchaseDetails', JSON.stringify(ticketDetails))
    router.push('/tickets')
  } catch (err) {
    error.value = 'Error al procesar la compra'
    console.error('Error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const sessionId = route.params.id

    await sessionsStore.fetchSessionById(sessionId)

    if (sessionsStore.currentSession) {
      selectedSession.value = sessionsStore.currentSession

      if (selectedSession.value.movie) {
        currentMovie.value = selectedSession.value.movie
      } else {
        const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
        if (movieId) {
          const movieData = await getPeliculaById(movieId)
          currentMovie.value = movieData
        }
      }
    } else {
      const sessionsData = await getSesiones()
      sessions.value = sessionsData.sessions || []
      selectedSession.value = sessions.value.find(s => s.id.toString() === sessionId.toString())

      if (!selectedSession.value) {
        throw new Error('No se encontró la sesión solicitada')
      }

      const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
      if (movieId) {
        const movieData = await getPeliculaById(movieId)
        currentMovie.value = movieData
      } else if (selectedSession.value.movie) {
        currentMovie.value = selectedSession.value.movie
      }
    }

    if (selectedSession.value.dia_espectador) {
      prices.value = {
        normal: 4,
        vip: 6
      }
    }

    const seatsData = await getAsientos(sessionId)
    
    let occupiedSeatIds = []
    try {
      const sessionTickets = await getSessionTickets(sessionId)
      if (sessionTickets && Array.isArray(sessionTickets)) {
        occupiedSeatIds = sessionTickets.map(ticket => ticket.seat_id)
      }
    } catch (err) {
      console.warn('No se pudieron cargar los tickets de la sesión:', err)
    }

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