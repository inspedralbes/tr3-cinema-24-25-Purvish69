// ~/composables/useAuth.js
export const useAuth = () => {
  const token = useCookie('token')
  const userId = useCookie('userId')

  const login = async (email, password) => {
    try {
      const { data } = await useFetch('http://localhost:8000/api/login', {
        method: 'POST',
        body: { email, password }
      })

      if (data.value?.token) {
        token.value = data.value.token

        // Guarda el ID del usuario en la cookie 
        if (data.value.user && data.value.user.id) {
          userId.value = data.value.user.id
        }
      }
      console.log('Datos recibidos:', data.value);

      return data.value
    } catch (error) {
      console.error('Error en login:', error)
      return null
    }
  }

  const logout = async () => {
    try {
      await useFetch('http://localhost:8000/api/logout', {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` }
      })
      token.value = null
      userId.value = null
    } catch (error) {
      console.error('Error en logout:', error)
      token.value = null
      userId.value = null
    }
    console.log('Sesión cerrada', token.value);
  }

  // Obtener info de user - Modificado para usar $fetch
  const getUserData = async () => {
    try {
      // Usado $fetch en lugar de useFetch para componentes ya montados
      const data = await $fetch('http://localhost:8000/api/user', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token.value}`
        }
      })
      console.log('Datos recibidos para profile:', data);
      return data
    } catch (error) {
      console.error("Error al obtener los datos del usuario", error)
      return null
    }
  }

  // Check if user is authenticated
  const isAuthenticated = computed(() => {
    return !!token.value
  })

  return { login, logout, token, userId, getUserData, isAuthenticated }
}

export default useAuth