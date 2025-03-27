<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br custom-bg py-4 md:py-8">
      <div class="container mx-auto px-2 md:px-4">
        <!-- Estado de carga -->
        <div v-if="loading" class="flex justify-center items-center h-[70vh]">
          <div class="animate-pulse-slow">
            <div class="w-16 h-16 border-4 border-gold border-t-transparent rounded-full animate-spin"></div>
          </div>
        </div>

        <!-- Muestra error en caso de fallo -->
        <div v-else-if="error" class="max-w-md mx-auto glass-effect text-light p-4 md:p-8 rounded-xl">
          <p class="text-center text-red-500">{{ error }}</p>
        </div>

        <!-- Contenido principal -->
        <div v-else class="max-w-6xl mx-auto mt-4 md:mt-12">
          <!-- Tarjeta de información de la película y la sesión -->
          <div class="glass-effect rounded-xl overflow-hidden mb-6 md:mb-8">
            <div class="flex flex-col md:flex-row">
              <!-- Cartel de la película -->
              <div class="md:w-1/3 h-[200px] md:h-auto">
                <img :src="currentMovie?.imagen || 'https://via.placeholder.com/400x600?text=No+Poster'"
                  :alt="currentMovie?.titulo" class="w-full h-full object-cover" />
              </div>

              <!-- Detalles de la sesión -->
              <div class="p-4 md:p-6 bg-primary/20 flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gold mb-4">{{ currentMovie?.titulo }}</h1>

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
          <div class="glass-effect p-4 md:p-6 rounded-xl">
            <h2 class="text-xl md:text-2xl font-semibold text-gold mb-4">Seleccionar Asientos</h2>

            <!-- Leyenda de estados -->
            <div class="grid grid-cols-2 md:flex md:flex-wrap gap-4 md:gap-6 mb-6">
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 md:w-6 md:h-6 bg-gray-500 rounded"></div>
                <span class="text-sm md:text-base text-light">Disponible</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 md:w-6 md:h-6 bg-red-500 rounded"></div>
                <span class="text-sm md:text-base text-light">Ocupado</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 md:w-6 md:h-6 bg-green-500 rounded"></div>
                <span class="text-sm md:text-base text-light">Seleccionado</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 md:w-6 md:h-6 bg-gold rounded"></div>
                <span class="text-sm md:text-base text-light">VIP</span>
              </div>
            </div>

            <!-- Pantalla de la sala -->
            <div class="w-full h-4 md:h-6 bg-gray-800 rounded-t-full mb-4 md:mb-8 text-center text-xs text-light/50 leading-tight">
              PANTALLA
            </div>

            <!-- Contenedor con scroll horizontal para móviles -->
            <div class="overflow-x-auto pb-2">
              <div class="min-w-[500px] md:min-w-0 w-full">
                <!-- Cuadrícula de asientos con filas verticales y números horizontales -->
                <div class="flex justify-center mb-4 md:mb-8">
                  <!-- Etiquetas de filas (A,B,C...) -->
                  <div class="flex flex-col mr-2 md:mr-4">
                    <div class="h-6 md:h-8 flex items-center justify-center"></div>
                    <template v-for="rowLetter in uniqueRows" :key="rowLetter">
                      <div class="h-6 md:h-8 w-6 md:w-8 flex items-center justify-center text-light font-bold mb-1 md:mb-2">
                        {{ rowLetter }}
                      </div>
                    </template>
                  </div>

                  <div>
                    <!-- Números de asiento -->
                    <div class="flex mb-2">
                      <div v-for="seatNumber in uniqueNumbers" :key="seatNumber" 
                           class="w-6 md:w-8 h-6 md:h-8 flex items-center justify-center text-light font-bold mx-0.5 md:mx-1">
                        {{ seatNumber }}
                      </div>
                    </div>

                    <!-- Cuadrícula de asientos -->
                    <div>
                      <div v-for="rowLetter in uniqueRows" :key="rowLetter" class="flex mb-1 md:mb-3">
                        <template v-for="seatNumber in uniqueNumbers" :key="`${rowLetter}-${seatNumber}`">
                          <div v-if="getSeat(rowLetter, seatNumber)" class="mx-0.25 md:mx-1">
                            <button 
                              @click="toggleSeat(getSeat(rowLetter, seatNumber))" 
                              :disabled="getSeat(rowLetter, seatNumber).estado === 'ocupada'"
                              class="w-4 h-4 md:w-8 md:h-8 relative rounded transition-all flex items-center justify-center"
                              :class="[
                                getSeat(rowLetter, seatNumber).estado === 'ocupada' ? 'bg-red-500' :
                                  selectedSeats.includes(getSeat(rowLetter, seatNumber)) ? 'bg-green-500' :
                                    getSeat(rowLetter, seatNumber).tipo === 'vip' ? 'bg-gold' : 'bg-gray-500'
                              ]">
                              <SeatIcon class="absolute inset-0 scale-40 md:scale-75" />
                            </button>
                          </div>
                          <div v-else class="w-4 h-4 md:w-8 md:h-8 mx-0.25 md:mx-1"></div>
                        </template>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Resumen de precios -->
            <div class="p-4 md:p-6 rounded-xl bg-primary/10">
              <h3 class="text-lg md:text-xl font-semibold text-gold mb-4">Resumen</h3>
              <div class="space-y-2 text-light">
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
                class="w-full md:w-auto bg-gold text-dark px-6 md:px-8 py-3 rounded-full font-semibold transition-all"
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
// Aquí va tu lógica de script existente
</script>

<style scoped>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 110%);
}

.glass-effect {
  background: rgba(74, 78, 105, 0.9);
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

/* Personalización del scrollbar para el contenedor de asientos */
.overflow-x-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(234, 224, 213, 0.3) transparent;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: rgba(234, 224, 213, 0.3);
  border-radius: 20px;
}
</style>
