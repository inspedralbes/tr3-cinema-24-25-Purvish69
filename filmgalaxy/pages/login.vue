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

          <v-form @submit.prevent="handleLogin" ref="form" class="login-form" validate-on="submit">
            <v-text-field v-model="email" label="Correu electrònic" type="email" :rules="[rules.required, rules.email]"
              variant="outlined" density="comfortable" class="mb-4 custom-input" prepend-inner-icon="mdi-email"
              bg-color="transparent" color="#4A4E69" validate-on="blur"></v-text-field>

            <v-text-field v-model="password" label="Contrasenya" :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword" 
              :rules="[
                rules.required,
                rules.password
              ]" 
              variant="outlined"
              density="comfortable" class="mb-6 custom-input" prepend-inner-icon="mdi-lock" bg-color="transparent"
              color="#4A4E69" validate-on="blur"></v-text-field>

            <v-btn type="submit" block size="large" :loading="loading" class="mb-4 login-btn" elevation="2"
              v-motion-pop>
              <v-icon start icon="mdi-login"></v-icon>
              Iniciar Sessió
            </v-btn>

            <div class="text-center mb-4">
              <v-btn variant="text" class="forgot-password-btn" @click="forgotPassword">
                Has oblidat la contrasenya?
              </v-btn>
            </div>

            <v-divider class="mb-4 divider"></v-divider>

            <div class="text-center">
              <NuxtLink to="/register" class="text-decoration-none d-inline-flex align-center register-link">
                <v-icon size="small" class="mr-1">mdi-account-plus</v-icon>
                No tens un compte? Registra't
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
import Swal from 'sweetalert2'

const email = ref('')
const password = ref('')
const { login } = useAuth()

const form = ref(null)
const showPassword = ref(false)
const loading = ref(false)

const rules = {
  required: value => !!value || 'Aquest camp és obligatori',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Format d\'email invàlid'
  },
  password: value => (value && value.length >= 8 && /^[a-zA-Z0-9]+$/.test(value)) || 
    'La contrasenya ha de tenir almenys 8 caràcters amb lletres o números'
}

const handleLogin = async () => {
  const { valid } = await form.value.validate()
  if (valid) {
    loading.value = true
    try {
      const response = await login(email.value, password.value)
      if (response?.token) {
        await Swal.fire({
          icon: 'success',
          title: 'Benvingut!',
          text: 'Inici de sessió correcte',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
          customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            htmlContainer: 'swal-custom-content'
          },
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
        navigateTo('/')
      } else {
        throw new Error('Credencials incorrectes')
      }
    } catch (error) {
      console.error('Login failed:', error)
      await Swal.fire({
        icon: 'error',
        title: 'Error d\'accés',
        text: error?.message || 'Credencials incorrectes. Si us plau, torna-ho a provar.',
        confirmButtonText: 'Entès',
        customClass: {
          popup: 'swal-custom-popup',
          title: 'swal-custom-title',
          htmlContainer: 'swal-custom-content',
          confirmButton: 'swal-custom-confirm'
        }
      })
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
/* Colores principals */
:root {
  --dark-blue: #22223B;
  --medium-blue: #4A4E69;
  --light-purple: #9A8C98;
  --ivory: #F2E9E4;
  --gold: #C8A96E;
  --dark-gray: #1C1C1C;
}

/* Estils del contenedor principal */
.login-container {
  background: linear-gradient(135deg, #22223B 0%, #EAE0D5 100%);
  min-height: 100vh;
}

/* Estils de la tarjeta de login */
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

/* Animació del icono */
.icon-animation {
  color: #C8A96E;
  animation: float 3s ease-in-out infinite;
}

/* Animació de flotació del icono */
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

/* Estils dels camps de entrada */
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

/* Botó de login */
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

/* Botó de contrasenya oblidada */
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

/* Enllaç de registre */
.register-link {
  color: #4A4E69;
  transition: color 0.2s ease;
}

.register-link:hover {
  color: #C8A96E;
}

/* Estils personalitzats per SweetAlert2 */
:global(.swal-custom-popup) {
  background: #F2E9E4;
  border-radius: 16px;
  padding: 2rem;
}

:global(.swal-custom-title) {
  color: #22223B;
  font-size: 1.5rem;
  font-weight: 600;
}

:global(.swal-custom-content) {
  color: #4A4E69;
  font-size: 1rem;
}

:global(.swal-custom-confirm) {
  background-color: #C8A96E !important;
  color: #1C1C1C !important;
  border-radius: 8px !important;
  font-weight: 500 !important;
  padding: 0.75rem 2rem !important;
  transition: all 0.3s ease !important;
}

:global(.swal-custom-confirm:hover) {
  background-color: #b89659 !important;
  transform: translateY(-2px);
}

:global(.swal2-timer-progress-bar) {
  background: #C8A96E !important;
}
</style>