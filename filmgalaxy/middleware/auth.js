
export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('token') // Obtiene el token de autenticación de una cookie llamada 'token'
  if (!token.value && to.path !== '/login') { // Si no hay token y la ruta de destino no es '/login'
    return navigateTo('/login') // Redirige al usuario a la página de inicio de sesión
  }
})