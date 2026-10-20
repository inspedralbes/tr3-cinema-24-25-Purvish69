<template>
  <v-app class="cinema-app-root bg-[#0c0e1a]">
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
          <!-- Tarjeta de información de la película y la sesión -->
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
              <h2 class="text-2xl font-semibold text-gold">Seleccionar Asientos</h2>
              <span v-if="selectedSeats.length > 0" class="text-xs text-light/80 mt-1 sm:mt-0 flex items-center gap-1">
                <i class="mdi mdi-timer-sand text-gold"></i>
                Bloqueo temporal activo (5 min)
              </span>
            </div>

            <!-- Leyenda de estados -->
            <div class="flex flex-wrap gap-4 sm:gap-6 mb-6 text-sm">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gray-500 rounded"></div>
                <span class="text-light">Disponible</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gold rounded"></div>
                <span class="text-light">VIP</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-green-500 rounded"></div>
                <span class="text-light">Seleccionado</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-orange-500 rounded"></div>
                <span class="text-light">En proceso de reserva</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gray-600 opacity-60 rounded"></div>
                <span class="text-light">Comprado</span>
              </div>
            </div>

            <!-- Pantalla de la sala -->
            <div class="w-full h-8 bg-gray-800 rounded-t-full mb-12 text-center text-sm text-light/50 flex items-center justify-center font-bold tracking-widest">
              PANTALLA
            </div>

            <!-- Cuadrícula de asientos con filas verticales y números horizontales -->
            <div class="flex justify-center mb-12 overflow-x-auto pb-4">
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
                      <!-- Botón de asiento con Ícono que pinta de acuerdo al estado -->
                      <div v-if="getSeat(rowLetter, seatNumber)" class="mx-2">
                        <button
                          type="button"
                          @click="toggleSeat(getSeat(rowLetter, seatNumber))"
                          :disabled="isSeatBought(getSeat(rowLetter, seatNumber)) || isSeatHeldByOther(getSeat(rowLetter, seatNumber))"
                          class="w-10 h-10 relative rounded transition-all flex items-center justify-center"
                          :class="[
                            // 1. Asiento comprado: gris bloqueado
                            isSeatBought(getSeat(rowLetter, seatNumber))
                              ? 'bg-gray-600 opacity-60 cursor-not-allowed'
                              // 2. Retenido por otro usuario: naranja y no clicable
                              : isSeatHeldByOther(getSeat(rowLetter, seatNumber))
                                ? 'bg-orange-500 cursor-not-allowed'
                                // 3. Seleccionado por el usuario actual: verde
                                : isSeatSelected(getSeat(rowLetter, seatNumber))
                                  ? 'bg-green-500 scale-105 shadow-md'
                                  // 4. Disponible VIP o Normal
                                  : getSeat(rowLetter, seatNumber).tipo === 'vip'
                                    ? 'bg-gold hover:bg-gold/80'
                                    : 'bg-gray-500 hover:bg-gray-400'
                          ]"
                          :title="
                            isSeatBought(getSeat(rowLetter, seatNumber))
                              ? 'Asiento ocupado / comprado'
                              : isSeatHeldByOther(getSeat(rowLetter, seatNumber))
                                ? 'Asiento en proceso de reserva por otro usuario'
                                : isSeatSelected(getSeat(rowLetter, seatNumber))
                                  ? 'Tu asiento seleccionado (retenido)'
                                  : `Asiento ${rowLetter}-${seatNumber} (${getSeat(rowLetter, seatNumber).tipo})`
                          "
                        >
                          <!-- Ícono de asiento -->
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
            <div class="p-6 rounded-xl bg-primary/20">
              <h3 class="text-xl font-semibold text-gold mb-4">Resumen</h3>
              <div class="space-y-2 text-light">
                <p>Asientos seleccionados: <span class="font-bold text-gold">{{ selectedSeatsLabels }}</span></p>
                <p>Asientos normales: {{ normalSeatsCount }} x €{{ prices.normal }} = €{{ normalSeatsTotal }}</p>
                <p>Asientos VIP: {{ vipSeatsCount }} x €{{ prices.vip }} = €{{ vipSeatsTotal }}</p>
                <div class="border-t border-gray-700 pt-2 mt-4">
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

    <!-- Notificación / Snackbar para avisos de reserva temporal -->
    <v-snackbar
      v-model="snackbar"
      :color="snackbarColor"
      timeout="4000"
      location="top"
      elevation="24"
      rounded="pill"
    >
      <div class="flex items-center gap-2 font-medium">
        <i class="mdi mdi-alert-circle-outline text-lg"></i>
        <span>{{ snackbarText }}</span>
      </div>
      <template v-slot:actions>
        <v-btn variant="text" @click="snackbar = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePeliculas } from '~/composables/communicationManagerPelicula'
