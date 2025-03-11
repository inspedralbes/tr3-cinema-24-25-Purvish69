<template>
  <div class="login-container">
    <v-container class="h-100">
      <v-row class="h-100" align="center" justify="center">
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card class="login-card" elevation="8">
            <div class="text-center pt-6">
              <v-icon icon="mdi-movie" size="48" color="gold" class="mb-2"></v-icon>
              <h1 class="text-h4 font-weight-bold mb-4 text-primary">CineTickets</h1>
            </div>
            <v-card-text>
              <v-form @submit.prevent="handleLogin" ref="form">
                <v-text-field
                  v-model="email"
                  label="Email"
                  prepend-inner-icon="mdi-email"
                  variant="outlined"
                  color="secondary"
                  :rules="[rules.required, rules.email]"
                  class="mb-4"
                ></v-text-field>

                <v-text-field
                  v-model="password"
                  label="Password"
                  prepend-inner-icon="mdi-lock"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword"
                  :type="showPassword ? 'text' : 'password'"
                  variant="outlined"
                  color="secondary"
                  :rules="[rules.required, rules.password]"
                  class="mb-6"
                ></v-text-field>

                <v-btn block color="gold" size="large" type="submit" :loading="loading" class="mb-4">
                  Sign In
                </v-btn>

                <div class="text-center mb-4">
                  <v-btn variant="text" color="secondary" class="text-caption" @click="forgotPassword">
                    Forgot Password?
                  </v-btn>
                </div>
                <v-divider class="mb-4"></v-divider>
                <div class="text-center">
                  <p class="text-body-2 mb-2">Don't have an account?</p>
                  <v-btn block color="primary" variant="outlined" @click="navigateToRegister">
                    Create Account
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
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
  required: value => !!value || 'Esto es requerido',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Invalid email format'
  },
  // password: value => value?.length >= 8 || 'La contraseña debe tener al menos 8 caracteres'
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
  // TODO: Implement forgot password logic
  console.log('Forgot password clicked')
}

const navigateToRegister = () => {
  navigateTo('/register')
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  background-image: linear-gradient(120deg, #22223B 33%, #C8A96E 100%);
}

.login-card {
  background-color: #F2E9E4 !important;
  border-radius: 16px !important;
  padding: 1rem;
  align-items: center;
}

:deep(.v-field) {
  border-radius: 8px;
}

:deep(.v-btn) {
  text-transform: none;
  border-radius: 8px;
  font-weight: 500;
}
</style>
