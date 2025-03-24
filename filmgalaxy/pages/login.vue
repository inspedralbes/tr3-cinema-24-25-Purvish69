<template>
  <v-container fluid class="fill-height pa-0 login-container">
    <v-row no-gutters justify="center" align="center" class="fill-height">
      <!-- Login Form -->
      <v-col cols="12" md="6" class="d-flex align-center justify-center pa-6">
        <v-card v-motion-slide-right class="pa-8 rounded-xl mx-auto login-card" elevation="12">
          <div class="text-center mb-6">
            <v-icon v-motion-pop icon="mdi-movie" size="64" class="icon-animation mb-4"></v-icon>
            <h2 class="text-h4 font-weight-bold card-title">Film Galaxy</h2>
          </div>

          <v-form @submit.prevent="handleLogin" ref="form" class="login-form">
            <v-text-field v-model="email" label="Email" type="email" :rules="[rules.required, rules.email]"
              variant="outlined" density="comfortable" class="mb-4 custom-input" prepend-inner-icon="mdi-email"
              bg-color="transparent" color="#4A4E69" hide-details="auto"></v-text-field>

            <v-text-field v-model="password" label="Contraseña" :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword" :rules="[rules.required]" variant="outlined"
              density="comfortable" class="mb-6 custom-input" prepend-inner-icon="mdi-lock" bg-color="transparent"
              color="#4A4E69" hide-details="auto"></v-text-field>

            <!-- Alerts for success and error messages -->
            <div class="mb-4">
              <v-slide-y-transition>
                <v-alert v-if="successMessage" type="success" variant="tonal" class="mb-2 alert-success" closable @click:close="successMessage = ''">
                  <div class="d-flex align-center">
                    <v-icon class="mr-2 pulse-animation">mdi-check-circle</v-icon>
                    <span>{{ successMessage }}</span>
                  </div>
                </v-alert>
              </v-slide-y-transition>
              
              <v-slide-y-transition>
                <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-2 alert-error" closable @click:close="errorMessage = ''">
                  <div class="d-flex align-center">
                    <v-icon class="mr-2 shake-animation">mdi-alert-circle</v-icon>
                    <span>{{ errorMessage }}</span>
                  </div>
                </v-alert>
              </v-slide-y-transition>
            </div>

            <v-btn type="submit" block size="large" :loading="loading" class="mb-4 login-btn" elevation="2"
              v-motion-pop>
              <v-icon start icon="mdi-login"></v-icon>
              Iniciar Sesión
            </v-btn>

            <div class="text-center mb-4">
              <v-btn variant="text" class="forgot-password-btn" @click="forgotPassword">
                ¿Olvidaste tu contraseña?
              </v-btn>
            </div>

            <v-divider class="mb-4 divider"></v-divider>

            <div class="text-center">
              <NuxtLink to="/register" class="text-decoration-none d-inline-flex align-center register-link">
                <v-icon size="small" class="mr-1">mdi-account-plus</v-icon>
                ¿No tienes una cuenta? Regístrate
              </NuxtLink>
            </div>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useAuth } from '~/composables/useAuth'

const email = ref('')
const password = ref('')
const { login } = useAuth()

const form = ref(null)
const showPassword = ref(false)
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const rules = {
  required: value => !!value || 'Este campo es requerido',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Formato de email inválido'
  }
}

const handleLogin = async () => {
  // Reset messages
  successMessage.value = ''
  errorMessage.value = ''
  
  const { valid } = await form.value.validate()
  if (valid) {
    loading.value = true
    try {
      const response = await login(email.value, password.value)
      if (response?.token) {
        successMessage.value = '¡Inicio de sesión exitoso! Redirigiendo...'
        
        setTimeout(() => {
          navigateTo('/')
        }, 1500)
      } else {
        errorMessage.value = 'Credenciales incorrectas. Por favor, inténtalo de nuevo.'
      }
    } catch (error) {
      console.error('Login failed:', error)
      errorMessage.value = error?.message || 'Error al iniciar sesión. Por favor, inténtalo de nuevo.'
    } finally {
      loading.value = false
    }
  }
}