import { useSessionsStore } from '~/stores/sessionsStore'
import SeatIcon from '~/components/SeatIcon.vue'

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const API_URL = config.public.apiBaseUrl
const { getPeliculaById, getSesiones, getAsientos } = usePeliculas()
const sessionsStore = useSessionsStore()

const loading = ref(true)
const error = ref(null)
const currentMovie = ref(null)
const sessions = ref([])
const selectedSession = ref(null)
const seats = ref([]) // guarda todos los asientos de la sala
const selectedSeats = ref([]) // guarda los asientos seleccionados por el usuario

// Asientos comprados y retenidos por otros usuarios
const boughtSeats = ref([]) // array de códigos, ej: ["A-1", "A-2"]
const heldByOtherSeats = ref([]) // array de códigos retenidos por otros
const myHoldSeats = ref([]) // array de códigos retenidos por este hold_token
let pollingInterval = null

// Snackbar reactivo para notificaciones al usuario
const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('warning')

const showNotice = (text, color = 'warning') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

// 1. Genera un hold_token (UUID) y guárdalo en sessionStorage
const getHoldToken = () => {
  if (typeof window === 'undefined') return ''
  let token = sessionStorage.getItem('hold_token')
  if (!token) {
    token = typeof crypto !== 'undefined' && crypto.randomUUID
      ? crypto.randomUUID()
      : 'ht_' + Math.random().toString(36).substring(2) + Date.now().toString(36)
    sessionStorage.setItem('hold_token', token)
  }
  return token
}

const prices = ref({
  normal: 6,
  vip: 8
})

// Código representativo del asiento ej: "A-1"
const getSeatCode = (seat) => {
  return seat ? `${seat.fila}-${seat.numero}` : ''
}

const isSeatBought = (seat) => {
  if (!seat) return false
  const code = getSeatCode(seat)
  return boughtSeats.value.includes(code) || seat.estado === 'ocupada'
}

const isSeatHeldByOther = (seat) => {
  if (!seat) return false
  const code = getSeatCode(seat)
  return heldByOtherSeats.value.includes(code)
}

const isSeatSelected = (seat) => {
  if (!seat) return false
  return selectedSeats.value.some(s => s.id === seat.id)
}

// Obtener filas únicas (A, B, C, D,...)
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

const selectedSeatsLabels = computed(() => {
  if (!selectedSeats.value.length) return 'Ninguno'
  return selectedSeats.value.map(s => `${s.fila}${s.numero}`).join(', ')
})

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}

// 2. Al seleccionar un asiento, llama a POST /holds. Si responde 409, no lo selecciona
// y muestra: "Este asiento está en proceso de reserva por otro usuario". Al deseleccionar, DELETE /holds.
const toggleSeat = async (seat) => {
  if (!seat) return
  if (!selectedSession.value?.id) return

  // Si ya está comprado, no hacer nada
  if (isSeatBought(seat)) return

  // Si está retenido por otro usuario, avisar y no hacer nada
  if (isSeatHeldByOther(seat)) {
    showNotice('Este asiento está en proceso de reserva por otro usuario', 'orange-darken-2')
    return
  }

  const seatCode = getSeatCode(seat)
  const isSelected = isSeatSelected(seat)

  if (isSelected) {
    // DESELECCIONAR: Quitar de la selección y llamar a DELETE /holds
    const index = selectedSeats.value.findIndex(s => s.id === seat.id)
    if (index !== -1) {
      selectedSeats.value.splice(index, 1)
    }

    try {
      const token = getHoldToken()
      await fetch(`${API_URL}/holds`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          funcion_id: selectedSession.value.id,
          asientos: [seatCode],
          hold_token: token
        })
      })
    } catch (err) {
      console.warn('Error al liberar hold:', err)
    }
  } else {
    // SELECCIONAR: Validar límite y solicitar bloqueo temporal con POST /holds
    if (selectedSeats.value.length >= 10) {
      showNotice('No puedes seleccionar más de 10 asientos', 'warning')
      return
    }

    try {
      const token = getHoldToken()
      const response = await fetch(`${API_URL}/holds`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          funcion_id: selectedSession.value.id,
          asientos: [seatCode],
          hold_token: token
        })
      })

      // Si responde 409, conflicto con otro usuario
      if (response.status === 409) {
        showNotice('Este asiento está en proceso de reserva por otro usuario', 'orange-darken-2')
        if (!heldByOtherSeats.value.includes(seatCode)) {
          heldByOtherSeats.value.push(seatCode)
        }
        return
      }

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        showNotice(data.message || 'Error al bloquear el asiento', 'error')
        return
      }

      // Si fue exitoso, seleccionarlo
      selectedSeats.value.push(seat)
      if (!myHoldSeats.value.includes(seatCode)) {
        myHoldSeats.value.push(seatCode)
      }
    } catch (err) {
      console.error('Error al retener asiento:', err)
      showNotice('Error de conexión al retener el asiento', 'error')
    }
  }
}

