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
  const fetchSessions = async (retry = true) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('Fetching sessions data...')
      
      const response = await fetch('http://filmgalaxyback.daw.inspedralbes.cat/api/sessions', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        let errorMsg = 'Error fetching sessions'
        try {
          const data = await response.json()
          errorMsg = data.message || errorMsg
        } catch (e) {
          console.error('Error parsing error response:', e)
        }
        
        throw new Error(errorMsg)
      }
      
      const data = await response.json()
      console.log('Sessions fetched successfully:', data)
      
      // Asegurarse de que cada sesión tenga la información de la película
      const processedSessions = (data.sessions || []).map(session => ({
        ...session,
        movie: session.movie || null,
        estado: session.estado || 'disponible'
      }))
      
      sessions.value = processedSessions
      console.log('Sessions processed and stored in state:', processedSessions.length)
      return { sessions: processedSessions }
    } catch (err) {
      console.error('Error in fetchSessions:', err)
      error.value = err.message
      
      // Si hay un error de conexión y retry es true, intentar una vez más después de un tiempo
      if (retry && (err.message.includes('network') || err.message.includes('fetch'))) {
        console.log('Retrying fetch in 2 seconds...')
        return new Promise((resolve) => {
          setTimeout(async () => {
            try {
              const result = await fetchSessions(false) // Segundo intento sin más retries
              resolve(result)
            } catch (retryErr) {
              resolve({ error: retryErr.message })
            }
          }, 2000)
        })
      }
      
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  const fetchSessionsByMovieId = async (movieId) => {
    try {
      loading.value = true
      const response = await fetch(`http://filmgalaxyback.daw.inspedralbes.cat/api/sessions?movie_id=${movieId}`, {
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

  const fetchSessionById = async (id, retry = true) => {
    try {
      if (!id) {
        throw new Error('Session ID is required');
      }
      
      loading.value = true
      console.log(`Fetching session details for ID: ${id}`)
      
      const response = await fetch(`http://filmgalaxyback.daw.inspedralbes.cat/api/sessions/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        let errorMsg = 'Error fetching session details'
        try {
          const data = await response.json()
          errorMsg = data.message || errorMsg
        } catch (e) {
          console.error('Error parsing error response:', e)
          errorMsg = `${errorMsg} (Status: ${response.status})`
        }
        
        throw new Error(errorMsg)
      }
      
      const data = await response.json()
      console.log('Session details fetched successfully:', data)
      
      // Ensure we have a valid session object
      if (!data.session) {
        throw new Error('Session data not found in response')
      }
      
      currentSession.value = {
        ...data.session,
        movie: data.session.movie || null,
        estado: data.session.estado || 'disponible'
      }
      
      // Save this session to the sessions array if not already there
      if (!sessions.value.some(s => s.id === currentSession.value.id)) {
        sessions.value = [...sessions.value, currentSession.value]
      }
      
      return { session: currentSession.value }
    } catch (err) {
      console.error('Error in fetchSessionById:', err)
      error.value = err.message
      
      // Si hay un error de conexión y retry es true, intentar una vez más después de un tiempo
      if (retry && (err.message.includes('network') || err.message.includes('fetch'))) {
        console.log('Retrying fetch session by ID in 2 seconds...')
        return new Promise((resolve) => {
          setTimeout(async () => {
            try {
              const result = await fetchSessionById(id, false) // Segundo intento sin más retries
              resolve(result)
            } catch (retryErr) {
              resolve({ error: retryErr.message })
            }
          }, 2000)
        })
      }
      
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