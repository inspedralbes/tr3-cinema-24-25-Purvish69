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
              variant="outlined" density="comfortable" class="mb-6 input-field"
              prepend-inner-icon="mdi-lock"></v-text-field>

            <v-btn type="submit" block size="large" :loading="loading" :disabled="!isFormValid || loading"
              class="mb-6 register-btn" elevation="2" v-motion-pop>
              <v-icon start icon="mdi-account-plus"></v-icon>
              Registrarse
            </v-btn>

            <v-slide-y-transition>
              <v-alert v-if="error" type="error" variant="tonal" class="mb-6 alert-error" closable>
                {{ error }}
              </v-alert>
            </v-slide-y-transition>

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
const formData = ref({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  password: ''
})

const handleSubmit = async () => {
  const result = await register(
    formData.value.nombre,
    formData.value.apellido,
    formData.value.email,
    formData.value.telefono,
    formData.value.password
  )

  if (!result.error) {
    navigateTo('/login')
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
}

/* Botón de registro */
.register-btn {
  background-color: #C8A96E;
  color: #1C1C1C;
  transition: transform 0.2s ease;
  text-transform: none;
  letter-spacing: 0.5px;
}

.register-btn:hover {
  transform: translateY(-2px);
}

/* Alerta de error */
.alert-error {
  border-left: 4px solid #FF5252;
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