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
        <div v-else-if="error" class="flex justify-center items-center min-h-screen">
          <div class="max-w-md glass-effect text-light p-8 rounded-xl">
            <p class="text-center text-red-500">{{ error }}</p>
            <div class="mt-4 text-center">
              <button @click="goBack" class="bg-gold text-dark px-6 py-2 rounded-full">
                Volver atrás
              </button>
            </div>
          </div>
        </div>

        <!-- Confirmación de pago exitoso -->
        <div v-else-if="paymentSuccess" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl">
          <div class="text-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mx-auto" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="text-2xl font-bold text-gold mt-4">¡Pago realizado con éxito!</h2>
            <p class="mt-2">Las entradas han sido añadidas a tu cuenta.</p>
          </div>
          <div class="flex flex-col space-y-4">
            <button @click="viewTickets"
              class="bg-gold text-dark px-6 py-2 rounded-full font-semibold hover:bg-gold/80">
              Ver mis entradas
            </button>
            <button @click="goHome"
              class="bg-transparent border border-gold text-gold px-6 py-2 rounded-full font-semibold hover:bg-gold/10">
              Volver al inicio
            </button>
          </div>
        </div>

        <!-- Formulario de pago -->
        <div v-else class="max-w-4xl mx-auto mt-16">
          <h1 class="text-6xl font-bold text-gold mb-8 text-center">Finalizar Compra</h1>

          <div class="flex flex-col md:flex-row gap-8">
            <!-- Resumen de la compra -->
            <div class="md:w-1/3">
              <div class="glass-effect rounded-xl p-6 mb-6">
                <h2 class="text-xl font-semibold text-gold mb-4">Resumen de compra</h2>

                <div v-if="ticketDetails">
                  <div class="mb-4">
                    <p class="text-light font-semibold">{{ ticketDetails.movie?.titulo }}</p>
                    <p class="text-light/70 text-sm">{{ formatDate(ticketDetails.session?.fecha) }} - {{
                      ticketDetails.session?.hora }}</p>
                    <p class="text-light/70 text-sm">Sala: {{ ticketDetails.session?.sala || 'Principal' }}</p>
                  </div>

                  <div class="space-y-2 text-light border-t border-gray-700 pt-3 mt-3">
                    <p>Asientos: {{ formatSeats(ticketDetails.seats) }}</p>
                    <p>Asientos normales: {{ ticketDetails.normalSeatsCount }} x €{{ ticketDetails.prices.normal }}</p>
                    <p>Asientos VIP: {{ ticketDetails.vipSeatsCount }} x €{{ ticketDetails.prices.vip }}</p>
                  </div>

                  <div class="border-t border-gray-700 pt-3 mt-3">
                    <p class="text-xl font-bold text-gold">Total: €{{ ticketDetails.total }}</p>
                  </div>
                </div>
                <div v-else class="text-light">
                  No hay información de compra disponible.
                </div>
              </div>
            </div>

            <!-- Formulario de pago -->
            <div class="md:w-2/3">
              <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gold mb-6">Información de pago</h2>

                <form @submit.prevent="processPayment" class="space-y-6">
                  <!-- Método de pago -->
                  <div class="space-y-2">
                    <label class="block text-light text-sm font-medium">Método de pago</label>
                    <div class="flex flex-wrap gap-4">
                      <label class="relative flex items-center">
                        <input type="radio" v-model="paymentMethod" value="card" class="hidden peer" checked />
                        <div
                          class="w-full p-3 rounded-lg border-2 border-gray-600 peer-checked:border-gold flex items-center gap-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-light" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                          </svg>
                          <span class="text-light">Tarjeta</span>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Formulario de tarjeta de crédito -->
                  <div v-if="paymentMethod === 'card'" class="space-y-4">
                    <div class="space-y-2">
                      <label for="cardHolder" class="block text-light text-sm font-medium">Titular de la tarjeta</label>
                      <input id="cardHolder" v-model="cardInfo.holder" type="text"
                        :class="['w-full p-3 bg-gray-700 text-light rounded-lg focus:outline-none', cardHolderError ? 'border-red-500' : 'border border-gray-600 focus:border-gold']"
                        placeholder="Nombre del titular" required />
                    </div>

                    <div class="space-y-2">
                      <label for="cardNumber" class="block text-light text-sm font-medium">Número de tarjeta</label>
                      <input id="cardNumber" v-model="cardInfo.number" type="text"
                        :class="['w-full p-3 bg-gray-700 text-light rounded-lg focus:outline-none', cardNumberError ? 'border-red-500' : 'border border-gray-600 focus:border-gold']"
                        placeholder="1234 5678 9012 3456" maxlength="19" @input="formatCardNumber" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label for="expiryDate" class="block text-light text-sm font-medium">Fecha de caducidad</label>
                        <input id="expiryDate" v-model="cardInfo.expiry" type="text"
                          :class="['w-full p-3 bg-gray-700 text-light rounded-lg focus:outline-none', cardExpiryError ? 'border-red-500' : 'border border-gray-600 focus:border-gold']"
                          placeholder="MM/AA" maxlength="5" @input="formatCardExpiry" required />
                      </div>

                      <div class="space-y-2">
                        <label for="cvv" class="block text-light text-sm font-medium">CVV</label>
                        <input id="cvv" v-model="cardInfo.cvv" type="text"
                          :class="['w-full p-3 bg-gray-700 text-light rounded-lg focus:outline-none', cardCvvError ? 'border-red-500' : 'border border-gray-600 focus:border-gold']"
                          placeholder="123" maxlength="3" required />
                      </div>
                    </div>
                  </div>

                  <!-- Botón de pago -->
                  <div class="text-center mt-8">
                    <button type="submit" class="bg-gold text-dark px-8 py-3 rounded-full font-semibold transition-all"
                      :class="!isFormValid ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gold/80'"
                      :disabled="!isFormValid">
                      Pagar
                    </button>
                  </div>
                </form>
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePeliculas } from '~/composables/communicationManagerPelicula'
import { useAuth } from '~/composables/useAuth'

