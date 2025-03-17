<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary to-secondary py-8">
      <div class="container mx-auto px-4">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-[70vh]">
          <div class="animate-pulse-slow">
            <div class="w-16 h-16 border-4 border-gold border-t-transparent rounded-full animate-spin"></div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl">
          <p class="text-center text-red-500">{{ error }}</p>
        </div>

        <!-- Main Content -->
        <div v-else class="space-y-8">
          <!-- Movie Info -->
          <div class="glass-effect p-6 rounded-xl">
            <h1 class="text-3xl font-bold text-gold mb-4">{{ movie?.titulo }}</h1>
            <div class="flex flex-wrap gap-4 text-light">
              <p><span class="font-semibold">Duración:</span> {{ movie?.duracion }} min</p>
              <p><span class="font-semibold">Clasificación:</span> {{ movie?.clasificacion }}</p>
            </div>
          </div>

          <!-- Session Selection -->
          <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-2xl font-semibold text-gold mb-4">Seleccionar Sesión</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="session in sessions" :key="session.id" 
                   class="p-4 rounded-lg border-2 transition-all cursor-pointer"
                   :class="[ selectedSession?.id === session.id 
                             ? 'border-gold bg-secondary/50' 
                             : 'border-gray-600 hover:border-gold/50' ]"
                   @click="selectSession(session)">
                <p class="text-gold font-semibold">{{ formatDate(session.fecha) }}</p>
                <p class="text-light">{{ session.hora }}</p>
                <p class="text-sm text-light/80" v-if="session.dia_espectador">¡Día del espectador!</p>
              </div>
            </div>
          </div>

          <!-- Seat Selection -->
          <div v-if="selectedSession" class="glass-effect p-6 rounded-xl">
            <h2 class="text-2xl font-semibold text-gold mb-4">Seleccionar Asientos</h2>
            
            <!-- Legend -->
            <div class="flex gap-6 mb-6">
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
                <div class="w-6 h-6 bg-purple-500 rounded"></div>
                <span class="text-light">VIP</span>
              </div>
            </div>

            <!-- Screen -->
            <div class="w-full h-8 bg-gray-800 rounded-t-full mb-8 text-center text-sm text-light/50">
              PANTALLA
            </div>

            <!-- Seats Grid -->
            <div class="grid grid-cols-10 gap-2 max-w-4xl mx-auto mb-8">
              <template v-for="seat in seats" :key="seat.id">
                <button 
                  @click="toggleSeat(seat)"
                  :disabled="seat.estado === 'ocupada'"
                  class="w-8 h-8 rounded transition-all"
                  :class="[
                    seat.estado === 'ocupada' ? 'bg-red-500' :
                    selectedSeats.includes(seat) ? 'bg-green-500' :
                    seat.tipo === 'vip' ? 'bg-purple-500' : 'bg-gray-500'
                  ]"
                >
                  <span class="text-xs text-light">{{ seat.fila }}{{ seat.numero }}</span>
                </button>
              </template>
            </div>

            <!-- Price Summary -->
            <div class="bg-secondary/30 p-6 rounded-xl">
              <h3 class="text-xl font-semibold text-gold mb-4">Resumen</h3>
              <div class="space-y-2 text-light">
                <p>Asientos normales: {{ normalSeatsCount }} x €{{ prices.normal }} = €{{ normalSeatsTotal }}</p>
                <p>Asientos VIP: {{ vipSeatsCount }} x €{{ prices.vip }} = €{{ vipSeatsTotal }}</p>
                <div class="border-t border-light/20 pt-2 mt-4">
                  <p class="text-xl font-bold">Total: €{{ total }}</p>
                </div>
              </div>
            </div>

            <!-- Purchase Button -->
            <div class="mt-6 text-center">
              <button 
                @click="purchaseTickets"
                :disabled="selectedSeats.length === 0"
                class="bg-gold text-dark px-8 py-3 rounded-full font-semibold transition-all"
                :class="selectedSeats.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gold/80'"
              >
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

const route = useRoute()
const router = useRouter()
const { getPeliculaById, getSesiones, getAsientos, createTicket } = usePeliculas()

// State
const loading = ref(true)
const error = ref(null)
const movie = ref(null)
const sessions = ref([])
const seats = ref([])
const selectedSession = ref(null)
const selectedSeats = ref([])
const prices = ref({
  normal: 6,
  vip: 8
})

// Computed
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

// Methods
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const selectSession = async (session) => {
  selectedSession.value = session
  selectedSeats.value = []
  loading.value = true
  
  try {
    const response = await getAsientos(session.id)
    seats.value = response.seats
    
    // Update prices based on día del espectador
    if (session.dia_espectador) {
      prices.value = {
        normal: 4,
        vip: 6
      }
    } else {
      prices.value = {
        normal: 6,
        vip: 8
      }
    }
  } catch (err) {
    error.value = 'Error al cargar los asientos'
  } finally {
    loading.value = false
  }
}

const toggleSeat = (seat) => {
  if (seat.estado === 'ocupada') return
  
  const index = selectedSeats.value.findIndex(s => s.id === seat.id)
  if (index === -1) {
    if (selectedSeats.value.length < 10) {
      selectedSeats.value.push(seat)
    }
  } else {
    selectedSeats.value.splice(index, 1)
  }
}

const purchaseTickets = async () => {
  if (!selectedSeats.value.length) return
  
  loading.value = true
  try {
    // Asumimos que el usuario está autenticado y tenemos su ID
    const userId = 1 // Reemplazar con el ID real del usuario autenticado
    
    // Crear tickets para cada asiento seleccionado
    const ticketPromises = selectedSeats.value.map(seat => 
      createTicket({
        user_id: userId,
        movieSession_id: selectedSession.value.id,
        seat_id: seat.id
      })
    )
    
    await Promise.all(ticketPromises)
    
    // Redirigir a la página de éxito o listado de tickets
    router.push('/tickets')
  } catch (err) {
    error.value = 'Error al procesar la compra'
  } finally {
    loading.value = false
  }
}

// Carga inicial de datos
onMounted(async () => {
  try {
    const movieId = route.params.id
    const [movieData, sessionsData] = await Promise.all([
      getPeliculaById(movieId),
      getSesiones(movieId)
    ])
    
    movie.value = movieData
    sessions.value = sessionsData.sessions
  } catch (err) {
    error.value = 'Error al cargar los datos de la película'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.glass-effect {
  background: rgba(255, 255, 255, 0.1);
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
