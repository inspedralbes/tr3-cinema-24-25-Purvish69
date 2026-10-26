<template>
  <v-app class="cinema-app-root">
    <div class="cinema-page-wrapper">
      <Navbar />

      <main class="tickets-page-content">
        <!-- Page Header -->
        <header class="page-header no-print">
          <div class="page-header-inner">
            <div class="header-badge">
              <i class="mdi mdi-ticket-account"></i>
              <span>EL MEU COMPTE</span>
            </div>
            <h1 class="page-title">Les Meves Entrades</h1>
            <p class="page-subtitle">
              Pases digitals amb codi QR únic per accedir a la sala de cinema
            </p>

            <!-- Search and Filter Bar -->
            <div v-if="tickets.length > 0" class="filters-panel">
              <div class="filters-row">
                <!-- Search input -->
                <div class="search-input-wrap">
                  <i class="mdi mdi-magnify search-icon"></i>
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Cercar per títol de pel·lícula..."
                    class="search-input"
                  />
                  <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="clear-search-btn"
                    title="Netejar cerca"
                  >
                    <i class="mdi mdi-close"></i>
                  </button>
                </div>

                <!-- Tabs for All / Upcoming / Past -->
                <div class="filter-tabs">
                  <button
                    class="filter-tab"
                    :class="{ active: activeTab === 'all' }"
                    @click="activeTab = 'all'"
                  >
                    Totes ({{ tickets.length }})
                  </button>
                  <button
                    class="filter-tab"
                    :class="{ active: activeTab === 'upcoming' }"
                    @click="activeTab = 'upcoming'"
                  >
                    Pròximes ({{ upcomingCount }})
                  </button>
                  <button
                    class="filter-tab"
                    :class="{ active: activeTab === 'past' }"
                    @click="activeTab = 'past'"
                  >
                    Anteriors ({{ pastCount }})
                  </button>
                </div>

                <!-- Print all button -->
                <button @click="printTickets" class="btn-print-all" title="Imprimir entrades">
                  <i class="mdi mdi-printer"></i>
                  <span>Imprimir</span>
                </button>
              </div>
            </div>
          </div>
        </header>

        <!-- Loading State -->
        <div v-if="loading" class="state-container">
          <div class="loading-spinner"></div>
          <p class="state-text">Carregant les teves entrades...</p>
        </div>

        <!-- Not Logged In State -->
        <div v-else-if="!isAuthenticated" class="state-container">
          <div class="auth-required-card">
            <div class="auth-icon-wrap">
              <i class="mdi mdi-account-lock-outline"></i>
            </div>
            <h2 class="auth-card-title">Inicia sessió per veure les teves entrades</h2>
            <p class="auth-card-desc">
              Necessites haver iniciat sessió amb el teu compte de FilmGalaxy per consultar i descarregar les teves entrades de cinema.
            </p>
            <button @click="goToLogin" class="btn-action-gold">
              <i class="mdi mdi-login"></i>
              <span>Iniciar Sessió</span>
            </button>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container no-print">
          <i class="mdi mdi-alert-circle-outline error-icon"></i>
          <p class="error-text">{{ error }}</p>
          <button @click="router.push('/movies')" class="btn-action-gold mt-4">
            <i class="mdi mdi-movie-open"></i>
            <span>Veure cartellera</span>
          </button>
        </div>

        <!-- No Tickets State -->
        <div v-else-if="!tickets.length" class="empty-container">
          <div class="empty-icon-box">
            <i class="mdi mdi-ticket-outline"></i>
          </div>
          <h2 class="empty-title">Encara no tens entrades</h2>
          <p class="empty-desc">
            Tria una pel·lícula de la nostra cartellera, reserva les teves butaques i gaudeix de la millor experiència de cinema.
          </p>
          <button @click="router.push('/movieDetails/moviSession')" class="btn-action-gold">
            <i class="mdi mdi-ticket-confirmation"></i>
            <span>Veure Sessions Disponibles</span>
          </button>
        </div>

        <!-- No Matches for Filter State -->
        <div v-else-if="filteredTickets.length === 0" class="empty-container">
          <div class="empty-icon-box">
            <i class="mdi mdi-filter-remove-outline"></i>
          </div>
          <h2 class="empty-title">No s'han trobat entrades amb aquest filtre</h2>
          <p class="empty-desc">Prova a canviar el text de cerca o la pestanya seleccionada.</p>
          <button @click="resetFilters" class="btn-action-gold">
            <i class="mdi mdi-refresh"></i>
            <span>Restablir filtres</span>
          </button>
        </div>

        <!-- Tickets List -->
        <div v-else class="tickets-list-wrapper">
          <div class="tickets-count-meta no-print">
            <span>
              Mostrant <strong>{{ filteredTickets.length }}</strong> de <strong>{{ tickets.length }}</strong> entrades
            </span>
            <span class="security-badge">
              <i class="mdi mdi-shield-check-outline"></i>
              QR xifrat únic per butaca
            </span>
          </div>

          <div class="tickets-stack">
            <!-- TICKET ITEM -->
            <article
              v-for="(ticket, index) in filteredTickets"
              :key="ticket.id"
              class="ticket-pass-card"
              :style="{ animationDelay: `${Math.min(index * 60, 600)}ms` }"
            >
              <!-- LEFT / MAIN CONTENT: Movie & Session Information -->
              <div class="ticket-main-section">
                <!-- Movie Poster thumbnail -->
                <div class="ticket-poster-wrap">
                  <img
                    :src="getMoviePoster(ticket)"
                    :alt="getMovieTitle(ticket)"
                    class="ticket-poster-img"
                    loading="lazy"
                    @error="handleImageError($event)"
                  />
                  <div class="poster-overlay-gradient"></div>
                  <span v-if="getMovieRating(ticket)" class="poster-rating-badge">
                    <i class="mdi mdi-star"></i>
                    {{ getMovieRating(ticket) }}
                  </span>
                </div>

                <!-- Ticket Details -->
                <div class="ticket-details-wrap">
                  <!-- Top Row: Title & Confirmation Badge -->
                  <div class="ticket-header-row">
                    <div>
                      <span class="ticket-category-tag">
                        {{ getMovieGenre(ticket) || 'CINEMA VIP' }}
                      </span>
                      <h2 class="ticket-movie-title" :title="getMovieTitle(ticket)">
                        {{ getMovieTitle(ticket) }}
                      </h2>
                    </div>

                    <div class="ticket-status-pill">
                      <span class="pulse-indicator"></span>
                      <span>VÀLIDA</span>
                    </div>
                  </div>

                  <!-- Details Grid -->
                  <div class="ticket-info-grid">
                    <!-- Data -->
                    <div class="info-cell">
                      <div class="info-label">
                        <i class="mdi mdi-calendar-blank-outline"></i>
                        <span>DATA</span>
                      </div>
                      <div class="info-value">
                        {{ formatDayAndDate(getSessionDate(ticket)) }}
                      </div>
                    </div>

                    <!-- Hora -->
                    <div class="info-cell">
                      <div class="info-label">
                        <i class="mdi mdi-clock-outline"></i>
                        <span>HORARI</span>
                      </div>
                      <div class="info-value highlight-gold">
                        {{ getSessionTime(ticket) }} hrs
                      </div>
                    </div>

                    <!-- Sala -->
                    <div class="info-cell">
                      <div class="info-label">
                        <i class="mdi mdi-projector-screen-outline"></i>
                        <span>SALA</span>
                      </div>
                      <div class="info-value">
                        Sala {{ getSessionRoom(ticket) }}
                        <small class="sub-label">Dolby Atmos</small>
                      </div>
                    </div>

                    <!-- Butaca -->
                    <div class="info-cell">
                      <div class="info-label">
                        <i class="mdi mdi-seat-recline-extra"></i>
                        <span>BUTACA</span>
                      </div>
                      <div class="info-value seat-value">
                        <span>Fila {{ getSeatRow(ticket) }}, Núm {{ getSeatNumber(ticket) }}</span>
                        <span
                          class="seat-type-pill"
                          :class="isVipSeat(ticket) ? 'vip' : 'standard'"
                        >
                          {{ isVipSeat(ticket) ? 'VIP' : 'ESTÀNDARD' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Bottom Row: Price & Transaction Metadata -->
                  <div class="ticket-bottom-row">
                    <div class="meta-payment-info">
                      <span class="price-chip">{{ getTicketPrice(ticket) }} €</span>
                      <span class="payment-method-chip">
                        <i class="mdi mdi-credit-card-outline"></i>
                        {{ getPaymentMethod(ticket) }}
                      </span>
                      <span class="date-purchased">
                        Comprat el {{ formatDate(ticket.created_at) }}
                      </span>
                    </div>

                    <div class="confirmation-code-wrap">
                      <span class="code-label">Codi:</span>
                      <code class="code-value">{{ getShortCode(ticket) }}</code>
                      <button
                        @click="copyConfirmationCode(ticket.codigo_confirmacion)"
                        class="btn-copy-code"
                        title="Copiar codi complet"
                      >
                        <i class="mdi mdi-content-copy"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PERFORATED TEAR-LINE DIVIDER (Authentic Cinema Stub Notches) -->
              <div class="ticket-perforated-divider">
                <div class="notch top-notch"></div>
                <div class="perforated-line"></div>
                <div class="notch bottom-notch"></div>
              </div>

              <!-- RIGHT SECTION: UNIQUE QR CODE PASS -->
              <div class="ticket-stub-section">
                <div class="stub-header">
                  <span class="stub-title">PASE D'ACCÉS</span>
                  <span class="stub-subtitle">Escaneja a l'entrada</span>
                </div>

                <!-- Unique QR Code Container -->
                <div
                  class="qr-code-box"
                  @click="openQrModal(ticket)"
                  title="Clica per ampliar el QR"
                >
                  <img
                    v-if="qrCodesMap[ticket.id]"
                    :src="qrCodesMap[ticket.id]"
                    :alt="`QR per entrada ${ticket.id}`"
                    class="qr-code-img"
                  />
                  <div v-else class="qr-loading-placeholder">
                    <div class="loading-spinner-sm"></div>
                  </div>

                  <div class="qr-hover-hint no-print">
                    <i class="mdi mdi-magnify-plus-outline"></i>
                    <span>Ampliar</span>
                  </div>
                </div>

                <!-- Stub Footer -->
                <div class="stub-footer">
                  <span class="stub-ticket-id">ENTRADA #{{ ticket.id }}</span>
                  <div class="stub-actions no-print">
                    <button
                      @click="openQrModal(ticket)"
                      class="btn-stub-action"
                      title="Veure codi QR en gran"
                    >
                      <i class="mdi mdi-qrcode-scan"></i>
                      <span>Ampliar QR</span>
                    </button>
                    <button
                      @click="printSingleTicket(ticket)"
                      class="btn-stub-action"
                      title="Descarregar / Imprimir aquesta entrada"
                    >
                      <i class="mdi mdi-download"></i>
                      <span>Descarregar</span>
                    </button>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </div>
      </main>

      <Footer class="no-print" />
    </div>

    <!-- FULLSCREEN MODAL FOR QR CODE SCANNING -->
    <div
      v-if="activeQrModalTicket"
      class="modal-backdrop no-print"
      role="dialog"
      aria-modal="true"
      @click.self="closeQrModal"
    >
      <div class="qr-modal-card">
        <!-- Close button -->
        <button @click="closeQrModal" class="btn-modal-close" aria-label="Tancar">
          <i class="mdi mdi-close"></i>
        </button>

        <!-- Brand header -->
        <div class="modal-brand">
          <i class="mdi mdi-filmstrip text-gold"></i>
          <span>FilmGalaxy Cinema Pass</span>
        </div>

        <h3 class="modal-movie-title">
          {{ getMovieTitle(activeQrModalTicket) }}
        </h3>

        <!-- Session quick info -->
        <div class="modal-session-row">
          <span>{{ formatDayAndDate(getSessionDate(activeQrModalTicket)) }}</span>
          <span>•</span>
          <strong class="text-gold">{{ getSessionTime(activeQrModalTicket) }} hrs</strong>
          <span>•</span>
          <span>Sala {{ getSessionRoom(activeQrModalTicket) }}</span>
          <span>•</span>
          <strong class="text-gold">Fila {{ getSeatRow(activeQrModalTicket) }}, Butaca {{ getSeatNumber(activeQrModalTicket) }}</strong>
        </div>

        <!-- High-res QR Display -->
        <div class="modal-qr-container">
          <img
            v-if="modalQrDataUrl"
            :src="modalQrDataUrl"
            alt="Codi QR ampliat"
            class="modal-qr-img"
          />
          <div v-else class="qr-loading-placeholder py-12">
            <div class="loading-spinner"></div>
          </div>
        </div>

        <!-- Confirmation UUID -->
        <div class="modal-code-box">
          <span class="text-xs text-lavender">CODI DE CONFIRMACIÓ</span>
          <p class="font-mono text-sm text-gold select-all">
            {{ activeQrModalTicket.codigo_confirmacion }}
          </p>
        </div>

        <p class="modal-scanner-hint">
          <i class="mdi mdi-cellphone-check"></i>
          Presenta aquest codi directament a l'escàner del personal de sala
        </p>

        <div class="modal-actions-row">
          <button @click="printSingleTicket(activeQrModalTicket)" class="btn-action-gold w-full">
            <i class="mdi mdi-printer"></i>
            <span>Imprimir Entrada</span>
          </button>
        </div>
      </div>
    </div>
  </v-app>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '~/composables/useAuth'
import { usePeliculas } from '~/composables/communicationManagerPelicula'
import QRCode from 'qrcode'

const router = useRouter()
const { userId, isAuthenticated } = useAuth()
const { getTickets } = usePeliculas()

const loading = ref(true)
const error = ref(null)
const tickets = ref([])

// Filters
const searchQuery = ref('')
const activeTab = ref('all') // 'all' | 'upcoming' | 'past'

// QR Codes dictionary (ticket.id -> dataUrl)
const qrCodesMap = ref({})

// Modal QR
const activeQrModalTicket = ref(null)
const modalQrDataUrl = ref(null)

// Helper methods to extract ticket information gracefully
const getMovieTitle = (ticket) => {
  return ticket.movie_session?.movie?.titulo || ticket.movie?.titulo || 'Pel·lícula sense títol'
}

const getMoviePoster = (ticket) => {
  return (
    ticket.movie_session?.movie?.imagen ||
    ticket.movie?.imagen ||
    ticket.movie_session?.movie?.poster ||
    'https://via.placeholder.com/300x450?text=FilmGalaxy'
  )
}

const getMovieGenre = (ticket) => {
  return ticket.movie_session?.movie?.genero || ticket.movie?.genero || ''
}

const getMovieRating = (ticket) => {
  return ticket.movie_session?.movie?.calificacion || ticket.movie?.calificacion || null
}

const getSessionDate = (ticket) => {
  return ticket.movie_session?.fecha || ticket.session?.fecha || ticket.created_at
}

const getSessionTime = (ticket) => {
  return ticket.movie_session?.hora || ticket.session?.hora || '18:00'
}

const getSessionRoom = (ticket) => {
  return ticket.movie_session?.sala || ticket.session?.sala || '1'
}

const getSeatRow = (ticket) => {
  return ticket.seat?.fila || '-'
}

const getSeatNumber = (ticket) => {
  return ticket.seat?.numero || '-'
}

const isVipSeat = (ticket) => {
  return ticket.seat?.tipo === 'vip' || ticket.movie_session?.fila_vip_activa
}

const getTicketPrice = (ticket) => {
  return ticket.precio !== undefined ? Number(ticket.precio).toFixed(2) : '6.00'
}

const getPaymentMethod = (ticket) => {
  const method = ticket.payment?.payment_method
  if (!method) return 'Targeta bancària'
  if (method === 'card') return 'Targeta bancària'
  if (method === 'paypal') return 'PayPal'
  return method
}

const getShortCode = (ticket) => {
  if (!ticket.codigo_confirmacion) return `FG-${ticket.id}`
  return `FG-${ticket.codigo_confirmacion.substring(0, 8).toUpperCase()}`
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/300x450?text=FilmGalaxy'
}

// Generate unique QR codes for tickets
const generateQrCodes = async (ticketList) => {
  for (const ticket of ticketList) {
    if (!qrCodesMap.value[ticket.id]) {
      try {
        const payload = ticket.codigo_confirmacion || `FG-TICKET-${ticket.id}-${ticket.user_id}`
        const url = await QRCode.toDataURL(payload, {
          width: 220,
          margin: 1,
          color: {
            dark: '#0c0f1c',
            light: '#ffffff'
          },
          errorCorrectionLevel: 'H'
        })
        qrCodesMap.value[ticket.id] = url
      } catch (err) {
        console.error('Error generant QR per a ticket:', ticket.id, err)
      }
    }
  }
}

// Format dates
const formatDate = (dateString) => {
  if (!dateString) return 'Data no disponible'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return dateString
    return date.toLocaleDateString('ca-ES', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch (e) {
    return dateString
  }
}

const formatDayAndDate = (dateString) => {
  if (!dateString) return 'Data no disponible'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return dateString

    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const tomorrow = new Date(today)
    tomorrow.setDate(tomorrow.getDate() + 1)

    const ticketDate = new Date(date)
    ticketDate.setHours(0, 0, 0, 0)

    if (ticketDate.getTime() === today.getTime()) {
      return 'Avui, ' + date.toLocaleDateString('ca-ES', { day: 'numeric', month: 'long' })
    } else if (ticketDate.getTime() === tomorrow.getTime()) {
      return 'Demà, ' + date.toLocaleDateString('ca-ES', { day: 'numeric', month: 'long' })
    }

    return date.toLocaleDateString('ca-ES', {
      weekday: 'long',
      day: 'numeric',
      month: 'long'
    })
  } catch (e) {
    return dateString
  }
}

// Filter tickets by query and tab
const isUpcoming = (ticket) => {
  const dateStr = getSessionDate(ticket)
  if (!dateStr) return true
  const date = new Date(dateStr)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date >= today
}

const upcomingCount = computed(() => {
  return tickets.value.filter(isUpcoming).length
})

const pastCount = computed(() => {
  return tickets.value.filter((t) => !isUpcoming(t)).length
})

const filteredTickets = computed(() => {
  let list = tickets.value

  // Filter by Tab
  if (activeTab.value === 'upcoming') {
    list = list.filter(isUpcoming)
  } else if (activeTab.value === 'past') {
    list = list.filter((t) => !isUpcoming(t))
  }

  // Filter by Search Query
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter((t) => {
      const title = getMovieTitle(t).toLowerCase()
      const genre = getMovieGenre(t).toLowerCase()
      const code = (t.codigo_confirmacion || '').toLowerCase()
      return title.includes(q) || genre.includes(q) || code.includes(q)
    })
  }

  return list
})

const resetFilters = () => {
  searchQuery.value = ''
  activeTab.value = 'all'
}

// Copy confirmation code
const copyConfirmationCode = async (code) => {
  if (!code) return
  try {
    await navigator.clipboard.writeText(code)
    alert(`Codi copiat al porta-retalls: ${code}`)
  } catch (err) {
    console.error('Error copiant:', err)
  }
}

// Open high-res QR Modal
const openQrModal = async (ticket) => {
  activeQrModalTicket.value = ticket
  modalQrDataUrl.value = null

  try {
    const payload = ticket.codigo_confirmacion || `FG-TICKET-${ticket.id}`
    modalQrDataUrl.value = await QRCode.toDataURL(payload, {
      width: 320,
      margin: 2,
      color: {
        dark: '#0c0f1c',
        light: '#ffffff'
      },
      errorCorrectionLevel: 'H'
    })
  } catch (err) {
    console.error('Error creant QR per al modal:', err)
  }
}

const closeQrModal = () => {
  activeQrModalTicket.value = null
  modalQrDataUrl.value = null
}

// Print actions
const printTickets = () => {
  window.print()
}

const printSingleTicket = (ticket) => {
  openQrModal(ticket)
  setTimeout(() => {
    window.print()
  }, 250)
}

const goToLogin = () => {
  router.push({
    path: '/login',
    query: { redirect: '/billetesDeUsuario' }
  })
}

// Get user identification
const getCurrentUserId = computed(() => {
  if (userId.value) return userId.value

  if (typeof window !== 'undefined') {
    try {
      const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
      if (storedUser.id) return storedUser.id
    } catch (e) {}
  }

  return null
})

onMounted(async () => {
  if (!isAuthenticated.value) {
    // If not authenticated, we keep loading false and let template show the login CTA
    loading.value = false
    return
  }

  try {
    const userIdValue = getCurrentUserId.value
    if (!userIdValue) {
      error.value = "No s'ha pogut identificar l'usuari actual."
      loading.value = false
      return
    }

    const allTickets = await getTickets(userIdValue)

    if (!allTickets || allTickets.error) {
            error.value = "Error en connectar amb el servidor d'entrades. Si us plau, torna-ho a provar."
      loading.value = false
      return
    }

    // Filter by logged-in user
    let userTickets = Array.isArray(allTickets)
      ? allTickets.filter((ticket) => !ticket.user_id || Number(ticket.user_id) === Number(userIdValue))
      : []

    tickets.value = userTickets

    // Generate unique QR code for each ticket
    await generateQrCodes(userTickets)
  } catch (err) {
        error.value = 'Hi ha hagut un problema en carregar les teves entrades.'
  } finally {
    loading.value = false
  }
})

// Watch for ticket changes to generate any missing QR
watch(tickets, (newTickets) => {
  if (newTickets && newTickets.length > 0) {
    generateQrCodes(newTickets)
  }
})
</script>

<style scoped>
/* ─── ROOT & LAYOUT ────────────────────────────────────────────────────────── */
.cinema-app-root {
  background: #080a14 !important;
}

:deep(.v-application__wrap) {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: transparent !important;
}

.cinema-page-wrapper {
  min-height: 100vh;
  background: radial-gradient(circle at 50% 0%, #1a203a 0%, #0c0f1c 45%, #06070e 100%);
  color: #f2e9e4;
  display: flex;
  flex-direction: column;
}

.tickets-page-content {
  flex: 1;
  padding-top: 74px; /* Sincronitzat amb la Navbar */
}

/* ─── HEADER ───────────────────────────────────────────────────────────────── */
.page-header {
  position: relative;
  padding: 2.25rem 1.5rem 2rem;
  text-align: center;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 720px;
  height: 280px;
  background: radial-gradient(ellipse, rgba(200, 169, 110, 0.14) 0%, transparent 70%);
  pointer-events: none;
}

.page-header-inner {
  position: relative;
  max-width: 960px;
  margin: 0 auto;
}

.header-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.35rem 0.95rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.35);
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 1.5px;
  color: #f5d78e;
  margin-bottom: 0.85rem;
  backdrop-filter: blur(10px);
}