// Router y navegación
const route = useRoute()
const router = useRouter()
const { createTicket, getPeliculaById, createPayment } = usePeliculas()
const { userId, isAuthenticated, token } = useAuth()

// Estado de la página
const loading = ref(true)
const error = ref(null)
const paymentSuccess = ref(false)
const ticketDetails = ref(null)

// Formulario de pago
const paymentMethod = ref('card')
const cardInfo = ref({
  holder: '',
  number: '',
  expiry: '',
  cvv: ''
})

// Validación individual de campos
const cardHolderError = computed(() => {
  return paymentMethod.value === 'card' && cardInfo.value.holder.trim() === ''
})
const cardNumberError = computed(() => {
  return paymentMethod.value === 'card' && cardInfo.value.number.replace(/\s/g, '').length !== 16
})
const cardExpiryError = computed(() => {
  const regex = /^\d{2}\/\d{2}$/
  return paymentMethod.value === 'card' && !regex.test(cardInfo.value.expiry)
})
const cardCvvError = computed(() => {
  return paymentMethod.value === 'card' && cardInfo.value.cvv.length !== 3
})

// Validación general del formulario
const isFormValid = computed(() => {
  return cardInfo.value.holder.trim() !== '' &&
    cardInfo.value.number.replace(/\s/g, '').length === 16 &&
    /^\d{2}\/\d{2}$/.test(cardInfo.value.expiry) &&
    cardInfo.value.cvv.length === 3
})

// Formatear número de tarjeta con espacios
const formatCardNumber = () => {
  let value = cardInfo.value.number.replace(/\s/g, '')
  if (value.length > 16) {
    value = value.slice(0, 16)
  }
  const parts = []
  for (let i = 0; i < value.length; i += 4) {
    parts.push(value.slice(i, i + 4))
  }
  cardInfo.value.number = parts.join(' ')
}

