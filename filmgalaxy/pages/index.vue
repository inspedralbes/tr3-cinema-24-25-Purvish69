<template>
  <v-app>
    <div class="min-h-screen bg-gradient-to-br from-primary via-secondary to-cream custom-bg">
      <!-- Navbar -->
      <Navbar />

      <main class="relative pt-16">
        <!-- Error Alert -->
        <div v-if="movieStore.error"
          class="fixed top-4 right-4 z-50 max-w-md bg-red-500/90 text-light py-36 rounded-lg backdrop-blur-sm animate-fade-in">
          <p class="font-medium">{{ movieStore.error }}</p>
        </div>

        <!-- Loading Overlay -->
        <div v-if="movieStore.loading"
          class="fixed inset-0 bg-primary/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-gold border-t-transparent"></div>
        </div>

        <!-- Featured Movies Carousel -->
        <section v-if="movieStore.featuredMovies.length > 0" class="relative">
          <div class="relative h-[420px] md:h-[600px]">
            <div v-for="(movie, index) in movieStore.featuredMovies" :key="movie.id"
              class="absolute inset-0 transition-opacity duration-700" :class="{ 'opacity-0': currentSlide !== index }">
              <div class="relative h-full">
                <img :src="movie.poster" :alt="movie.titulo" class="absolute inset-0 w-full h-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/50 to-transparent">
                  <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12 space-y-4">
                    <h2 class="text-3xl md:text-5xl font-bold text-light animate-fade-up">
                      {{ movie.titulo }}
                    </h2>

                    <div class="flex items-center space-x-4 animate-fade-up" style="animation-delay: 200ms">
                      <span class="text-gold text-xl md:text-2xl font-bold">
                        {{ movie.calificacion }}/10
                      </span>
                      <div class="flex">
                        <template v-for="n in 5" :key="n">
                          <svg v-if="n <= Math.floor(movie.calificacion / 2)" class="w-5 h-5 text-gold"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                          <svg v-else class="w-5 h-5 text-gold/40" fill="currentColor" viewBox="0 0 20 20">
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        </template>
                      </div>
                    </div>

                    <div class="flex space-x-4 animate-fade-up" style="animation-delay: 400ms">
                      <button class="btn-primary">
                        Comprar entrades
                      </button>
                      <button class="btn-secondary"
                        @click="movieStore.navigateToMovieDetails(movieStore.featuredMovies[currentSlide], router)">
                        Detalls
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Carousel Controls -->
          <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
            <button v-for="(_, index) in movieStore.featuredMovies" :key="index" @click="currentSlide = index"
              class="w-2 h-2 rounded-full transition-all duration-300"
              :class="currentSlide === index ? 'bg-gold w-8' : 'bg-light/50 hover:bg-light'"></button>
          </div>
        </section>

        <!-- Now Playing Movies -->
        <section class="container mx-auto px-4 py-12">
          <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h2 class="text-2xl md:text-4xl font-bold text-light mb-4 md:mb-0">
              Sessions de pel·lícula
            </h2>
            <button class="btn-primary" @click="$router.push('/')">
              Veure més sessions
            </button>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <div v-for="session in combinedSessions" :key="session.id"
              class="group relative bg-light/10 rounded-xl overflow-hidden backdrop-blur-sm transition-transform duration-300 hover:scale-105">
              <!-- Movie Poster -->
              <div class="relative aspect-[2/3]">
                <img :src="session.movie.imagen" :alt="session.movie.titulo" class="w-full h-full object-cover" />
                <div class="absolute top-4 right-4 bg-gold text-primary px-3 py-1 rounded-full text-sm font-medium">
                  {{ session.hora }}
                </div>
                <!-- Indicador d'estat (a punt d'acabar) -->
                <div v-if="isSessionEndingSoon(session)"
                  class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium animate-pulse">
                  Acaba aviat
                </div>
                <div
                  class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-light text-sm mb-2">
                      {{ formatDuration(session.movie.duracion) }} • {{ session.movie.genero }}
                    </p>
                    <p class="text-light text-sm">
                      {{ formatReleaseDate(session.fecha) }} • {{ getSessionTimeInfo(session) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Movie Info -->
              <div class="p-4 space-y-3">
                <h3 class="text-lg font-bold text-light line-clamp-2">
                  {{ session.movie.titulo }}
                </h3>
                <!-- Nova línia per mostrar dia, data i hora -->
                <p class="text-sm text-light">
                  {{ formatDateHeader(session.fecha) }} - {{ session.hora }}
                </p>
                <div class="flex gap-2">
                  <button @click="router.push(`/billets/${session.id}`)"
                    class="flex-1 bg-gold hover:bg-gold/80 text-primary font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300">
                    Comprar
                  </button>
                  <button
                    class="flex-1 bg-accent hover:bg-accent/80 text-light font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300"
                    @click="movieStore.navigateToMovieDetails(session.movie, router)">
                    Detalls
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Pròximament Info de Pel·lícules -->
        <section class="container mx-auto px-4 py-12">
          <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h2 class="text-2xl md:text-4xl font-bold text-light mb-4 md:mb-0">
              Pròximament
            </h2>
            <button class="btn-primary" @click="$router.push('/movies')">
              Veure més pel·lícules
            </button>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <div v-for="movie in movieStore.comingSoonMovies" :key="movie.id"
              class="group relative bg-light/10 rounded-xl overflow-hidden backdrop-blur-sm transition-transform duration-300 hover:scale-105">
              <!-- Movie Poster -->
              <div class="relative aspect-[2/3]">
                <img :src="movie.imagen" :alt="movie.titulo" class="w-full h-full object-cover" />

                <div class="absolute top-4 right-4 bg-gold text-primary px-3 py-1 rounded-full text-sm font-medium">
                  {{ formatReleaseDate(movie.fecha_estreno) }}
                </div>

                <div
                  class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-light text-sm">{{ movie.genero }}</p>
                  </div>
                </div>
              </div>

              <div class="p-4">
                <h3 class="text-lg font-bold text-light line-clamp-2">
                  {{ movie.titulo }}
                </h3>
                <button
                  class="w-full mt-3 bg-accent hover:bg-accent/80 text-light font-medium py-2 px-3 rounded-lg text-sm transition-colors duration-300"
                  @click="movieStore.navigateToMovieDetails(movie, router)">
                  Detalls
                </button>
              </div>
            </div>
          </div>
        </section>

        <Footer />
      </main>
    </div>
  </v-app>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMovieStore } from '~/stores/movieStore';
import { useSessionsStore } from '~/stores/sessionsStore';

const router = useRouter();
const movieStore = useMovieStore();
const sessionsStore = useSessionsStore();
const currentSlide = ref(0);

// Auto advance carousel
let carouselInterval;
onMounted(() => {
  movieStore.fetchMovies();
  sessionsStore.fetchSessions();
  startCarousel();
});

function startCarousel() {
  carouselInterval = setInterval(() => {
    if (movieStore.featuredMovies.length > 0) {
      currentSlide.value = (currentSlide.value + 1) % movieStore.featuredMovies.length;
    }
  }, 5000);
}

// Sessions imminents per als propers 4 dies
const combinedSessions = computed(() => {
  const now = new Date();
  const today = new Date(now);
  today.setHours(0, 0, 0, 0);

  // Inicialment buscar en els propers 4 dies
  let maxDate = new Date(today);
  maxDate.setDate(maxDate.getDate() + 4);

  let sessions = sessionsStore.availableSessions.filter(session => {
    // Convertir la data i hora de la sessió a un objecte Date
    const sessionDate = new Date(session.fecha);
    const sessionTime = session.hora.split(':');
    const sessionDateTime = new Date(sessionDate);
    sessionDateTime.setHours(
      parseInt(sessionTime[0]),
      parseInt(sessionTime[1]),
      0, 0
    );

    // Verificar si la sessió ja ha acabat
    const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
    const sessionEndTime = new Date(sessionDateTime);
    sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

    // Només mostrar sessions que encara no han acabat
    return sessionEndTime > now && sessionDate <= maxDate;
  });

  // Si hi ha menys de 4 sessions, augmentar el rang de dies fins a trobar almenys 4
  if (sessions.length < 4) {
    let extendedDays = 5; // Començar amb un dia addicional
    while (sessions.length < 4 && extendedDays <= 30) { // Limitar a 30 dies
      maxDate = new Date(today);
      maxDate.setDate(maxDate.getDate() + extendedDays);

      sessions = sessionsStore.availableSessions.filter(session => {
        const sessionDate = new Date(session.fecha);
        const sessionTime = session.hora.split(':');
        const sessionDateTime = new Date(sessionDate);
        sessionDateTime.setHours(
          parseInt(sessionTime[0]),
          parseInt(sessionTime[1]),
          0, 0
        );

        const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
        const sessionEndTime = new Date(sessionDateTime);
        sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

        return sessionEndTime > now && sessionDate <= maxDate;
      });

      extendedDays++;
    }
  }

  // Ordenar sessions primer per data i després per hora
  sessions.sort((a, b) => {
    const dateA = new Date(a.fecha);
    const dateB = new Date(b.fecha);

    // Si les dates són diferents, ordenar per data
    if (dateA.getTime() !== dateB.getTime()) {
      return dateA - dateB;
    }

    // Si les dates són iguals, ordenar per hora
    return a.hora.localeCompare(b.hora);
  });

  // Devolver màxim 8 sessions per mostrar
  return sessions.slice(0, 8);
});

// Comprovar periòdicament si hi ha sessions que han acabat i actualitzar la llista
const checkExpiredSessions = () => {
  const now = new Date();

  // Comptar quantes sessions han acabat
  const expiredSessionsCount = sessionsStore.sessions.filter(session => {
    const sessionDate = new Date(session.fecha);
    const sessionTime = session.hora.split(':');
    const sessionDateTime = new Date(sessionDate);
    sessionDateTime.setHours(
      parseInt(sessionTime[0]),
      parseInt(sessionTime[1]),
      0, 0
    );

    const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
    const sessionEndTime = new Date(sessionDateTime);
    sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

    return sessionEndTime <= now;
  }).length;

  // Si més del 50% de les sessions han acabat o no hi ha sessions visibles, recarregar
  if (expiredSessionsCount > sessionsStore.sessions.length / 2 || combinedSessions.value.length === 0) {
    sessionsStore.fetchSessions();
  }
};

// Configurar un interval per comprovar sessions acabades cada 5 minuts
let checkSessionsInterval;
onMounted(() => {
  movieStore.fetchMovies();
  sessionsStore.fetchSessions();
  startCarousel();

  // Comprovar periòdicament si hi ha sessions acabades (cada 5 minuts)
  checkSessionsInterval = setInterval(checkExpiredSessions, 300000);

  // També comprovar en carregar la pàgina
  checkExpiredSessions();
});

// Netejar intervals al desmuntar
onUnmounted(() => {
  if (carouselInterval) clearInterval(carouselInterval);
  if (checkSessionsInterval) clearInterval(checkSessionsInterval);
});

// Helpers per formatejar dates i durada
const formatReleaseDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('ca-ES', {
    day: 'numeric', month: 'short'
  });
};

