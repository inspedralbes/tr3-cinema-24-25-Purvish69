export const useAuth = () => {
  const token = useCookie('token')
  const userId = useCookie('userId')
  const API_URL = 'http://localhost:8000/api'

  const login = async (email, password) => {
    try {
      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ email, password })
      })
      
      const data = await response.json()

      if (!response.ok) {
        console.error('Server validation error:', data)
        return { error: data.message || 'Error de autenticación' }
      }

      if (data.token) {
        token.value = data.token

        // Guarda el ID del usuario en la cookie 
        if (data.user && data.user.id) {
          userId.value = data.user.id
        }
      }
      console.log('Datos recibidos:', data)
      return data
    } catch (error) {
      console.error('Error en login:', error)
      return { error: 'Error de conexión' }
    }
  }

  const logout = async () => {
    try {
      await fetch(`${API_URL}/logout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token.value}`
        },
        credentials: 'include'
      })
      token.value = null
      userId.value = null
    } catch (error) {
      console.error('Error en logout:', error)
      token.value = null
      userId.value = null
    }
    console.log('Sesión cerrada', token.value)
  }

  const getUserData = async () => {
    try {
      const response = await fetch(`${API_URL}/user`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token.value}`
        },
        credentials: 'include'
      })
      
      if (!response.ok) {
        console.error('Error al obtener datos de usuario:', response.status)
        return null
      }
      
      const data = await response.json()
      console.log('Datos recibidos para profile:', data)
      return data
    } catch (error) {
      console.error("Error al obtener los datos del usuario", error)
      return null
    }
  }

  // Check if user is authenticated
  const isAuthenticated = computed(() => !!token.value)

  return { login, logout, token, userId, getUserData, isAuthenticated }
}

export default useAuth