.page-title {
  font-size: 2.5rem;
  font-weight: 900;
  color: #f2e9e4;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
}

@media (min-width: 768px) {
  .page-title {
    font-size: 3.25rem;
  }
}

.page-subtitle {
  font-size: 1rem;
  color: #9a8c98;
  max-width: 580px;
  margin: 0 auto 1.75rem;
  line-height: 1.5;
}

/* Filters Panel */
.filters-panel {
  background: rgba(22, 26, 46, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 0.85rem 1.25rem;
  backdrop-filter: blur(16px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
}

.filters-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.search-input-wrap {
  flex: 1.5;
  min-width: 220px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 14px;
  font-size: 1.2rem;
  color: #c8a96e;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.7rem 2.2rem 0.7rem 2.8rem;
  border-radius: 12px;
  border: 1.5px solid rgba(200, 169, 110, 0.25);
  background: rgba(255, 255, 255, 0.05);
  color: #f2e9e4;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.25s ease;
}

.search-input::placeholder {
  color: #6c7293;
}

.search-input:focus {
  border-color: #c8a96e;
  background: rgba(255, 255, 255, 0.08);
  box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.18);
}

.clear-search-btn {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  color: #9a8c98;
  cursor: pointer;
}

.clear-search-btn:hover {
  color: #f5d78e;
}

.filter-tabs {
  display: inline-flex;
  background: rgba(12, 15, 28, 0.7);
  padding: 4px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.filter-tab {
  padding: 0.55rem 0.95rem;
  font-size: 0.82rem;
  font-weight: 700;
  color: #9a8c98;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-tab:hover {
  color: #f2e9e4;
}

.filter-tab.active {
  background: rgba(200, 169, 110, 0.22);
  color: #f5d78e;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.btn-print-all {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.65rem 1.15rem;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #f2e9e4;
  font-size: 0.85rem;
  font-weight: 700;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-print-all:hover {
  background: rgba(200, 169, 110, 0.18);
  border-color: #c8a96e;
  color: #f5d78e;
}

/* ─── STATES: LOADING / EMPTY / ERROR ──────────────────────────────────────── */
.state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 5rem 1rem;
  gap: 1.25rem;
}

.loading-spinner {
  width: 52px;
  height: 52px;
  border: 4px solid rgba(200, 169, 110, 0.2);
  border-top-color: #c8a96e;
  border-radius: 50%;
  animation: spin 0.85s linear infinite;
}

.loading-spinner-sm {
  width: 28px;
  height: 28px;
  border: 3px solid rgba(200, 169, 110, 0.2);
  border-top-color: #c8a96e;
  border-radius: 50%;
  animation: spin 0.85s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.state-text {
  color: #9a8c98;
  font-size: 0.95rem;
}

.auth-required-card {
  max-width: 460px;
  width: 100%;
  padding: 2.5rem 2rem;
  text-align: center;
  background: rgba(22, 26, 46, 0.85);
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 24px;
  backdrop-filter: blur(14px);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
}

.auth-icon-wrap {
  width: 68px;
  height: 68px;
  margin: 0 auto 1.25rem;
  background: rgba(200, 169, 110, 0.14);
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.2rem;
  color: #f5d78e;
}

.auth-card-title {
  font-size: 1.45rem;
  font-weight: 800;
  color: #f2e9e4;
  margin-bottom: 0.6rem;
}

.auth-card-desc {
  color: #c9ada7;
  font-size: 0.92rem;
  line-height: 1.5;
  margin-bottom: 1.75rem;
}

.error-container {
  max-width: 440px;
  margin: 4rem auto;
  text-align: center;
  padding: 2rem;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 20px;
}

.error-icon {
  font-size: 3rem;
  color: #f87171;
  display: block;
  margin-bottom: 0.75rem;
}

.error-text {
  color: #fca5a5;
  font-size: 0.95rem;
}

.empty-container {
  text-align: center;
  padding: 5rem 1rem;
  max-width: 520px;
  margin: 0 auto;
}

.empty-icon-box {
  width: 84px;
  height: 84px;
  margin: 0 auto 1.25rem;
  background: rgba(200, 169, 110, 0.12);
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.6rem;
  color: #c8a96e;
}

.empty-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #f2e9e4;
  margin-bottom: 0.5rem;
}

.empty-desc {
  color: #9a8c98;
  font-size: 0.92rem;
  margin-bottom: 1.6rem;
  line-height: 1.5;
}

.btn-action-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.8rem 1.6rem;
  background: linear-gradient(135deg, #f5d78e 0%, #c8a96e 100%);
  color: #121526;
  font-weight: 800;
  font-size: 0.92rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 16px rgba(200, 169, 110, 0.35);
}

.btn-action-gold:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(200, 169, 110, 0.5);
  background: linear-gradient(135deg, #fff0b8 0%, #d4b679 100%);
}

/* ─── TICKETS LIST WRAPPER ─────────────────────────────────────────────────── */
.tickets-list-wrapper {
  max-width: 1040px;
  margin: 0 auto;
  padding: 0.5rem 1.25rem 4rem;
}

.tickets-count-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  color: #9a8c98;
  font-size: 0.88rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.security-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  color: #c8a96e;
  background: rgba(200, 169, 110, 0.1);
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  border: 1px solid rgba(200, 169, 110, 0.25);
}

.tickets-stack {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

/* ─── CINEMA PASS / TICKET CARD (AUTHENTIC TICKET STUB) ───────────────────── */
.ticket-pass-card {
  position: relative;
  background: rgba(20, 24, 46, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 22px;
  display: flex;
  flex-direction: row;
  overflow: hidden;
  backdrop-filter: blur(16px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.45);
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
              box-shadow 0.3s ease,
              border-color 0.3s ease;
  animation: ticketEnter 0.5s ease-out both;
}

@keyframes ticketEnter {
  from {
    opacity: 0;
    transform: translateY(22px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.ticket-pass-card:hover {
  transform: translateY(-5px);
  border-color: rgba(200, 169, 110, 0.4);
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.55),
              0 0 25px rgba(200, 169, 110, 0.12);
}

/* MAIN TICKET SECTION (Left) */
.ticket-main-section {
  flex: 1;
  display: flex;
  padding: 1.35rem;
  gap: 1.35rem;
}

/* Poster */
.ticket-poster-wrap {
  position: relative;
  width: 120px;
  height: 180px;
  border-radius: 14px;
  overflow: hidden;
  flex-shrink: 0;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
  background: #0d1020;
}

.ticket-poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  transition: transform 0.4s ease;
}

.ticket-pass-card:hover .ticket-poster-img {
  transform: scale(1.05);
}

.poster-overlay-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(12, 15, 28, 0.8) 0%, transparent 60%);
  pointer-events: none;
}

.poster-rating-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  display: inline-flex;
  align-items: center;
  gap: 2px;
  background: rgba(12, 15, 28, 0.85);
  border: 1px solid rgba(200, 169, 110, 0.4);
  color: #f5d78e;
  font-size: 0.72rem;
  font-weight: 800;
  padding: 0.2rem 0.5rem;
  border-radius: 9999px;
  backdrop-filter: blur(6px);
}

/* Details */
.ticket-details-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.ticket-header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 0.85rem;
}

