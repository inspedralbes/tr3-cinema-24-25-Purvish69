<template>
  <v-app class="cinema-app-root bg-[#090b14] text-white">
    <Navbar />

    <div class="cinema-booking-page min-h-screen py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Estado de carga -->
        <div v-if="loading" class="flex flex-col justify-center items-center h-[70vh] gap-4">
          <div class="relative">
            <div class="w-16 h-16 border-4 border-amber-400/20 border-t-amber-400 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center text-amber-400">
              <i class="mdi mdi-filmstrip text-xl"></i>
            </div>
          </div>
          <p class="text-amber-200/80 font-medium tracking-wider text-sm animate-pulse">CARGANDO SALA Y BUTACAS...</p>
        </div>

        <!-- Muestra error en caso de fallo -->
        <div v-else-if="error" class="max-w-lg mx-auto glass-card p-8 rounded-2xl text-center border border-red-500/30 shadow-2xl mt-12">
          <div class="w-16 h-16 bg-red-500/10 text-red-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="mdi mdi-alert-circle"></i>
          </div>
          <h2 class="text-xl font-bold text-white mb-2">No se pudo cargar la sesión</h2>
          <p class="text-gray-300 text-sm mb-6">{{ error }}</p>
          <button
            @click="router.push('/movieDetails/moviSession')"
            class="px-6 py-2.5 rounded-full bg-amber-400 text-gray-950 font-bold hover:bg-amber-300 transition-all shadow-lg shadow-amber-400/20"
          >
            Ver otras sesiones
          </button>
        </div>

        <!-- Contenido principal -->
        <div v-else class="space-y-8 mt-4 animate-fade-in">

          <!-- 1. BARRA SUPERIOR / BREADCRUMB -->
          <div class="flex items-center justify-between flex-wrap gap-4">
            <button
              @click="router.push('/movieDetails/moviSession')"
              class="flex items-center gap-2 text-sm text-gray-400 hover:text-amber-300 transition-colors py-1.5 px-3 rounded-lg hover:bg-white/5"
            >
              <i class="mdi mdi-arrow-left text-lg"></i>
              <span>Volver a la cartelera</span>
            </button>

            <!-- Sincronización en vivo -->
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-medium">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
              <span>Disponibilidad sincronizada en tiempo real</span>
            </div>
          </div>

          <!-- 2. TARJETA CABECERA DE LA PELÍCULA Y SESIÓN -->
          <div class="glass-card rounded-2xl overflow-hidden border border-white/10 shadow-2xl relative">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 via-purple-500/5 to-transparent pointer-events-none"></div>

            <div class="flex flex-col md:flex-row items-center md:items-stretch">
              <!-- Cartel de la película -->
              <div class="w-full md:w-56 lg:w-64 h-64 md:h-auto flex-shrink-0 relative overflow-hidden group">
                <img
                  :src="currentMovie?.imagen || 'https://via.placeholder.com/400x600?text=Film+Galaxy'"
                  :alt="currentMovie?.titulo"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-[#0c0f1d] via-transparent to-transparent"></div>
              </div>

              <!-- Información de la sesión -->
              <div class="p-6 md:p-8 flex-1 flex flex-col justify-between z-10 w-full">
                <div>
                  <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wider uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30">
                      {{ selectedSession?.sala || 'SALA VIP 1' }}
                    </span>
                    <span v-if="selectedSession?.dia_espectador" class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wider uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30 flex items-center gap-1 animate-pulse">
                      <i class="mdi mdi-tag-star"></i>
                      ¡Día del Espectador! Precios reducidos
                    </span>
                  </div>

                  <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-3">
                    {{ currentMovie?.titulo }}
                  </h1>

                  <p v-if="currentMovie?.descripcion" class="text-gray-400 text-sm line-clamp-2 mb-4 max-w-3xl">
                    {{ currentMovie?.descripcion }}
                  </p>
                </div>

                <!-- Pills con detalles de la proyección -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-white/10 text-xs sm:text-sm">
                  <div class="flex items-center gap-2.5 text-gray-300">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center text-base">
                      <i class="mdi mdi-calendar"></i>
                    </div>
                    <div>
                      <div class="text-[10px] text-gray-400 uppercase tracking-wider">Fecha</div>
                      <div class="font-semibold text-white capitalize">{{ formatDate(selectedSession?.fecha) }}</div>
                    </div>
                  </div>

                  <div class="flex items-center gap-2.5 text-gray-300">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center text-base">
                      <i class="mdi mdi-clock-outline"></i>
                    </div>
                    <div>
                      <div class="text-[10px] text-gray-400 uppercase tracking-wider">Hora</div>
                      <div class="font-semibold text-white">{{ selectedSession?.hora }}</div>
                    </div>
                  </div>

                  <div class="flex items-center gap-2.5 text-gray-300">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center text-base">
                      <i class="mdi mdi-timer-outline"></i>
                    </div>
                    <div>
                      <div class="text-[10px] text-gray-400 uppercase tracking-wider">Duración</div>
                      <div class="font-semibold text-white">{{ currentMovie?.duracion || 120 }} min</div>
                    </div>
                  </div>

                  <div class="flex items-center gap-2.5 text-gray-300">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center text-base">
                      <i class="mdi mdi-star"></i>
                    </div>
                    <div>
                      <div class="text-[10px] text-gray-400 uppercase tracking-wider">Calificación</div>
                      <div class="font-semibold text-amber-300">{{ currentMovie?.calificacion || '8.5' }} / 10</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. CONTENEDOR PRINCIPAL: SALA DE CINE + BILLETE INTERACTIVO -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- COLUMNA IZQUIERDA: PANTALLA Y MAPA DE BUTACAS (8 COLS) -->
            <div class="lg:col-span-8 glass-card p-6 sm:p-8 rounded-2xl border border-white/10 shadow-2xl relative overflow-hidden">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-white/10 gap-3">
                <div>
                  <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                    <i class="mdi mdi-seat-recline-extra text-amber-400"></i>
                    Selecciona tus Butacas
                  </h2>
                  <p class="text-xs text-gray-400 mt-1">Haz clic en los asientos disponibles para reservarlos</p>
                </div>

                <!-- Temporizador de bloqueo si hay asientos seleccionados -->
                <div
                  v-if="selectedSeats.length > 0"
                  class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-400/10 border border-amber-400/30 text-amber-300 text-xs font-semibold"
                >
                  <i class="mdi mdi-timer-sand text-base animate-spin-slow"></i>
                  <span>Bloqueo temporal activo: <strong>{{ formattedHoldTime }}</strong></span>
                </div>
              </div>

              <!-- LEYENDA MODERNA CON BUTACAS MINIATURA -->
              <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 py-6 text-xs text-gray-300">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6">
                    <SeatIcon status="normal" number="·" />
                  </div>
                  <span>Normal (€{{ prices.normal }})</span>
                </div>

                <div class="flex items-center gap-2">
                  <div class="w-6 h-6">
                    <SeatIcon status="vip" number="★" />
                  </div>
                  <span class="text-amber-300 font-medium">VIP (€{{ prices.vip }})</span>
                </div>

                <div class="flex items-center gap-2">
                  <div class="w-6 h-6">
                    <SeatIcon status="selected" number="✓" />
                  </div>
                  <span class="text-emerald-300 font-semibold">Tu selección</span>
                </div>

                <div class="flex items-center gap-2">
                  <div class="w-6 h-6">
                    <SeatIcon status="held" number="⏳" />
                  </div>
                  <span class="text-orange-300 font-medium">En reserva</span>
                </div>

                <div class="flex items-center gap-2">
                  <div class="w-6 h-6">
                    <SeatIcon status="bought" number="" />
                  </div>
                  <span class="text-gray-500">Ocupado</span>
                </div>
              </div>

              <!-- PANTALLA DE CINE CINEMÁTICA CON LUZ DE PROYECCIÓN -->
              <div class="relative max-w-xl mx-auto my-8 select-none">
                <!-- Haz de luz del proyector -->
                <div class="projection-beam"></div>

                <!-- Pantalla curva con perspectiva -->
                <div class="cinema-screen-curve">
                  <div class="screen-light-bar"></div>
                  <div class="flex items-center justify-center gap-2 py-2 text-xs font-black tracking-[0.3em] text-white/80 uppercase">
                    <i class="mdi mdi-projector text-amber-400 text-sm"></i>
                    <span>PANTALLA</span>
                  </div>
                </div>
              </div>

              <!-- MAPA / CUADRÍCULA DE ASIENTOS CON SCROLL SUAVE -->
              <div class="overflow-x-auto pb-6 pt-2 cinema-room-scroll">
                <div class="min-w-max mx-auto px-4 flex flex-col items-center">

                  <!-- Fila con números superiores como guía -->
                  <div class="flex items-center mb-3">
                    <div class="w-8 mr-3"></div> <!-- Espaciador para la letra de fila izq -->
                    <div
                      v-for="seatNumber in uniqueNumbers"
                      :key="'col-' + seatNumber"
                      class="w-11 sm:w-12 h-6 flex items-center justify-center text-[11px] font-bold text-gray-400 mx-1 select-none"
                    >
                      {{ seatNumber }}
                    </div>
                    <div class="w-8 ml-3"></div> <!-- Espaciador para la letra de fila der -->
                  </div>

                  <!-- Filas de butacas -->
                  <div
                    v-for="rowLetter in uniqueRows"
                    :key="'row-' + rowLetter"
                    class="flex items-center mb-3.5 group/row"
                  >
                    <!-- Indicador de Fila Izquierda -->
                    <div class="w-8 h-8 mr-3 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-xs font-black text-amber-300/80 group-hover/row:text-amber-400 group-hover/row:border-amber-400/40 select-none transition-all">
                      {{ rowLetter }}
                    </div>

                    <!-- Butacas de la fila -->
                    <template v-for="seatNumber in uniqueNumbers" :key="`${rowLetter}-${seatNumber}`">
                      <div v-if="getSeat(rowLetter, seatNumber)" class="mx-1">
                        <button
                          type="button"
                          @click="toggleSeat(getSeat(rowLetter, seatNumber))"
                          :disabled="isSeatBought(getSeat(rowLetter, seatNumber)) || isSeatHeldByOther(getSeat(rowLetter, seatNumber))"
                          class="w-11 h-11 sm:w-12 sm:h-12 relative p-0.5 rounded-lg transition-transform focus:outline-none"
                          :title="getSeatTooltip(getSeat(rowLetter, seatNumber))"
                        >
                          <SeatIcon
                            :status="getSeatStatus(getSeat(rowLetter, seatNumber))"
                            :number="seatNumber"
                            :seat-id="getSeat(rowLetter, seatNumber).id"
                          />
                        </button>
                      </div>
                      <!-- Pasillo / Espacio vacío si no hay asiento -->
                      <div v-else class="w-11 sm:w-12 h-11 sm:h-12 mx-1 opacity-0 pointer-events-none"></div>
                    </template>

                    <!-- Indicador de Fila Derecha -->
                    <div class="w-8 h-8 ml-3 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-xs font-black text-amber-300/80 group-hover/row:text-amber-400 group-hover/row:border-amber-400/40 select-none transition-all">
                      {{ rowLetter }}
                    </div>
                  </div>

                </div>
              </div>

              <!-- Aviso informativo inferior de la sala -->
              <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between text-xs text-gray-400 gap-2">
                <span class="flex items-center gap-1.5">
                  <i class="mdi mdi-information-outline text-amber-400"></i>
                  Máximo 10 butacas por transacción
                </span>
                <span class="flex items-center gap-1.5">
                  <i class="mdi mdi-wheelchair-accessibility text-blue-400"></i>
                  Acceso habilitado para personas con movilidad reducida
                </span>
              </div>
            </div>


            <!-- COLUMNA DERECHA: BILLETE INTERACTIVO CON ANIMACIÓN (4 COLS) -->
            <div class="lg:col-span-4 sticky top-6">
              <div class="ticket-container relative rounded-3xl overflow-hidden shadow-2xl border border-amber-400/30">

                <!-- Adorno holográfico y destellos -->
                <div class="ticket-hologram"></div>

                <!-- 1. CABECERA DEL BILLETE -->
                <div class="ticket-header p-6 pb-5 relative bg-gradient-to-b from-[#191e33] to-[#121626] border-b border-dashed border-amber-400/30">
                  <!-- Muescas del billete (Ticket notches) -->
                  <div class="ticket-notch notch-left"></div>
                  <div class="ticket-notch notch-right"></div>

                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-amber-500 to-amber-300 text-gray-950 flex items-center justify-center font-black text-sm shadow-md">
                        FG
                      </div>
                      <span class="font-black text-xs tracking-widest text-amber-300 uppercase">FILMGALAXY PASS</span>
                    </div>

                    <span class="text-[10px] font-mono text-gray-400 tracking-wider">
                      #{{ selectedSession?.id ? `SES-${selectedSession.id}` : 'PASS' }}
                    </span>
                  </div>

                  <h3 class="text-lg font-bold text-white leading-snug line-clamp-1 mb-1">
                    {{ currentMovie?.titulo }}
                  </h3>

                  <div class="flex items-center gap-3 text-xs text-gray-300">
                    <span class="flex items-center gap-1">
                      <i class="mdi mdi-clock-outline text-amber-400"></i>
                      {{ selectedSession?.hora }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                      <i class="mdi mdi-theater text-amber-400"></i>
                      {{ selectedSession?.sala || 'Sala 1' }}
                    </span>
                  </div>
                </div>

                <!-- 2. CUERPO DEL BILLETE (ASIENTOS Y RESUMEN) -->
                <div class="ticket-body p-6 bg-[#0f1322] space-y-5">

                  <!-- ALERTA DE TEMPORIZADOR DE RESERVA (SI HAY ASIENTOS) -->
                  <div
                    v-if="selectedSeats.length > 0"
                    class="p-3 rounded-xl bg-amber-400/10 border border-amber-400/30 flex items-center justify-between animate-fade-in"
                  >
                    <div class="flex items-center gap-2 text-xs text-amber-300">
                      <i class="mdi mdi-timer-outline text-base animate-spin-slow"></i>
                      <div>
                        <div class="font-bold">Reserva temporal activa</div>
                        <div class="text-[10px] text-amber-200/80">Quedan {{ formattedHoldTime }} min para finalizar</div>
                      </div>
                    </div>
                    <div class="text-xs font-mono font-bold px-2 py-1 rounded bg-amber-400 text-gray-950 shadow">
                      {{ formattedHoldTime }}
                    </div>
                  </div>

                  <!-- SELECCIÓN DE ASIENTOS: ESTADO VACÍO O LISTADO -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-xs uppercase tracking-wider font-bold text-gray-400">Butacas Elegidas</span>
                      <span class="text-xs font-bold text-amber-400">
                        {{ selectedSeats.length }} / 10
                      </span>
                    </div>

                    <!-- ESTADO VACÍO CON ANIMACIÓN -->
                    <div
                      v-if="selectedSeats.length === 0"
                      class="p-6 rounded-2xl border-2 border-dashed border-white/10 text-center flex flex-col items-center justify-center gap-3"
                    >
                      <div class="w-12 h-12 rounded-full bg-amber-400/10 text-amber-400 flex items-center justify-center text-2xl animate-bounce-gentle">
                        <i class="mdi mdi-seat-outline"></i>
                      </div>
                      <p class="text-xs text-gray-400 max-w-[200px] leading-relaxed">
                        Selecciona butacas en el mapa para emitir tu billete
                      </p>
                    </div>

                    <!-- LISTA DE BUTACAS SELECCIONADAS CON CHIPS ANIMADOS -->
                    <div v-else class="space-y-2 max-h-48 overflow-y-auto pr-1 cinema-custom-scroll">
                      <div
                        v-for="seat in selectedSeats"
                        :key="'chip-' + seat.id"
                        class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-amber-400/40 transition-all group animate-pop"
                      >
                        <div class="flex items-center gap-2.5">
                          <div
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold"
                            :class="seat.tipo === 'vip' ? 'bg-amber-400 text-gray-950' : 'bg-emerald-500 text-white'"
                          >
                            <i :class="seat.tipo === 'vip' ? 'mdi mdi-crown' : 'mdi mdi-seat'"></i>
                          </div>
                          <div>
                            <div class="text-xs font-bold text-white flex items-center gap-1.5">
                              Fila {{ seat.fila }} • Asiento {{ seat.numero }}
                              <span
                                class="text-[9px] uppercase px-1.5 py-0.2 rounded font-black tracking-wider"
                                :class="seat.tipo === 'vip' ? 'bg-amber-400/20 text-amber-300' : 'bg-gray-700 text-gray-300'"
                              >
                                {{ seat.tipo }}
                              </span>
                            </div>
                            <div class="text-[10px] text-gray-400">
                              €{{ seat.tipo === 'vip' ? prices.vip : prices.normal }}
                            </div>
                          </div>
                        </div>

                        <!-- Botón para quitar asiento -->
                        <button
                          type="button"
                          @click="toggleSeat(seat)"
                          class="w-6 h-6 rounded-full bg-white/10 hover:bg-red-500/20 hover:text-red-400 text-gray-400 flex items-center justify-center transition-all text-xs"
                          title="Eliminar asiento"
                        >
                          <i class="mdi mdi-close"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- DESGLOSE DE PRECIOS -->
                  <div class="pt-4 border-t border-white/10 space-y-2 text-xs">
                    <div v-if="normalSeatsCount > 0" class="flex justify-between text-gray-300">
                      <span>Normal ({{ normalSeatsCount }}x €{{ prices.normal }})</span>
                      <span class="font-mono font-medium">€{{ normalSeatsTotal.toFixed(2) }}</span>
                    </div>

                    <div v-if="vipSeatsCount > 0" class="flex justify-between text-gray-300">
                      <span class="text-amber-300">VIP ({{ vipSeatsCount }}x €{{ prices.vip }})</span>
                      <span class="font-mono font-medium text-amber-300">€{{ vipSeatsTotal.toFixed(2) }}</span>
                    </div>

                    <div class="pt-3 border-t border-white/10 flex items-baseline justify-between">
                      <div>
                        <span class="text-sm font-bold text-white">Importe Total</span>
                        <div class="text-[10px] text-gray-400">IVA incluido</div>
                      </div>
                      <div class="text-2xl font-black text-amber-400 font-mono tracking-tight">
                        €{{ total.toFixed(2) }}
                      </div>
                    </div>
                  </div>

                  <!-- BOTÓN DE COMPRA / CONTINUAR CON ANIMACIÓN SHIMMER -->
                  <div class="pt-2">
                    <button
                      type="button"
                      @click="purchaseTickets"
                      :disabled="selectedSeats.length === 0"
                      class="w-full py-3.5 px-6 rounded-2xl font-black text-sm tracking-wide uppercase transition-all duration-300 flex items-center justify-center gap-2 relative overflow-hidden group shadow-xl"
                      :class="selectedSeats.length === 0
                        ? 'bg-gray-800 text-gray-500 cursor-not-allowed border border-white/5'
                        : 'bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 text-gray-950 hover:shadow-amber-400/30 hover:scale-[1.02] active:scale-[0.98]'"
                    >
                      <!-- Efecto brillo sweep al pasar cursor -->
                      <span v-if="selectedSeats.length > 0" class="ticket-button-shine"></span>

                      <i class="mdi mdi-ticket-confirmation-outline text-lg"></i>
                      <span>Continuar al Pago ({{ selectedSeats.length }})</span>
                      <i class="mdi mdi-arrow-right text-lg transition-transform group-hover:translate-x-1"></i>
                    </button>
                  </div>

                  <!-- CÓDIGO DE BARRAS DE ADORNO AL PIE DEL BILLETE -->
                  <div class="pt-3 border-t border-dashed border-white/10 flex flex-col items-center justify-center opacity-40 select-none">
                    <div class="ticket-barcode-strip"></div>
                    <span class="text-[8px] font-mono tracking-widest text-gray-400 mt-1">
                      FILMGALAXY • ENTRADA OFICIAL
                    </span>
                  </div>

                </div>
              </div>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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

// Temporizador visual para el bloqueo de 5 minutos
const holdSecondsLeft = ref(300)
let holdTimer = null

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

// Control del temporizador de 5 minutos
const startHoldTimer = () => {
  holdSecondsLeft.value = 300
  if (holdTimer) clearInterval(holdTimer)
  holdTimer = setInterval(() => {
    if (holdSecondsLeft.value > 0) {
      holdSecondsLeft.value--
    } else {
      clearInterval(holdTimer)
      fetchAsientosStatus()
    }
  }, 1000)
}

const stopHoldTimer = () => {
  if (holdTimer) {
    clearInterval(holdTimer)
    holdTimer = null
  }
}

watch(selectedSeats, (newVal) => {
  if (newVal.length > 0) {
    if (!holdTimer) startHoldTimer()
  } else {
    stopHoldTimer()
  }
}, { deep: true })

const formattedHoldTime = computed(() => {
  const m = Math.floor(holdSecondsLeft.value / 60)
  const s = holdSecondsLeft.value % 60
  return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`
})

// Código representativo del asiento ej: "A-1"
const getSeatCode = (seat) => {
  return seat && seat.fila && seat.numero ? `${seat.fila}-${seat.numero}` : ''
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

const getSeatStatus = (seat) => {
  if (!seat) return 'normal'
  if (isSeatBought(seat)) return 'bought'
  if (isSeatHeldByOther(seat)) return 'held'
  if (isSeatSelected(seat)) return 'selected'
  if (seat.tipo === 'vip') return 'vip'
  return 'normal'
}

const getSeatTooltip = (seat) => {
  if (!seat) return ''
  const status = getSeatStatus(seat)
  const typeLabel = seat.tipo === 'vip' ? `VIP (€${prices.value.vip})` : `Normal (€${prices.value.normal})`
  if (status === 'bought') return `Fila ${seat.fila} • Butaca ${seat.numero} - Ocupado`
  if (status === 'held') return `Fila ${seat.fila} • Butaca ${seat.numero} - En proceso de reserva por otro usuario`
  if (status === 'selected') return `Fila ${seat.fila} • Butaca ${seat.numero} - Seleccionado (${typeLabel})`
  return `Fila ${seat.fila} • Butaca ${seat.numero} (${typeLabel})`
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

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('es-ES', options)
}

// 2. Al seleccionar un asiento, llama a POST /holds. Si responde 409, no lo selecciona
// y muestra: "Este asiento está en proceso de reserva por otro usuario". Al deseleccionar, DELETE /holds.
const toggleSeat = async (seat) => {
  if (!seat || !seat.fila || !seat.numero) return
  if (!selectedSession.value?.id) return

  // Si ya está comprado, no hacer nada
  if (isSeatBought(seat)) return

  // Si está retenido por otro usuario, avisar y no hacer nada
  if (isSeatHeldByOther(seat)) {
    showNotice('Este asiento está en proceso de reserva por otro usuario', 'orange-darken-2')
    return
  }

  const seatCode = getSeatCode(seat)
  if (!seatCode) return
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

      // Si fue exitoso, seleccionarlo y reiniciar el reloj de 5 min
      selectedSeats.value.push(seat)
      startHoldTimer()

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

        if (isBought || isHeldByOtherUser || (!isStillHeldByMe && myHoldSeats.value.length > 0)) {
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
  stopHoldTimer()
})
</script>

<style scoped>
/* Fondo ambiental de cine */
.cinema-booking-page {
  background: radial-gradient(circle at 50% 10%, rgba(30, 41, 59, 0.5) 0%, rgba(10, 13, 24, 0.98) 70%);
}

.glass-card {
  background: rgba(18, 22, 36, 0.75);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
}

/* Pantalla de cine curvada con proyección */
.cinema-screen-curve {
  position: relative;
  height: 38px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.04) 100%);
  border-top: 3px solid rgba(251, 191, 36, 0.8);
  border-radius: 50% 50% 0 0 / 14px 14px 0 0;
  box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.25), inset 0 2px 8px rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
}

.screen-light-bar {
  position: absolute;
  top: -2px;
  left: 20%;
  right: 20%;
  height: 3px;
  background: #fef08a;
  filter: blur(2px);
  box-shadow: 0 0 14px #fbbf24;
}

.projection-beam {
  position: absolute;
  top: 10px;
  left: 10%;
  right: 10%;
  height: 80px;
  background: linear-gradient(180deg, rgba(251, 191, 36, 0.12) 0%, rgba(56, 189, 248, 0.03) 60%, transparent 100%);
  clip-path: polygon(15% 0%, 85% 0%, 100% 100%, 0% 100%);
  pointer-events: none;
}

/* Billete estilizado */
.ticket-container {
  background: #0f1322;
}

.ticket-hologram {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #f59e0b, #ec4899, #8b5cf6, #3b82f6, #10b981, #f59e0b);
  background-size: 200% auto;
  animation: hologramShift 6s linear infinite;
}

.ticket-notch {
  position: absolute;
  bottom: -11px;
  width: 22px;
  height: 22px;
  background: #090b14;
  border-radius: 50%;
  z-index: 20;
}

.notch-left {
  left: -11px;
  box-shadow: inset -2px 0 3px rgba(0, 0, 0, 0.4);
}

.notch-right {
  right: -11px;
  box-shadow: inset 2px 0 3px rgba(0, 0, 0, 0.4);
}

/* Código de barras decorativo */
.ticket-barcode-strip {
  width: 140px;
  height: 22px;
  background: repeating-linear-gradient(
    90deg,
    #ffffff,
    #ffffff 2px,
    transparent 2px,
    transparent 4px,
    #ffffff 4px,
    #ffffff 7px,
    transparent 7px,
    transparent 9px,
    #ffffff 9px,
    #ffffff 10px,
    transparent 10px,
    transparent 13px
  );
  opacity: 0.7;
}

/* Brillo en botón de compra */
.ticket-button-shine {
  position: absolute;
  top: -50%;
  left: -60%;
  width: 40%;
  height: 200%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
  transform: rotate(30deg);
  animation: buttonSweep 3.5s infinite;
}

/* Animaciones */
@keyframes hologramShift {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 200% 50%;
  }
}

@keyframes buttonSweep {
  0% {
    left: -70%;
  }
  30%, 100% {
    left: 140%;
  }
}

@keyframes popIn {
  0% {
    opacity: 0;
    transform: scale(0.9);
  }
  70% {
    transform: scale(1.05);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-pop {
  animation: popIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

.animate-spin-slow {
  animation: spin 3s linear infinite;
}

.animate-bounce-gentle {
  animation: bounceGentle 2.5s infinite ease-in-out;
}

@keyframes bounceGentle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-6px);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Scrollbars personalizados */
.cinema-room-scroll::-webkit-scrollbar,
.cinema-custom-scroll::-webkit-scrollbar {
  height: 6px;
  width: 5px;
}

.cinema-room-scroll::-webkit-scrollbar-track,
.cinema-custom-scroll::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.04);
  border-radius: 999px;
}

.cinema-room-scroll::-webkit-scrollbar-thumb,
.cinema-custom-scroll::-webkit-scrollbar-thumb {
  background: rgba(251, 191, 36, 0.3);
  border-radius: 999px;
}

.cinema-room-scroll::-webkit-scrollbar-thumb:hover,
.cinema-custom-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(251, 191, 36, 0.6);
}
</style>
