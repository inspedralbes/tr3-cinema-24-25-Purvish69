<template>
  <v-app>
    <!-- NavBar -->
    <Navbar />
    <v-main class="pa-0 ma-0 backgroundIndex">
      <v-container fluid class="pa-0 ma-0">
        <!-- Hero Section with Featured Movies -->
        <v-row no-gutters>
          <v-col cols="12">
            <v-carousel cycle height="600" hide-delimiter-background show-arrows="hover" class="featured-movies">
              <v-carousel-item v-for="movie in featuredMovies" :key="movie.id" :src="movie.imagen" cover>
                <div class="featured-overlay">
                  <h2 class="text-h2 font-weight-bold mb-2">{{ movie.titulo }}</h2>
                  <p class="text-h6 mb-6 font-weight-regular">{{ movie.descripcion }}</p>
                  <v-btn color="gold" size="x-large" class="text-dark font-weight-bold px-8" elevation="4">
                    Comprar Entradas
                  </v-btn>
                </div>
              </v-carousel-item>
            </v-carousel>
          </v-col>
        </v-row>

        <!-- Now Playing Section -->
        <v-row class="py-12 px-4">
          <v-col cols="12">
            <h2 class="text-h3 mb-8 text-dark text-center font-weight-bold">Películas de Hoy</h2>
            <v-row>
              <v-col v-for="movie in nowPlayingMovies" :key="movie.id" cols="12" sm="6" md="3">
                <v-card class="mx-auto movie-card" color="white" elevation="8">
                  <v-img :src="movie.imagen" height="400" cover class="movie-poster">
                    <template v-slot:placeholder>
                      <v-row class="fill-height ma-0" align="center" justify="center">
                        <v-progress-circular indeterminate color="gold"></v-progress-circular>
                      </v-row>
                    </template>
                  </v-img>
                  <v-card-title class="text-dark pt-4 text-h5">{{ movie.titulo }}</v-card-title>
                  <v-card-text class="text-accent pb-0">
                    {{ formatDuration(movie.duracion) }} • {{ movie.genero }}
                  </v-card-text>
                  <v-card-actions class="pa-4">
                    <v-btn color="gold" variant="elevated" block size="large" class="text-dark font-weight-bold">
                      Comprar Entradas
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <!-- Coming Soon Section -->
        <v-row class="py-12 px-4">
          <v-col cols="12">
            <h2 class="text-h3 mb-8 text-dark text-center font-weight-bold">Próximamente</h2>
            <v-row>
              <v-col v-for="movie in comingSoonMovies" :key="movie.id" cols="12" sm="6" md="3">
                <v-card class="mx-auto movie-card" color="white" elevation="8">
                  <v-img :src="movie.imagen" height="400" cover class="movie-poster">
                    <div class="release-date-badge">
                      {{ formatReleaseDate(movie.fecha_estreno) }}
                    </div>
                  </v-img>
                  <v-card-title class="text-dark pt-4 text-h5">{{ movie.titulo }}</v-card-title>
                  <v-card-text class="text-accent">
                    {{ movie.genero }}
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <Footer />

      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';
import { useAuth } from '../composables/communicationManagerPelicula';

const featuredMovies = ref([]);
const nowPlayingMovies = ref([]);
const comingSoonMovies = ref([]);
const allMovies = ref([]);

// Formatear fecha de estreno
const formatReleaseDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Cargar películas desde la API
const loadMovies = async () => {
  try {
    const auth = useAuth();
    const response = await auth.getPeliculas();
    
    if (response && !response.error && response.movies) {
      allMovies.value = response.movies;
      console.log('Películas cargadas:', allMovies.value);
      
      // Separar películas en categorías
      categorizeMovies();
    } else {
      console.error('Error al cargar películas:', response?.error || 'No se encontraron películas');
    }
  } catch (error) {
    console.error('Error en la carga de películas:', error);
  }
};

