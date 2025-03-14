import { defineStore } from 'pinia';
import { usePeliculas } from '~/composables/communicationManagerPelicula';

export const useMovieStore = defineStore('movies', {
  state: () => ({
    allMovies: [],
    featuredMovies: [],
    nowPlayingMovies: [],
    comingSoonMovies: [],
    loading: false,
    error: null,
    currentMovie: null
  }),
  
  actions: {
    // Cargar películas desde la API
    async fetchMovies() {
      this.loading = true;
      this.error = null;
      
      try {
        const peliculasManager = usePeliculas();
        const response = await peliculasManager.getPeliculas();
        
        if (response && !response.error && response.movies) {
          this.allMovies = response.movies;
          this.categorizeMovies();
          console.log('Películas cargadas correctamente en el store');
        } else {
          this.error = response?.error || 'No se encontraron películas';
          console.error('Error al cargar películas:', this.error);
        }
      } catch (error) {
        this.error = 'Error en la carga de películas';
        console.error(this.error, error);
      } finally {
        this.loading = false;
      }
    },
    
    // Forzar recarga de películas (útil para desarrollo/pruebas)
    async reloadMovies() {
      console.log('Forzando recarga de películas');
      await this.fetchMovies();
    },
    
    // Categorizar películas
    categorizeMovies() {
      if (!this.allMovies || this.allMovies.length === 0) {
        console.error('No hay películas para categorizar');
        return;
      }
      
      // Convertir string JSON de calificación a número si es necesario
      const processedMovies = this.allMovies.map(movie => {
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
      this.featuredMovies = [...processedMovies]
        .sort((a, b) => b.calificacionNum - a.calificacionNum)
        .slice(0, 3);
      
      const currentDate = new Date();
      
      // Películas en cartelera (lanzadas en los últimos 30 días)
      this.nowPlayingMovies = processedMovies
        .filter(movie => {
          if (!movie.fecha_estreno) return true;
          
          const releaseDate = new Date(movie.fecha_estreno);
          if (isNaN(releaseDate.getTime())) return true;
          
          const diffTime = currentDate - releaseDate;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          return diffDays >= 0 && diffDays <= 30;
        })
        .slice(0, 4);
      
      // Películas próximamente (lanzamientos futuros)
      this.comingSoonMovies = processedMovies
        .filter(movie => {
          if (!movie.fecha_estreno) return false;
          
          const releaseDate = new Date(movie.fecha_estreno);
          if (isNaN(releaseDate.getTime())) return false;
          
          return releaseDate > currentDate;
        })
        .sort((a, b) => new Date(a.fecha_estreno) - new Date(b.fecha_estreno))
        .slice(0, 4);
      
      // Si no hay suficientes próximas, se agregan algunas de las restantes
      if (this.comingSoonMovies.length < 4) {
        const remainingMovies = processedMovies
          .filter(movie => 
            !this.featuredMovies.some(fm => fm.id === movie.id) &&
            !this.nowPlayingMovies.some(npm => npm.id === movie.id) &&
            !this.comingSoonMovies.some(csm => csm.id === movie.id)
          )
          .slice(0, 4 - this.comingSoonMovies.length);
        
        this.comingSoonMovies = [...this.comingSoonMovies, ...remainingMovies];
      }
      
      console.log('Categorización completada:', {
        featured: this.featuredMovies.length,
        nowPlaying: this.nowPlayingMovies.length,
        comingSoon: this.comingSoonMovies.length
      });
    },
    
    // Obtener detalles de una película por ID
    getMovieById(id) {
      return this.allMovies.find(movie => movie.id === id);
    },
    
    // Establecer la película actual para ver detalles
    setCurrentMovie(movie) {
      this.currentMovie = movie;
    },
    
    navigateToMovieDetails(movie, router) {
      console.log('Navegando a detalles de la película:', JSON.stringify(movie));
      if (!movie || !movie.id) {
        console.error('El objeto película no contiene el id esperado:', movie);
        return;
      }
      this.setCurrentMovie(movie);
      router.push(`/movieDetails/${movie.id}`);
    }

  }
});
