<template>
  <v-app class="bg-midnight min-h-screen">
    <Navbar />
    <div class="main-background min-h-[calc(100vh-64px)] pt-8">
      <div class="container mx-auto px-4">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center min-h-[60vh]">
          <div class="loading-spinner"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="max-w-md mx-auto bg-carbon/80 text-ivory p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ error }}</p>
          <button @click="router.push('/movies')" class="action-button mt-4 w-full">
            Veure pel·lícules
          </button>
        </div>

       <!-- No Tickets State -->
       <div v-else-if="!tickets.length" class="min-h-[80vh] flex items-center justify-center">
          <div class="max-w-md w-full ticket-card p-8 rounded-xl text-center">
            <div class="py-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gold mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
              </svg>
              <h2 class="text-2xl font-bold text-gold mb-3">No tens entrades</h2>
              <p class="mb-6 text-ivory">Compra les teves entrades ara i gaudeix del millor cinema!</p>
              <button @click="router.push('/moviSession')" class="action-button">
                Veure pel·lícules
              </button>
            </div>
          </div>
        </div>

        <!-- Tickets List -->
        <div v-else class="max-w-4xl mx-auto mt-16">
          <h1 class="text-5xl font-bold text-gold mb-8 text-center">Les Meves Entrades</h1>
          <div class="space-y-4">
            <div v-for="ticket in tickets" :key="ticket.id" class="ticket-card rounded-xl overflow-hidden">
              <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                  <h2 class="text-2xl font-bold text-ivory">
                    {{ ticket.movie_session?.movie?.titulo || ticket.movie?.titulo || 'Película sense títol' }}
                  </h2>
                  <span class="status-badge">
                    Confirmat
                  </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-ivory mb-6">
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ formatDayAndDate(ticket.movie_session?.fecha || ticket.session?.fecha) }}</span>
                  </div>
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ ticket.movie_session?.hora || ticket.session?.hora }}</span>
                  </div>
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Sala {{ ticket.movie_session?.sala || ticket.session?.sala || 1 }}</span>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-ivory">
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Seient {{ ticket.seat?.fila }}{{ ticket.seat?.numero }}</span>
                  </div>
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <span>{{ ticket.payment?.payment_method || 'Targeta' }}</span>
                  </div>
                  <div class="ticket-info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ ticket.precio }}€</span>
                  </div>
                </div>

                <div class="ticket-footer">
                  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                    <div class="text-sm text-ivory/70">
                      Comprat el {{ formatDate(ticket.created_at) }}
                    </div>
                    <div class="text-sm text-gold font-medium">
                      Codi: {{ ticket.codigo_confirmacion ? ticket.codigo_confirmacion.substring(0, 8) : 'N/D' }}
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

const handleImageError = (event, ticket) => {
  event.target.src = 'https://via.placeholder.com/300x450?text=No+Image';
  event.target.onerror = null;

  if (ticket.movie_session?.movie) {
    ticket.movie_session.movie.imagen = 'https://via.placeholder.com/300x450?text=No+Image';
  }
  if (ticket.movie) {
    ticket.movie.imagen = 'https://via.placeholder.com/300x450?text=No+Image';
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'Data no disponible';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Data no disponible';
    }
    return date.toLocaleDateString('ca-ES', options);
  } catch (e) {
    console.error('Error al formatear la fecha:', e);
    return 'Data no disponible';
  }
}

const formatDayAndDate = (dateString) => {
  if (!dateString) return 'Data no disponible';
  const options = { weekday: 'long', day: 'numeric', month: 'long' };
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Data no disponible';
    }
    return date.toLocaleDateString('ca-ES', options);
  } catch (e) {
    console.error('Error al formatear el día y fecha:', e);
    return 'Data no disponible';
  }
};

const getCurrentUserId = computed(() => {
  if (userId.value) return userId.value;

  if (typeof window !== 'undefined') {
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
    return storedUser.id;
  }

  return null;
});

onMounted(async () => {
  if (!isAuthenticated.value) {
    error.value = 'Has d\'iniciar sessió per veure les teves entrades';
    loading.value = false;
    return;
  }

  try {
    const userIdValue = getCurrentUserId.value;

    if (!userIdValue) {
      error.value = 'No s\'ha pogut identificar l\'usuari';
      loading.value = false;
      return;
    }

    const allTickets = await getTickets();

    if (!allTickets || allTickets.error) {
      console.error('Error al obtener tickets:', allTickets?.error);
      error.value = 'Error al carregar les teves entrades. Si us plau, torna-ho a provar més tard.';
      loading.value = false;
      return;
    }

    let userTickets = allTickets.filter(ticket => ticket.user_id === userIdValue);

    if (!userTickets.length) {
      loading.value = false;
      return;
    }

    userTickets = userTickets.map(ticket => {
      const enhancedTicket = { ...ticket };

      if (!enhancedTicket.movie_session && enhancedTicket.movieSession_id) {
        enhancedTicket.movie_session = {
          id: enhancedTicket.movieSession_id,
          fecha: enhancedTicket.session?.fecha || new Date().toISOString().split('T')[0],
          hora: enhancedTicket.session?.hora || '19:00',
          sala: enhancedTicket.session?.sala || '1'
        };
      }

      if (enhancedTicket.movie_session && !enhancedTicket.movie_session.movie) {
        enhancedTicket.movie_session.movie = {};
      }

      return enhancedTicket;
    });

    tickets.value = userTickets;
  } catch (err) {
    console.error('Error al carregar tickets:', err);
    error.value = 'Error al carregar les teves entrades. Si us plau, torna-ho a provar més tard.';
  } finally {
    loading.value = false;
  }
});
</script>

<style>
/* Variables */
:root {
  --color-midnight: #22223B;
  --color-greyBlue: #4A4E69;
  --color-lavender: #9A8C98;
  --color-oldRose: #C9ADA7;
  --color-beige: #F2E9E4;
  --color-carbon: #1C1C1C;
  --color-gold: #C8A96E;
  --color-ivory: #EAE0D5;
}

/* Utility Classes */
.bg-midnight {
  background-color: var(--color-midnight);
}

.text-gold {
  color: var(--color-gold);
}

.text-ivory {
  color: var(--color-ivory);
}

/* Main Background */
.main-background {
  background: linear-gradient(135deg, #22223B 10%, #9A8C98 100%);
}

/* Loading Spinner */
.loading-spinner {
  animation: spin 1s linear infinite;
  border: 4px solid var(--color-gold);
  border-top-color: transparent;
  border-radius: 50%;
  height: 3rem;
  width: 3rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Ticket Card */
.ticket-card {
  background: rgba(74, 78, 105, 0.2);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(200, 169, 110, 0.1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Status Badge */
.status-badge {
  background-color: rgba(200, 169, 110, 0.2);
  color: var(--color-gold);
  padding: 0.25rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Ticket Info Item */
.ticket-info-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* Ticket Footer */
.ticket-footer {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(234, 224, 213, 0.1);
}

/* Action Button */
.action-button {
  background-color: var(--color-gold);
  color: var(--color-carbon);
  padding: 0.75rem 2rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.action-button:hover {
  background-color: rgba(200, 169, 110, 0.9);
}
</style>