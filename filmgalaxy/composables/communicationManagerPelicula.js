export const usePeliculas = () => {
  const API_URL = 'http://localhost:8000/api'
  const error = ref('')
  const loading = ref(false)


  // Funcion para obtener el token de autenticacion
  const getAuthToken = () => {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('token')
    }
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
        const data = await response.json()
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
        const data = await response.json()
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
  const getSesiones = async (movieId) => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/sessions?movie_id=${movieId}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })
      console.log('Datos de la sesion', response);


      if (!response.ok) {
        const data = await response.json()
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
        const data = await response.json()
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

      // Obtener el token desde las cookies o localStorage
      const token = useCookie('token').value || localStorage.getItem('token')

      if (!token) {
        throw new Error('No hay token de autenticación disponible')
      }

      const response = await fetch(`${API_URL}/tickets`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        credentials: 'include',
        body: JSON.stringify(ticketData)
      })

      if (!response.ok) {
        const data = await response.json()
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
  const getTickets = async () => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/tickets`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json()
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
        const data = await response.json()
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
  // Create payment con token de autenticación
  const createPayment = async (paymentData) => {
    try {
      loading.value = true

      const token = getAuthToken()

      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      // Añadir token de autenticación si está disponible
      if (token) {
        headers['Authorization'] = `Bearer ${token}`
      } else {
        console.warn('No authentication token available')
      }
      console.log('pago de user:', headers);
      

      const response = await fetch(`${API_URL}/payments`, {
        method: 'POST',
        headers,
        credentials: 'include',
        body: JSON.stringify(paymentData)
      })

      console.log('Payment response status:', response.status)

      if (!response.ok) {
        const errorData = await response.json()
        console.error('Payment error details:', errorData)
        throw new Error(errorData.message || `Error creating payment: ${response.status}`)
      }

      const data = await response.json()
      console.log('Payment success data:', data)
      return data
    } catch (err) {
      console.error('Payment error:', err)
      error.value = err.message
      return { error: err.message }
    } finally {
      loading.value = false
    }
  }
  // Get user tickets
  const getUserTickets = async (userId) => {
    try {
      loading.value = true
      const response = await fetch(`${API_URL}/users/${userId}/tickets`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include'
      })

      if (!response.ok) {
        const data = await response.json()
        throw new Error(data.message || 'Error fetching user tickets')
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
    getUserTickets
  }
}