<template>
  <v-container fluid class="fill-height pa-0 register-container">
    <v-row no-gutters justify="center" align="center" class="fill-height">
      <!-- Registration Form -->
      <v-col cols="12" md="6" class="d-flex align-center justify-center pa-6">
        <v-card v-motion-slide-right class="pa-8 rounded-xl mx-auto register-card" elevation="12">
          <v-card-title class="text-h4 font-weight-bold text-center mb-8 card-title">
            Crear Cuenta
          </v-card-title>

          <v-form @submit.prevent="handleSubmit" v-model="isFormValid">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field v-model="formData.nombre" label="Nombre" :rules="[v => !!v || 'El nombre es requerido']"
                  variant="outlined" density="comfortable" class="input-field"
                  prepend-inner-icon="mdi-account"></v-text-field>
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field v-model="formData.apellido" label="Apellido"
                  :rules="[v => !!v || 'El apellido es requerido']" variant="outlined" density="comfortable"
                  class="input-field" prepend-inner-icon="mdi-account"></v-text-field>
              </v-col>
            </v-row>

            <v-text-field v-model="formData.email" label="Email" type="email" :rules="[
              v => !!v || 'El email es requerido',
              v => /.+@.+\..+/.test(v) || 'Email debe ser válido'
            ]" variant="outlined" density="comfortable" class="mb-2 input-field"
              prepend-inner-icon="mdi-email"></v-text-field>

            <v-text-field v-model="formData.telefono" label="Teléfono" type="tel"
              :rules="[v => !!v || 'El teléfono es requerido']" variant="outlined" density="comfortable"
              class="mb-2 input-field" prepend-inner-icon="mdi-phone"></v-text-field>

            <v-text-field v-model="formData.password" label="Contraseña" :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword" :rules="[v => !!v || 'La contraseña es requerida']"
              variant="outlined" density="comfortable" class="mb-4 input-field"
              prepend-inner-icon="mdi-lock"></v-text-field>

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
                <v-alert v-if="error" type="error" variant="tonal" class="mb-2 alert-error" closable @click:close="error = ''">
                  <div class="d-flex align-center">
                    <v-icon class="mr-2 shake-animation">mdi-alert-circle</v-icon>
                    <span>{{ error }}</span>
                  </div>
                </v-alert>
              </v-slide-y-transition>
            </div>

            <v-btn type="submit" block size="large" :loading="loading" :disabled="!isFormValid || loading"
              class="mb-6 register-btn" elevation="2" v-motion-pop>
              <v-icon start icon="mdi-account-plus"></v-icon>
              Registrarse
            </v-btn>

            <div class="text-center">
              <NuxtLink to="/login" class="text-decoration-none d-inline-flex align-center login-link">
                <v-icon size="small" class="mr-1">mdi-login</v-icon>
                ¿Ya tienes una cuenta? Inicia sesión
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

const { register, loading, error } = useAuth()
 
const showPassword = ref(false)
const isFormValid = ref(false)
const successMessage = ref('')
const formData = ref({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  password: ''
})

const handleSubmit = async () => {
  // Reset success message
  successMessage.value = ''
  
  const result = await register(
    formData.value.nombre,
    formData.value.apellido,
    formData.value.email,
    formData.value.telefono,
    formData.value.password
  )

  if (!result.error) {
    successMessage.value = '¡Cuenta creada exitosamente! Redirigiendo al inicio de sesión...'
    // Wait a moment to show the success message before redirecting
    setTimeout(() => {
      navigateTo('/login')
    }, 1500)
  }
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
.register-container {
  background: linear-gradient(135deg, #22223B 0%, #EAE0D5 100%);
  min-height: 100vh;
}

/* Estilos de la tarjeta de registro */
.register-card {
  background-color: #F2E9E4;
  max-width: 500px;
  width: 100%;
  border-radius: 16px !important;
  box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15) !important;
}

.card-title {
  color: #22223B;
}

/* Estilos de los campos de entrada */
.input-field :deep(.v-field__outline__start) {
  border-color: #9A8C98;
}

.input-field :deep(.v-field__outline__end) {
  border-color: #9A8C98;
}

.input-field :deep(.v-field__outline) {
  color: #9A8C98;
}

.input-field :deep(.v-label) {
  color: #4A4E69;
}

.input-field :deep(.v-field--variant-outlined) {
  --v-field-border-opacity: 1;
}

.input-field :deep(.v-field__input) {
  color: #22223B;
}

.input-field :deep(.v-icon) {
  color: #4A4E69;
}

.input-field :deep(.v-field) {
  background-color: #F2E9E4;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.input-field :deep(.v-field--focused) {
  box-shadow: 0 0 0 2px rgba(74, 78, 105, 0.4) !important;
}

.input-field :deep(.v-field--focused .v-field__outline) {
  color: #4A4E69 !important;
  opacity: 1;
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

/* Botón de registro */
.register-btn {
  background-color: #C8A96E;
  color: #1C1C1C;
  transition: transform 0.2s ease, background-color 0.3s ease;
  text-transform: none;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.register-btn:hover {
  transform: translateY(-2px);
  background-color: #b89659;
}

/* Enlace de inicio de sesión */
.login-link {
  color: #4A4E69;
  transition: color 0.2s ease;
}

.login-link:hover {
  color: #C8A96E;
}

/* Estilos generales de botones */
.v-btn {
  text-transform: none;
  letter-spacing: 0.5px;
}
</style>