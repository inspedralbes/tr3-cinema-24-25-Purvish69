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
              variant="outlined" density="comfortable" class="mb-4 input-field" prepend-inner-icon="mdi-email">
            </v-text-field>

            <v-text-field v-model="password" label="Contraseña" :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword" :rules="[rules.required]" variant="outlined"
              density="comfortable" class="mb-6 input-field" prepend-inner-icon="mdi-lock">
            </v-text-field>

            <v-btn type="submit" block size="large" :loading="loading" class="mb-4 login-btn" elevation="2" v-motion-pop>
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

const rules = {
  required: value => !!value || 'Este campo es requerido',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Formato de email inválido'
  }
}

const handleLogin = async () => {
  const { valid } = await form.value.validate()
  if (valid) {
    loading.value = true
    try {
      const response = await login(email.value, password.value)
      if (response?.token) {
        navigateTo('/')
      }
    } catch (error) {
      console.error('Login failed:', error)
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
}

.card-title {
  color: #22223B;
}

/* Animación del icono */
.icon-animation {
  color: #C8A96E;
  animation: float 3s ease-in-out infinite;
}

/* Animación de flotamiento del icono */
@keyframes float {
  0% {
    transform: translateY(20px);
  }

  50% {
    transform: translateY(-10px);
  }

  100% {
    transform: translateY(20px);
  }
}

/* Estilos de los campos de entrada */
.input-field {
  --v-field-border-opacity: 1;
}

.input-field .v-field__outline__start,
.input-field .v-field__outline__end,
.input-field .v-field__outline {
  border-color: #9A8C98;
  color: #9A8C98;
}

.input-field .v-label {
  color: #4A4E69;
}

.input-field .v-field--variant-outlined {
  --v-field-border-opacity: 1;
}

.input-field .v-field__input {
  color: #22223B;
}

.input-field .v-icon {
  color: #4A4E69;
}

.input-field .v-field {
  background-color: #F2E9E4;
}

/* Estilos del botón de login */
.login-btn {
  background-color: #C8A96E;
  color: #1C1C1C;
  transition: transform 0.2s ease;
  text-transform: none;
  letter-spacing: 0.5px;
}

.login-btn:hover {
  transform: translateY(-2px);
}

/* Botón de contraseña olvidada */
.forgot-password-btn {
  color: #4A4E69;
  opacity: 0.8;
  transition: opacity 0.2s ease;
}

.forgot-password-btn:hover {
  opacity: 1;
}

/* Divider */
.divider {
  color: #9A8C98;
}

/* Enlace de registro */
.register-link {
  color: #4A4E69;
  transition: color 0.2s ease;
}

.register-link:hover {
  color: #C8A96E;
}

/* Estilos generales de botones */
.v-btn {
  text-transform: none;
  letter-spacing: 0.5px;
}
</style>