const formatDateHeader = (dateString) => {
  // La data ve en format "yyyy-mmm-dd" (ex: "2025-mar.-18")
  const date = new Date(dateString);

  // Verificar si és avui, demà o passat demà
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);

  const dayAfterTomorrow = new Date(today);
  dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);

  // Convertir a dates sense hora per comparar
  const dateOnly = new Date(date);
  dateOnly.setHours(0, 0, 0, 0);

  if (dateOnly.getTime() === today.getTime()) {
    return 'Avui - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'long', day: 'numeric', month: 'long'
    });
  } else if (dateOnly.getTime() === tomorrow.getTime()) {
    return 'Demà - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'long', day: 'numeric', month: 'long'
    });
  } else if (dateOnly.getTime() === dayAfterTomorrow.getTime()) {
    return 'Passat demà - ' + date.toLocaleDateString('ca-ES', {
      weekday: 'long', day: 'numeric', month: 'long'
    });
  } else {
    return date.toLocaleDateString('ca-ES', {
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
  }
};

const formatDuration = (minutes) => {
  if (!minutes) return '2h 00min';
  let mins = typeof minutes === 'string' ? parseInt(minutes) : minutes;
  if (isNaN(mins)) return '2h 00min';
  const hours = Math.floor(mins / 60);
  const remainingMins = mins % 60;
  return `${hours}h ${remainingMins.toString().padStart(2, '0')}min`;
};

// Indicar si una sessió està a punt d'acabar
const isSessionEndingSoon = (session) => {
  const now = new Date();
  const sessionDate = new Date(session.fecha);
  const sessionTime = session.hora.split(':');
  const sessionDateTime = new Date(sessionDate);
  sessionDateTime.setHours(
    parseInt(sessionTime[0]),
    parseInt(sessionTime[1]),
    0, 0
  );

  const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
  const sessionEndTime = new Date(sessionDateTime);
  sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

  const timeDiff = (sessionEndTime - now) / 1000 / 60; // minuts

  return timeDiff < 30; // menys de 30 minuts per acabar
};

// Indicar si una sessió està a punt de començar
const isSessionStartingSoon = (session) => {
  const now = new Date();
  const sessionDate = new Date(session.fecha);
  const sessionTime = session.hora.split(':');
  const sessionDateTime = new Date(sessionDate);
  sessionDateTime.setHours(
    parseInt(sessionTime[0]),
    parseInt(sessionTime[1]),
    0, 0
  );

  const timeDiff = (sessionDateTime - now) / 1000 / 60; // minuts

  return timeDiff < 30; // menys de 30 minuts per començar
};

// Obtenir informació del temps restant per a una sessió
const getSessionTimeInfo = (session) => {
  const now = new Date();
  const sessionDate = new Date(session.fecha);
  const sessionTime = session.hora.split(':');
  const sessionDateTime = new Date(sessionDate);
  sessionDateTime.setHours(
    parseInt(sessionTime[0]),
    parseInt(sessionTime[1]),
    0, 0
  );

  const movieDurationMinutes = session.movie?.duracion ? parseInt(session.movie.duracion) : 120;
  const sessionEndTime = new Date(sessionDateTime);
  sessionEndTime.setMinutes(sessionEndTime.getMinutes() + movieDurationMinutes);

  // Si la sessió ja ha començat
  if (now > sessionDateTime) {
    const timeDiff = (sessionEndTime - now) / 1000 / 60; // minuts fins al final
    if (timeDiff <= 0) {
      return `Sessió finalitzada`;
    } else if (timeDiff < 30) {
      return `Acaba en ${Math.floor(timeDiff)} min`;
    } else {
      return `En curs (${Math.floor(timeDiff)} min restants)`;
    }
  } else {
    // Si la sessió no ha començat
    const timeDiff = (sessionDateTime - now) / 1000 / 60; // minuts fins a l'inici
    if (timeDiff < 60) {
      return `Comença en ${Math.floor(timeDiff)} min`;
    } else if (timeDiff < 1440) { // menys de 24 hores
      return `Comença a les ${session.hora}`;
    } else {
      return `${formatReleaseDate(session.fecha)} a les ${session.hora}`;
    }
  }
};
</script>

<style>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}

/* Animació per fade-up */
@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-up {
  animation: fade-up 0.6s ease-out forwards;
}

/* Animació per fade-in */
@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}

/* Animació per pulse-subtle */
@keyframes pulse-subtle {
  from {
    transform: scale(1);
  }

  to {
    transform: scale(1.05);
  }
}

.animate-pulse-subtle {
  animation: pulse-subtle 1s ease-out infinite;
}

/* Barra de desplaçament personalitzada */
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #1a202c;
}

::-webkit-scrollbar-thumb {
  background: #d4af37;
  border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(212, 175, 55, 0.8);
}

* {
  scrollbar-width: thin;
  scrollbar-color: #d4af37 #1a202c;
}

/* Truncament de text en 2 línies */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
