<template>
  <v-container fluid class="fill-height pa-0 register-container">
    <v-row no-gutters justify="center" align="center" class="fill-height">
      <!-- Registration Form -->
      <v-col cols="12" md="6" class="d-flex align-center justify-center pa-6">
        <v-card v-motion-slide-right class="pa-8 rounded-xl mx-auto register-card" elevation="12">
          <v-card-title class="text-h4 font-weight-bold text-center mb-8 card-title">
            Crear Compte
          </v-card-title>

          <v-form @submit.prevent="handleSubmit" v-model="isFormValid">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field v-model="formData.nombre" label="Nom" :rules="[v => !!v || 'El nom és obligatori']"
                  variant="outlined" density="comfortable" class="input-field"
                  prepend-inner-icon="mdi-account"></v-text-field>
              </v-col>

              <v-col cols="12" sm="6">
                <v-text-field v-model="formData.apellido" label="Cognom"
                  :rules="[v => !!v || 'El cognom és obligatori']" variant="outlined" density="comfortable"
                  class="input-field" prepend-inner-icon="mdi-account"></v-text-field>
              </v-col>
            </v-row>

            <v-text-field v-model="formData.email" label="Email" type="email" 
              :rules="emailRules"
              variant="outlined" density="comfortable" class="mb-2 input-field"
              prepend-inner-icon="mdi-email"></v-text-field>

            <v-text-field v-model="formData.telefono" label="Telèfon" type="tel"
              :rules="[v => !!v || 'El telèfon és obligatori']" variant="outlined" density="comfortable"
              class="mb-2 input-field" prepend-inner-icon="mdi-phone"></v-text-field>

            <v-text-field v-model="formData.password" label="Contrasenya" :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showPassword = !showPassword" 
              :rules="passwordRules"
              variant="outlined" density="comfortable" class="mb-4 input-field"
              prepend-inner-icon="mdi-lock"></v-text-field>

            <v-btn type="submit" block size="large" :loading="loading" class="mb-6 register-btn" elevation="2"
              v-motion-pop>
              <v-icon start icon="mdi-account-plus"></v-icon>
              Registrar-se
            </v-btn>

            <div class="text-center">
              <NuxtLink to="/login" class="text-decoration-none d-inline-flex align-center login-link">
                <v-icon size="small" class="mr-1">mdi-login</v-icon>
                Ja tens un compte? Inicia sessió
              </NuxtLink>
            </div>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuth } from '~/composables/useAuth'
import Swal from 'sweetalert2'

const { register } = useAuth()

const showPassword = ref(false)
const isFormValid = ref(false)
const loading = ref(false)
const formData = ref({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  password: ''
})

// Improved validation rules
const emailRules = computed(() => [
  v => !!v || 'L\'email és obligatori',
  v => !v || v.length < 2 || /.+@.+\..+/.test(v) || 'L\'email ha de ser vàlid'
])

const passwordRules = computed(() => [
  v => !!v || 'La contrasenya és obligatòria',
  v => !v || v.length >= 8 || 'La contrasenya ha de tenir almenys 8 caràcters',
  v => !v || /^[a-zA-Z0-9]+$/.test(v) || 'Només s\'accepten lletres o números'
])

const handleSubmit = async () => {
  loading.value = true
  try {
    const result = await register(
      formData.value.nombre,
      formData.value.apellido,
      formData.value.email,
      formData.value.telefono,
      formData.value.password
    )

    if (!result.error) {
      await Swal.fire({
        icon: 'success',
        title: 'Registre Completat!',
        text: 'El teu compte ha estat creat correctament',
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
      navigateTo('/login')
    } else {
      throw new Error(result.error)
    }
  } catch (error) {
    console.error('Registration failed:', error)
    await Swal.fire({
      icon: 'error',
      title: 'Error en el registre',
      text: error?.message || 'Hi ha hagut un error durant el registre. Si us plau, torna-ho a provar.',
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

/* Estils del contenidor principal */
.register-container {
  background: linear-gradient(135deg, #22223B 0%, #EAE0D5 100%);
  min-height: 100vh;
}

/* Estils de la targeta de registre */
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

/* Estils de els camps de entrada */
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

/* Botó de registre */
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

/* Enllaç d'inici de sessió */
.login-link {
  color: #4A4E69;
  transition: color 0.2s ease;
}

.login-link:hover {
  color: #C8A96E;
}

/* Estils generals de botons */
.v-btn {
  text-transform: none;
  letter-spacing: 0.5px;
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
