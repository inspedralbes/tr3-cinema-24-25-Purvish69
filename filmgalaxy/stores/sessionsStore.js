import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useSessionsStore = defineStore('sessions', () => {
  // State
  const sessions = ref([])
  const currentSession = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const ticketDetails = ref(null)

  // Computed
  const availableSessions = computed(() => {
    return sessions.value.filter(session => session.estado === 'disponible')
  })

  // Actions
  const fetchSessions = async () => {
    try {
      loading.value = true
      console.log('Fetching sessions...')
      const response = await fetch('http://localhost:8000/api/sessions', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        const data = await response.json()
        throw new Error(data.message || 'Error fetching sessions')
      }
      
      const data = await response.json()
      console.log('Sessions received:', data)
      
      // Asegurarse de que cada sesión tenga la información de la película
      const processedSessions = (data.sessions || []).map(session => ({
        ...session,
        movie: session.movie || null,
        estado: session.estado || 'disponible'
      }))
      
      sessions.value = processedSessions
      console.log('Processed sessions:', sessions.value)
      return { sessions: processedSessions }
    } catch (err) {
      console.error('Error in fetchSessions:', err)
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  const fetchSessionsByMovieId = async (movieId) => {
    try {
      loading.value = true
      const response = await fetch(`http://localhost:8000/api/sessions?movie_id=${movieId}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        const data = await response.json()
        throw new Error(data.message || 'Error fetching sessions for this movie')
      }
      
      const data = await response.json()
      const movieSessions = (data.sessions || []).map(session => ({
        ...session,
        movie: session.movie || null,
        estado: session.estado || 'disponible'
      }))
      
      // Update store state with these sessions
      sessions.value = movieSessions
      
      return { sessions: movieSessions }
    } catch (err) {
      console.error('Error in fetchSessionsByMovieId:', err)
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  const fetchSessionById = async (id) => {
    try {
      loading.value = true
      const response = await fetch(`http://localhost:8000/api/sessions/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        const data = await response.json()
        throw new Error(data.message || 'Error fetching session details')
      }
      
      const data = await response.json()
      currentSession.value = {
        ...data.session,
        movie: data.session.movie || null,
        estado: data.session.estado || 'disponible'
      }
      return { session: currentSession.value }
    } catch (err) {
      console.error('Error in fetchSessionById:', err)
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  const clearSessions = () => {
    sessions.value = []
    currentSession.value = null
    error.value = null
  }

  const setTicketDetails = (details) => {
    ticketDetails.value = details
  }

  const getTicketDetails = () => {
    return ticketDetails.value
  }

  const clearTicketDetails = () => {
    ticketDetails.value = null
  }

  return {
    // State
    sessions,
    currentSession,
    loading,
    error,
    ticketDetails,
    // Computed
    availableSessions,
    // Actions
    fetchSessions,
    fetchSessionsByMovieId,
    fetchSessionById,
    clearSessions,
    setTicketDetails,
    getTicketDetails,
    clearTicketDetails
  }
})