// Categorizar películas en destacadas, en cartelera y próximamente
const categorizeMovies = () => {
  if (!allMovies.value || allMovies.value.length === 0) {
    console.error('No hay películas para categorizar');
    return;
  }
  
  // Convertir string JSON de calificación a número si es necesario
  const processedMovies = allMovies.value.map(movie => {
    let rating = movie.calificacion;
    if (typeof rating === 'string') {
      try {
        rating = parseFloat(rating);
      } catch (e) {
        rating = 0;
      }
    }
    return { ...movie, calificacionNum: rating || 0 };
  });
  
  // Seleccionar películas destacadas (las 3 con mejores ratings)
  featuredMovies.value = [...processedMovies]
    .sort((a, b) => b.calificacionNum - a.calificacionNum)
    .slice(0, 3);
  
  const currentDate = new Date();
  
  // Películas en cartelera (con fecha de estreno pasada o reciente)
  nowPlayingMovies.value = processedMovies
    .filter(movie => {
      if (!movie.fecha_estreno) return true; // Incluir películas sin fecha como en cartelera
      
      const releaseDate = new Date(movie.fecha_estreno);
      if (isNaN(releaseDate.getTime())) return true; // Fecha inválida, mostrar como en cartelera
      
      const diffTime = currentDate - releaseDate;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays >= 0 && diffDays <= 30; // Películas estrenadas en el último mes
    })
    .slice(0, 4);
  
  // Películas próximamente (con fecha de estreno futura)
  comingSoonMovies.value = processedMovies
    .filter(movie => {
      if (!movie.fecha_estreno) return false;
      
      const releaseDate = new Date(movie.fecha_estreno);
      if (isNaN(releaseDate.getTime())) return false;
      
      return releaseDate > currentDate;
    })
    .sort((a, b) => new Date(a.fecha_estreno) - new Date(b.fecha_estreno)) // Ordenar por fecha más cercana
    .slice(0, 4);
  
  // Si no hay suficientes películas próximas, llenar con las restantes
  if (comingSoonMovies.value.length < 4) {
    const remainingMovies = processedMovies
      .filter(movie => !featuredMovies.value.some(fm => fm.id === movie.id) && 
                      !nowPlayingMovies.value.some(npm => npm.id === movie.id) &&
                      !comingSoonMovies.value.some(csm => csm.id === movie.id))
      .slice(0, 4 - comingSoonMovies.value.length);
    
    comingSoonMovies.value = [...comingSoonMovies.value, ...remainingMovies];
  }
  
  console.log('Categorización completada:', {
    featured: featuredMovies.value.length,
    nowPlaying: nowPlayingMovies.value.length,
    comingSoon: comingSoonMovies.value.length
  });
};

// Formatear duración
const formatDuration = (minutes) => {
  if (!minutes) return '2h 00min'; // Valor por defecto
  
  // Convertir string a número si es necesario
  let mins = minutes;
  if (typeof minutes === 'string') {
    mins = parseInt(minutes);
  }
  
  if (isNaN(mins)) return '2h 00min';
  
  const hours = Math.floor(mins / 60);
  const remainingMins = mins % 60;
  return `${hours}h ${remainingMins}min`;
};

// Cargar películas al montar el componente
onMounted(() => {
  loadMovies();
});
</script>

<style>
/* Background Colors */
.white-bg {
  background-color: white;
}

.backgroundIndex{
  /* background-color: #EAE0D5 ; */
  background: linear-gradient(135deg, #22223B 0%, #EAE0D5 100%);
}

.rojo-bg {
  background-color: #f8d7da; /* Esto es un rojo claro, cámbialo por el color que necesites */
}

/* Featured Movies Carousel */
.featured-movies .featured-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 4rem;
  background: linear-gradient(transparent, rgba(28, 28, 28, 0.95));
  color: var(--v-light-base);
}

/* Remove any spacing between Navbar and content */
.v-application {
  margin: 0 !important;
  padding: 0 !important;
}

.v-main {
  padding: 0 !important;
  margin: 0 !important;
}

/* Movie Card Styles */
.movie-card {
  transition: all 0.3s ease;
  overflow: hidden;
  border-radius: 12px;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3) !important;
}

.movie-card .movie-poster {
  transition: transform 0.3s ease;
}

.movie-card:hover .movie-poster {
  transform: scale(1.05);
}

/* Release Date Badge */
.movie-card .release-date-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background-color: var(--v-gold-base);
  color: var(--v-dark-base);
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>