const forgotPassword = () => {
  console.log('Forgot password clicked')
}
</script>

<style scoped>
/* Colores principales */
:root {
  --dark-blue: #22223B;
  --medium-blue: #4A4E69;
  --light-purple: #9A8C98;
  --ivory: #F2E9E4;
  --gold: #C8A96E;
  --dark-gray: #1C1C1C;
}

/* Estilos del contenedor principal */
.login-container {
  background: linear-gradient(135deg, #22223B 0%, #EAE0D5 100%);
  min-height: 100vh;
}

/* Estilos de la tarjeta de login */
.login-card {
  position: relative;
  overflow: hidden;
  background-color: #F2E9E4;
  max-width: 500px;
  width: 100%;
  border-radius: 16px !important;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15) !important;
}

.card-title {
  color: #22223B;
  margin-bottom: 1rem;
  letter-spacing: 0.5px;
}

/* Animación del icono */
.icon-animation {
  color: #C8A96E;
  animation: float 3s ease-in-out infinite;
}

/* Animación de flotamiento del icono */
@keyframes float {
  0% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-10px);
  }

  100% {
    transform: translateY(0);
  }
}

/* Estilos de los campos de entrada */
:deep(.custom-input) {
  margin-bottom: 1rem;
}

:deep(.custom-input .v-field__outline) {
  color: #9A8C98 !important;
  opacity: 0.7;
}

:deep(.custom-input .v-field) {
  border-radius: 8px;
  transition: all 0.3s ease;
  background-color: transparent !important;
}

:deep(.custom-input .v-field--focused) {
  box-shadow: 0 0 0 2px rgba(74, 78, 105, 0.4) !important;
}

:deep(.custom-input .v-field--focused .v-field__outline) {
  color: #4A4E69 !important;
  opacity: 1;
}

:deep(.custom-input .v-field__input) {
  color: #22223B;
  padding: 16px 12px;
}

:deep(.custom-input .v-label) {
  color: #4A4E69;
  opacity: 0.8;
}

:deep(.custom-input .v-field--focused .v-label) {
  color: #4A4E69;
  opacity: 1;
}

:deep(.custom-input .v-icon) {
  color: #9A8C98;
}

:deep(.custom-input .v-field--focused .v-icon) {
  color: #4A4E69;
}

/* Elimina el focus outline predeterminado */
:deep(.custom-input .v-field__outline) {
  --v-field-border-opacity: 1;
}

/* Estilos para las alertas */
.alert-success {
  border-left: 4px solid #4CAF50;
  background-color: rgba(76, 175, 80, 0.1) !important;
}

.alert-error {
  border-left: 4px solid #FF5252;
  background-color: rgba(255, 82, 82, 0.1) !important;
}

/* Animaciones para los iconos de alerta */
.pulse-animation {
  animation: pulse 1.5s infinite;
  color: #4CAF50;
}

.shake-animation {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
  color: #FF5252;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes shake {
  10%, 90% {
    transform: translate3d(-1px, 0, 0);
  }
  20%, 80% {
    transform: translate3d(2px, 0, 0);
  }
  30%, 50%, 70% {
    transform: translate3d(-3px, 0, 0);
  }
  40%, 60% {
    transform: translate3d(3px, 0, 0);
  }
}

/* Botón de login */
.login-btn {
  background-color: #C8A96E;
  color: #1C1C1C;
  transition: transform 0.2s ease, background-color 0.3s ease;
  text-transform: none;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.login-btn:hover {
  transform: translateY(-2px);
  background-color: #b89659;
}

/* Botón de contraseña olvidada */
.forgot-password-btn {
  color: #4A4E69;
  font-size: 0.875rem;
  transition: color 0.2s ease;
}

.forgot-password-btn:hover {
  color: #C8A96E;
}

/* Divider */
.divider {
  opacity: 0.5;
  border-color: #9A8C98;
}

/* Enlace de registro */
.register-link {
  color: #4A4E69;
  transition: color 0.2s ease;
}

.register-link:hover {
  color: #C8A96E;
}
</style>