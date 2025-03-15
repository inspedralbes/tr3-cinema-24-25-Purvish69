<template>
  <v-app>
    <Navbar />
    <ClientOnly>
      <div class="profile-container">
        <v-container>
          <v-row justify="center">
            <v-col cols="12" sm="10" md="8" lg="6">
              <v-card class="profile-card" elevation="22">
                <!-- Profile Header -->
                <div class="profile-header">
                  <v-avatar size="120" class="user-avatar elevation-5">
                    <v-icon size="72" icon="mdi-account" color="#F2E9E4"></v-icon>
                  </v-avatar>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                  <div class="loading-animation">
                    <div class="dot dot1"></div>
                    <div class="dot dot2"></div>
                    <div class="dot dot3"></div>
                  </div>
                  <p class="text-body-1 loading-text">Cargando información de usuario...</p>
                </div>

                <!-- User Data -->
                <v-card-text v-else-if="userData" class="user-details pa-6">
                  <div class="user-info-container">
                    <div class="info-row">
                      <div class="info-item">
                        <v-icon icon="mdi-account" color="#4A4E69" class="info-icon"></v-icon>
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ userData.nombre }}</span>
                      </div>
                      <div class="info-item">
                        <v-icon icon="mdi-account-multiple" color="#4A4E69" class="info-icon"></v-icon>
                        <span class="info-label">Apellido:</span>
                        <span class="info-value">{{ userData.apellido }}</span>
                      </div>
                    </div>
                    <div class="info-row">
                      <div class="info-item">
                        <v-icon icon="mdi-email" color="#4A4E69" class="info-icon"></v-icon>
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ userData.email }}</span>
                      </div>
                      <div class="info-item">
                        <v-icon icon="mdi-phone" color="#4A4E69" class="info-icon"></v-icon>
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value">{{ userData.telefono }}</span>
                      </div>
                    </div>
                  </div>
                </v-card-text>

                <!-- Error State -->
                <v-card-text v-else class="text-center py-6">
                  <v-icon size="48" icon="mdi-alert-circle" color="#9A8C98" class="mb-4"></v-icon>
                  <p class="text-body-1">No se pudo cargar la información del usuario.</p>
                  <v-btn color="#4A4E69" class="mt-4" @click="loadUserData">
                    Intentar nuevamente
                  </v-btn>
                </v-card-text>

                <!-- Action Button: Logout -->
                <v-card-actions v-if="userData" class="actions-container">
                  <v-btn color="#9A8C98" size="large" @click="handleLogout" variant="elevated" prepend-icon="mdi-logout" class="logout-btn">
                    Cerrar Sesión
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </div>
    </ClientOnly>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import useAuth from '~/composables/useAuth'

// Usamos el objeto completo del composable
const auth = useAuth()
const { getUserData, logout } = auth

const userData = ref(null)
const loading = ref(true)

// Función para leer cookies
const getCookie = (name) => {
  const cookies = document.cookie.split('; ').reduce((prev, current) => {
    const [cookieName, value] = current.split('=')
    prev[cookieName] = value
    return prev
  }, {})
  return cookies[name]
}

const loadUserData = async () => {
  try {
    loading.value = true

    // Obtener el userId de las cookies directamente
    const cookieUserId = getCookie('userId')
    if (!cookieUserId) {
      console.error("No se encontró la cookie userId o es inválida")
      const response = await getUserData()
      if (response ) {
        console.warn('Usando el primer usuario por defecto (solo para desarrollo)')
        userData.value = response
      }
      return
    }

    const response = await getUserData()
    if (response ) {
      userData.value = response
      console.log('Usuario logueado encontrado:', response)
    }else{
      console.error('Usuario no encontrado')
    }
  } catch (error) {
    console.error('Error al cargar los datos del usuario:', error)
  } finally {
    loading.value = false
    console.log('Carga de datos completada. Estado de loading:', loading.value)
  }
}

const handleLogout = async () => {
  try {
    document.cookie = "userId=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
    await logout()
    userData.value = null
    navigateTo('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}

onMounted(() => {
  loadUserData()
})
</script>

<style scoped>
/* Contenedor principal */
.profile-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background-image: linear-gradient(140deg,  #EAE0D5 5%,  #22223B 100%);
}

/* Tarjeta de perfil */
.profile-card {
  border-radius: 10px;
  overflow: hidden;
  background-color: #fff;
  box-shadow: 0 10px 30px rgba(34, 34, 59, 0.1);
  transition: transform 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-4px);
}

/* Encabezado con degradado */
.profile-header {
  padding: 2rem 1.5rem;
  background-color: #EAE0D5;
  display: flex;
  justify-content: center;
}

/* Avatar del usuario */
.user-avatar {
  border: 4px solid #22223B;
  background-color: #4A4E69;
}

.user-details {
  padding: 1.5rem;
}

/* Contenedor de información del usuario */
.user-info-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Filas de información (dos columnas en desktop) */
.info-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

/* Elemento de información */
.info-item {
  flex: 1;
  display: flex;
  align-items: center;
  background-color: #f8f5f2;
  padding: 0.8rem;
  border-radius: 8px;
  border-left: 4px solid #4A4E69;
}

.info-icon {
  margin-right: 0.5rem;
  font-size: 1.2rem;
}

.info-label {
  font-weight: 600;
  color: #4A4E69;
  margin-right: 0.3rem;
}

.info-value {
  font-weight: 500;
  color: #22223B;
}


.actions-container {
    background-color: #EAE0D5;
  padding: 1rem;
  display: flex;
  justify-content: center;
}

.logout-btn {
  min-width: 180px;
  color: white;
  box-shadow: 0 4px 8px rgba(154, 140, 152, 0.3);
}

.logout-btn:hover {
  background-color: #22223B !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(34, 34, 59, 0.2);
}

/* Animación de carga */
.loading-animation {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.dot {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background-color: #4A4E69;
  animation: bounce 1.4s infinite ease-in-out;
}

.dot1 { animation-delay: -0.32s; }
.dot2 { animation-delay: -0.16s; }
.dot3 { animation-delay: 0s; }

@keyframes bounce {
  0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
  40% { transform: scale(1.0); opacity: 1; }
}

.loading-text {
  color: #4A4E69;
  font-weight: 500;
}

/* Responsive: en móviles las filas se apilan */
@media (max-width: 600px) {
  .info-row {
    flex-direction: column;
  }
  .profile-card {
    margin: 0 1rem;
  }
}
</style>