.ticket-category-tag {
  display: block;
  font-size: 0.72rem;
  font-weight: 800;
  color: #c8a96e;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 0.25rem;
}

.ticket-movie-title {
  font-size: 1.35rem;
  font-weight: 800;
  color: #f2e9e4;
  line-height: 1.3;
  margin: 0;
}

.ticket-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.25rem 0.75rem;
  background: rgba(34, 197, 94, 0.16);
  border: 1px solid rgba(74, 222, 128, 0.35);
  color: #4ade80;
  font-size: 0.72rem;
  font-weight: 800;
  border-radius: 9999px;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.pulse-indicator {
  width: 6px;
  height: 6px;
  background: #4ade80;
  border-radius: 50%;
  box-shadow: 0 0 8px #4ade80;
  animation: pulseDot 2s infinite;
}

@keyframes pulseDot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.85); }
}

/* Info Grid */
.ticket-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem 1.25rem;
  padding: 0.75rem 1rem;
  background: rgba(12, 15, 28, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  margin-bottom: 0.85rem;
}

@media (min-width: 680px) {
  .ticket-info-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.info-cell {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.info-label {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  color: #9a8c98;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.info-label .mdi {
  color: #c8a96e;
  font-size: 0.85rem;
}

.info-value {
  font-size: 0.92rem;
  font-weight: 700;
  color: #f2e9e4;
}

.highlight-gold {
  color: #f5d78e;
}

.sub-label {
  display: block;
  font-size: 0.7rem;
  font-weight: 500;
  color: #6c7293;
}

.seat-value {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}

.seat-type-pill {
  font-size: 0.65rem;
  padding: 0.15rem 0.45rem;
  border-radius: 4px;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.seat-type-pill.vip {
  background: rgba(147, 51, 234, 0.25);
  color: #d8b4fe;
  border: 1px solid rgba(168, 85, 247, 0.4);
}

.seat-type-pill.standard {
  background: rgba(200, 169, 110, 0.2);
  color: #f5d78e;
  border: 1px solid rgba(200, 169, 110, 0.3);
}

/* Bottom Row */
.ticket-bottom-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding-top: 0.4rem;
}

.meta-payment-info {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.price-chip {
  padding: 0.25rem 0.65rem;
  background: rgba(200, 169, 110, 0.16);
  border: 1px solid rgba(200, 169, 110, 0.35);
  color: #f5d78e;
  font-size: 0.85rem;
  font-weight: 800;
  border-radius: 8px;
}

.payment-method-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.78rem;
  color: #9a8c98;
}

.date-purchased {
  font-size: 0.75rem;
  color: #6c7293;
}

.confirmation-code-wrap {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: rgba(12, 15, 28, 0.6);
  padding: 0.25rem 0.65rem;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.code-label {
  font-size: 0.72rem;
  color: #9a8c98;
}

.code-value {
  font-family: monospace;
  font-weight: 700;
  font-size: 0.8rem;
  color: #f5d78e;
}

.btn-copy-code {
  background: transparent;
  border: none;
  color: #9a8c98;
  cursor: pointer;
  padding: 2px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.btn-copy-code:hover {
  color: #f5d78e;
}

/* ─── PERFORATED TEAR-LINE DIVIDER ─────────────────────────────────────────── */
.ticket-perforated-divider {
  position: relative;
  width: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  background: transparent;
}

.perforated-line {
  position: absolute;
  top: 18px;
  bottom: 18px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  border-left: 2px dashed rgba(200, 169, 110, 0.35);
}

.notch {
  width: 22px;
  height: 22px;
  background: #0c0f1c; /* Match deep page background for cutout illusion */
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.08);
  z-index: 2;
}

.top-notch {
  margin-top: -11px;
}

.bottom-notch {
  margin-bottom: -11px;
}

/* ─── STUB / QR PASS SECTION (Right) ───────────────────────────────────────── */
.ticket-stub-section {
  width: 230px;
  background: rgba(15, 18, 36, 0.75);
  padding: 1.35rem 1.15rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  text-align: center;
  flex-shrink: 0;
}

.stub-header {
  margin-bottom: 0.65rem;
}

.stub-title {
  display: block;
  font-size: 0.75rem;
  font-weight: 900;
  color: #f5d78e;
  letter-spacing: 1.5px;
}

.stub-subtitle {
  display: block;
  font-size: 0.7rem;
  color: #9a8c98;
}

/* QR Box */
.qr-code-box {
  position: relative;
  width: 140px;
  height: 140px;
  padding: 8px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.qr-code-box:hover {
  transform: scale(1.04);
  box-shadow: 0 12px 30px rgba(200, 169, 110, 0.35);
}

.qr-code-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.qr-loading-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.qr-hover-hint {
  position: absolute;
  inset: 0;
  background: rgba(12, 15, 28, 0.85);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  color: #f5d78e;
  font-size: 0.78rem;
  font-weight: 700;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.qr-code-box:hover .qr-hover-hint {
  opacity: 1;
}

.qr-hover-hint .mdi {
  font-size: 1.4rem;
}

/* Stub Footer */
.stub-footer {
  width: 100%;
  margin-top: 0.75rem;
}

.stub-ticket-id {
  display: block;
  font-family: monospace;
  font-size: 0.72rem;
  font-weight: 700;
  color: #c8a96e;
  margin-bottom: 0.65rem;
}

.stub-actions {
  display: flex;
  gap: 0.4rem;
  width: 100%;
}

.btn-stub-action {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.5rem 0.4rem;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.14);
  color: #f2e9e4;
  font-size: 0.75rem;
  font-weight: 700;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-stub-action:hover {
  background: rgba(200, 169, 110, 0.2);
  border-color: #c8a96e;
  color: #f5d78e;
}

/* ─── MODAL ZOOM QR ────────────────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 100;
  background: rgba(0, 0, 0, 0.82);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.25rem;
}

.qr-modal-card {
  position: relative;
  width: 100%;
  max-width: 440px;
  background: #121528;
  border: 1.5px solid rgba(200, 169, 110, 0.4);
  border-radius: 26px;
  padding: 2.25rem 2rem;
  text-align: center;
  box-shadow: 0 25px 65px rgba(0, 0, 0, 0.8),
              0 0 35px rgba(200, 169, 110, 0.2);
  animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(16px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.btn-modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  color: #f2e9e4;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-modal-close:hover {
  background: rgba(239, 68, 68, 0.25);
  border-color: #f87171;
  color: #f87171;
}

.modal-brand {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 1px;
  color: #c8a96e;
  text-transform: uppercase;
  margin-bottom: 0.5rem;
}

.modal-movie-title {
  font-size: 1.5rem;
  font-weight: 900;
  color: #f2e9e4;
  margin-bottom: 0.4rem;
  line-height: 1.25;
}

.modal-session-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: #9a8c98;
  margin-bottom: 1.4rem;
  flex-wrap: wrap;
}

.modal-qr-container {
  width: 220px;
  height: 220px;
  margin: 0 auto 1.4rem;
  padding: 12px;
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-qr-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.modal-code-box {
  background: rgba(255, 255, 255, 0.05);
  border: 1px dashed rgba(200, 169, 110, 0.3);
  border-radius: 12px;
  padding: 0.65rem 1rem;
  margin-bottom: 1.2rem;
}

.modal-scanner-hint {
  font-size: 0.8rem;
  color: #9a8c98;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  margin-bottom: 1.4rem;
}

.modal-scanner-hint .mdi {
  color: #c8a96e;
  font-size: 1rem;
}

/* ─── RESPONSIVE LAYOUT (Mobile / Tablet) ──────────────────────────────────── */
@media (max-width: 860px) {
  .ticket-pass-card {
    flex-direction: column;
  }

  .ticket-main-section {
    padding: 1.15rem;
    gap: 1rem;
  }

  .ticket-poster-wrap {
    width: 90px;
    height: 135px;
  }

  .ticket-movie-title {
    font-size: 1.15rem;
  }

  /* Transform vertical tear-line into horizontal */
  .ticket-perforated-divider {
    width: 100%;
    height: 22px;
    flex-direction: row;
  }

  .perforated-line {
    top: 50%;
    left: 18px;
    right: 18px;
    bottom: auto;
    width: auto;
    height: 0;
    transform: translateY(-50%);
    border-left: none;
    border-top: 2px dashed rgba(200, 169, 110, 0.35);
  }

  .notch {
    margin: 0;
  }

  .top-notch {
    margin-left: -11px;
  }

  .bottom-notch {
    margin-right: -11px;
  }

  /* Stub section at bottom */
  .ticket-stub-section {
    width: 100%;
    flex-direction: row;
    padding: 1.15rem;
    gap: 1.2rem;
  }

  .stub-header {
    text-align: left;
    margin-bottom: 0;
    flex: 1;
  }

  .qr-code-box {
    width: 100px;
    height: 100px;
    padding: 6px;
  }

  .stub-footer {
    margin-top: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }

  .stub-actions {
    flex-direction: column;
    width: auto;
  }
}

@media (max-width: 580px) {
  .page-header {
    padding: 1.5rem 1rem;
  }

  .page-title {
    font-size: 2rem;
  }

  .ticket-main-section {
    flex-direction: column;
  }

  .ticket-poster-wrap {
    width: 100%;
    height: 160px;
  }

  .ticket-stub-section {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .stub-footer {
    align-items: center;
    width: 100%;
  }

  .stub-actions {
    flex-direction: row;
    width: 100%;
  }

  .filters-row {
    flex-direction: column;
  }

  .search-input-wrap,
  .filter-tabs,
  .btn-print-all {
    width: 100%;
  }

  .filter-tabs {
    display: flex;
  }

  .filter-tab {
    flex: 1;
    text-align: center;
    padding: 0.5rem 0.25rem;
  }
}

/* ─── PRINT STYLES (@media print) ──────────────────────────────────────────── */
@media print {
  body,
  .cinema-app-root,
  .cinema-page-wrapper {
    background: #ffffff !important;
    color: #000000 !important;
  }

  .no-print {
    display: none !important;
  }

  .tickets-page-content {
    padding-top: 0 !important;
  }

  .ticket-pass-card {
    background: #ffffff !important;
    border: 2px solid #000000 !important;
    color: #000000 !important;
    box-shadow: none !important;
    page-break-inside: avoid;
    margin-bottom: 2rem;
  }

  .ticket-movie-title {
    color: #000000 !important;
  }

  .info-value,
  .highlight-gold,
  .code-value {
    color: #000000 !important;
  }

  .ticket-info-grid {
    background: #f3f4f6 !important;
    border-color: #d1d5db !important;
  }

  .ticket-stub-section {
    background: #f9fafb !important;
    border-left: 2px dashed #000000 !important;
  }

  .qr-code-box {
    border: 1px solid #000000 !important;
    box-shadow: none !important;
  }

  .notch {
    display: none !important;
  }
}
</style>