// 3. Polling cada 4 s a GET /asientos (limpia el intervalo en onUnmounted).
// 4. Si el usuario ve que sus asientos expiran, avisa y los deselecciona.
const fetchAsientosStatus = async () => {
  if (!selectedSession.value?.id) return

  try {
    const token = getHoldToken()
    const response = await fetch(`${API_URL}/funciones/${selectedSession.value.id}/asientos?hold_token=${token}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })

    if (!response.ok) return

    const data = await response.json()
    boughtSeats.value = data.comprados || []
    heldByOtherSeats.value = data.retenidos || []
    myHoldSeats.value = data.mis_holds || []

    // 4. Si alguno de los asientos seleccionados por el usuario expira o pasa a otro estado, deseleccionarlo
    if (selectedSeats.value.length > 0) {
      const remaining = []
      const expiredSeats = []

      selectedSeats.value.forEach(seat => {
        const code = getSeatCode(seat)
        const isBought = boughtSeats.value.includes(code)
        const isHeldByOtherUser = heldByOtherSeats.value.includes(code)
        const isStillHeldByMe = myHoldSeats.value.includes(code)

        if (isBought || isHeldByOtherUser || (!isStillHeldByMe && myHoldSeats.value.length >= 0)) {
          expiredSeats.push(code)
        } else {
          remaining.push(seat)
        }
      })

      if (expiredSeats.length > 0) {
        selectedSeats.value = remaining
        showNotice(`El tiempo de reserva de tus asientos (${expiredSeats.join(', ')}) ha expirado y se han deseleccionado.`, 'warning')
      }
    }
  } catch (err) {
    console.warn('Error polling asientos:', err)
  }
}

const purchaseTickets = async () => {
  if (!selectedSeats.value.length) return

  try {
    loading.value = true
    const token = getHoldToken()

    const ticketDetails = {
      movie: currentMovie.value,
      session: selectedSession.value,
      seats: selectedSeats.value,
      prices: prices.value,
      total: total.value,
      normalSeatsCount: normalSeatsCount.value,
      vipSeatsCount: vipSeatsCount.value,
      hold_token: token
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
    getHoldToken()
    const sessionId = route.params.id?.toString()

    const sessionResponse = await sessionsStore.fetchSessionById(sessionId)

    if (sessionResponse?.session || sessionsStore.currentSession) {
      selectedSession.value = sessionsStore.currentSession

      if (selectedSession.value.movie) {
        currentMovie.value = selectedSession.value.movie
      } else {
        const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
        if (movieId) {
          const movieData = await getPeliculaById(movieId)
          currentMovie.value = movieData.movie || movieData
        }
      }
    } else {
      const sessionsData = await getSesiones()
      sessions.value = sessionsData.sessions || []
      selectedSession.value = sessions.value.find(s => s.id?.toString() === sessionId)

      if (!selectedSession.value) {
        error.value = 'La sesión ya no existe o ha cambiado. Selecciona una sesión disponible.'
        await router.replace('/moviSession')
        return
      }

      const movieId = selectedSession.value.movie_id || selectedSession.value.movieId
      if (movieId) {
        const movieData = await getPeliculaById(movieId)
        currentMovie.value = movieData.movie || movieData
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
    seats.value = seatsData.seats || []

    // Consulta inicial de asientos comprados y retenidos
    await fetchAsientosStatus()

    // Iniciar polling cada 4 segundos
    pollingInterval = setInterval(fetchAsientosStatus, 4000)
  } catch (err) {
    console.error('Error:', err)
    error.value = 'Error al cargar los datos de la sesión'
  } finally {
    loading.value = false
  }
})

// Limpiar el intervalo en onUnmounted
onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
    pollingInterval = null
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
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
