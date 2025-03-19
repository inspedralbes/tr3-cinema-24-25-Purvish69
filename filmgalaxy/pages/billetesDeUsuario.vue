<template>
  <v-app>
    <Navbar />
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-dark custom-bg pt-16">
      <div class="container mx-auto px-4 py-8">
        <!-- Loading state -->
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="max-w-md mx-auto bg-red-500/80 text-light p-6 rounded-xl backdrop-blur-sm">
          <p class="text-center">{{ error }}</p>
        </div>

        <!-- No tickets -->
        <div v-else-if="!tickets.length" class="max-w-md mx-auto glass-effect text-light p-8 rounded-xl text-center">
          <h2 class="text-2xl font-bold text-gold mb-4">No tienes entradas</h2>
          <p class="mb-6">¡Compra tus entradas ahora y disfruta del mejor cine!</p>
          <button @click="router.push('/movies')" class="bg-gold text-dark px-6 py-2 rounded-full hover:bg-gold/80">
            Ver películas
          </button>
        </div>

        <!-- Tickets list -->
        <div v-else class="max-w-4xl mx-auto">
          <h1 class="text-3xl font-bold text-gold mb-8 text-center">Mis Entradas</h1>
          
          <div class="space-y-6">
            <div v-for="ticket in tickets" :key="ticket.id" class="glass-effect rounded-xl overflow-hidden">
              <div class="md:flex">
                <!-- Movie Poster -->
                <div class="md:w-1/4">
                  <img 
                    :src="ticket.movie?.imagen || 'https://via.placeholder.com/300x450?text=No+Image'" 
                    :alt="ticket.movie?.titulo || 'Película'"
                    class="w-full h-full object-cover"
                  />
                </div>

                <!-- Ticket Details -->
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
                      <span>{{ formatDate(ticket.session?.fecha) }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ ticket.session?.hora || '-- : --' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span>Sala {{ ticket.session?.sala || '-' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Asiento {{ ticket.seat?.fila || '-' }}{{ ticket.seat?.numero || '-' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                      </svg>
                      <span>{{ ticket.payment?.payment_method || 'Método de pago no disponible' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ ticket.precio || '-' }}€</span>
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '~/composables/useAuth';
import { usePeliculas } from '~/composables/communicationManagerPelicula';

const router = useRouter();
const { userId, isAuthenticated } = useAuth();
const { getUserTickets, getTickets } = usePeliculas();

const loading = ref(true);
const error = ref(null);
const tickets = ref([]);

// Format date to a more readable format
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  try {
    return new Date(dateString).toLocaleDateString('es-ES', options);
  } catch (error) {
    console.error('Error al formatear la fecha:', error);
    return dateString;
  }
}

onMounted(async () => {
  try {
    loading.value = true;
    
    // Check if user is authenticated
    if (!isAuthenticated.value) {
      router.push('/login?redirect=/billetesDeUsuario');
      return;
    }
    
    // Get user ID
    const currentUserId = userId.value || 1;
    
    // Fetch tickets
    let userTickets;
    try {
      userTickets = await getUserTickets(currentUserId);
    } catch (err) {
      console.warn('Error fetching user tickets, falling back to all tickets:', err);
      const allTickets = await getTickets();
      userTickets = allTickets.filter(ticket => ticket.user_id === currentUserId);
    }
    
    tickets.value = userTickets;
    
    // Process tickets to add movie and session data if needed
    tickets.value = await Promise.all(tickets.value.map(async (ticket) => {
      // Add additional data if needed
      return ticket;
    }));
    
  } catch (e) {
    error.value = 'Error al cargar tus tickets: ' + e.message;
    console.error(e);
  } finally {
    loading.value = false;
  }
});
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #4A4E69 100%);
}

.glass-effect {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}
</style>