// Formatear fecha de caducidad
const formatCardExpiry = () => {
  let value = cardInfo.value.expiry.replace(/\D/g, '')
  if (value.length > 4) {
    value = value.slice(0, 4)
  }
  if (value.length > 2) {
    cardInfo.value.expiry = `${value.slice(0, 2)}/${value.slice(2)}`
  } else {
    cardInfo.value.expiry = value
  }
}

// Función para formatear la lista de asientos
const formatSeats = (seats) => {
  if (!seats || !seats.length) return '-'
  return seats.map(seat => `${seat.fila}${seat.numero}`).join(', ')
}

// Función para formatear fechas en español
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Procesar el pago
const processPayment = async () => {
  if (!isFormValid.value || !ticketDetails.value) return

  loading.value = true
  try {
    // Verificar la autenticación antes de proceder
    if (!isAuthenticated.value) {
      router.push('/login?redirect=' + encodeURIComponent(route.fullPath))
      error.value = 'Necesitas iniciar sesión para completar la compra'
      return
    }

    const currentUserId = userId.value || 1

    // Crear registro de pago
    const paymentData = {
      user_id: currentUserId,
      amount: ticketDetails.value.total,
      payment_method: paymentMethod.value,
      status: 'completed'
    }

    console.log('Creating payment with data:', paymentData)

    const paymentResponse = await createPayment(paymentData)

    console.log('Payment response:', paymentResponse)

    // Verificar si hay error en la respuesta
    if (paymentResponse.error) {
      // Si es un error de autenticación, redirigir al login
      if (paymentResponse.error.includes('Unauthenticated')) {
        router.push('/login?redirect=' + encodeURIComponent(route.fullPath))
        throw new Error('Sesión expirada. Por favor, inicia sesión nuevamente')
      } else {
        throw new Error(paymentResponse.error)
      }
    }

    // Buscar el ID de pago en distintas propiedades, ej. id o insertId
    const paymentId = paymentResponse.id || paymentResponse.insertId || paymentResponse.data?.id;
    if (!paymentId) {
      throw new Error('Error al crear el pago: no se recibió ID de pago')
    }

    // Crear entradas usando el ID de pago obtenido
    const ticketPromises = ticketDetails.value.seats.map(async (seat) => {
      const ticketData = {
        user_id: currentUserId,
        movieSession_id: ticketDetails.value.session.id,
        seat_id: seat.id,
        payment_id: paymentId,
        precio: seat.tipo === 'vip' ? ticketDetails.value.prices.vip : ticketDetails.value.prices.normal
      }

      console.log('Creating ticket with data:', ticketData)

      const ticketResponse = await createTicket(ticketData)
      if (ticketResponse.error) {
        throw new Error(ticketResponse.error)
      }
      return ticketResponse
    })

    const tickets = await Promise.all(ticketPromises)
    console.log('Tickets created:', tickets)

    paymentSuccess.value = true

    // Limpiar detalles de compra almacenados
    localStorage.removeItem('ticketPurchaseDetails')
  } catch (err) {
    console.error('Error al procesar el pago:', err)
    error.value = err.message || 'Ha ocurrido un error al procesar el pago. Por favor, inténtalo de nuevo.'
  } finally {
    loading.value = false
  }
}

// Navegar a mis entradas
const viewTickets = () => {
  router.push('/billetesDeUsuario')
}

// Volver al inicio
const goHome = () => {
  router.push('/')
}

// Volver atrás
const goBack = () => {
  router.back()
}

// Cargar datos iniciales
onMounted(async () => {
  if (!isAuthenticated.value) {
    router.push('/login?redirect=/tickets')
    return
  }
  try {
    const storedDetails = localStorage.getItem('ticketPurchaseDetails')
    if (!storedDetails) {
      throw new Error('No hay detalles de compra disponibles')
    }
    const details = JSON.parse(storedDetails)
    ticketDetails.value = details
    if (details.movie && !details.movie.titulo && details.movie.id) {
      const movieData = await getPeliculaById(details.movie.id)
      ticketDetails.value.movie = movieData
    }
  } catch (err) {
    console.error('Error al cargar los detalles:', err)
    error.value = 'No se pudieron cargar los detalles de la compra. Por favor, vuelve a intentarlo.'
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

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}
</style>
