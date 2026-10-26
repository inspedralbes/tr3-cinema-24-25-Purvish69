import { ref } from 'vue'
import { useCookie } from '#app'

export const usePeliculas = () => {
  const config = useRuntimeConfig()
  const API_URL = config.public.apiBaseUrl
  const error = ref('')
  const loading = ref(false)

  // Función para obtener el token de autenticación
  const getAuthToken = () => {
    if (typeof window !== 'undefined') {
      const localToken = localStorage.getItem('token')
      if (localToken) {
        return localToken
      }

      const cookies = document.cookie.split(';')
      for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=')
        if (name === 'token') {
          return value
        }
      }
    }
    return null
  }

  // Get all movies
  const getPeliculas = async () => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/movies`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching movies')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get movie by ID
  const getPeliculaById = async (id) => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/movies/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching movie details')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get sessions for a movie
  const getSesiones = async (movieId = null) => {
    try {
      loading.value = true
      const sessionsUrl = movieId
        ? `${API_URL}/sessions?movie_id=${movieId}`
        : `${API_URL}/sessions`

      const response = await fetch(sessionsUrl, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching sessions')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get seats for a session
  const getAsientos = async (sessionId) => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/sessions/${sessionId}/seats`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching seats')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // fetch para crear el ticket
  const createTicket = async (ticketData) => {
    try {
      loading.value = true
      const token = getAuthToken()

      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      if (token) {
        headers['Authorization'] = `Bearer ${token}`
      }

      const response = await fetch(`${API_URL}/tickets`, {
        method: 'POST',
        headers,
        credentials: 'include',
        body: JSON.stringify(ticketData)
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error creating ticket')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get user tickets
  const getTickets = async (userId = null) => {
    try {
      loading.value = true
      const token = getAuthToken()
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      if (token) {
        headers['Authorization'] = `Bearer ${token}`
      }

      let url = `${API_URL}/tickets`
      if (userId) {
        url += `?user_id=${encodeURIComponent(userId)}`
      }

      const response = await fetch(url, {
        method: 'GET',
        headers,
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching tickets')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get tickets for a specific session
  const getSessionTickets = async (sessionId) => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/sessions/${sessionId}/tickets`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json().catch(() => ({}))
        throw new Error(data.message || 'Error fetching session tickets')
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // fetch para crear el pago
  const createPayment = async (paymentData) => {
    try {
      loading.value = true
      const token = getAuthToken()

      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      if (token) {
        headers['Authorization'] = `Bearer ${token}`
      }

      const response = await fetch(`${API_URL}/payments`, {
        method: 'POST',
        headers,
        credentials: 'include',
        body: JSON.stringify(paymentData)
      })

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}))
        throw new Error(errorData.message || `Error creating payment: ${response.status}`)
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  // Get user tickets with user info
  const getTicketWithUser = async (ticketId) => {
    try {
      if (!ticketId) {
        return { error: 'No se proporcionó ID del ticket' }
      }

      loading.value = true
      const token = getAuthToken()
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      if (token) {
        headers['Authorization'] = `Bearer ${token}`
      }

      const response = await fetch(`${API_URL}/tickets/${ticketId}/with-user`, {
        method: 'GET',
        headers,
        credentials: 'include'
      })

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}))
        throw new Error(errorData.message || `Error obteniendo ticket: ${response.status}`)
      }

      const data = await response.json()
      return data
    } catch (err) {
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }

  return {
    error,
    loading,
    getPeliculas,
    getPeliculaById,
    getSesiones,
    getAsientos,
    createTicket,
    getTickets,
    getSessionTickets,
    createPayment,
    getTicketWithUser,
  }
}
