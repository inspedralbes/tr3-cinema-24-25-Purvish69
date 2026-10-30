<template>
  <div class="auth-page-wrapper">
    <!-- Ambient Animated Galaxy Background -->
    <div class="ambient-nebula nebula-1"></div>
    <div class="ambient-nebula nebula-2"></div>
    <div class="ambient-nebula nebula-3"></div>
    <div class="projector-light"></div>

    <!-- Floating Star Particles -->
    <div class="stars-layer" aria-hidden="true">
      <span v-for="n in 24" :key="n" :class="`star star-${n}`"></span>
    </div>

    <!-- Main Card Container -->
    <div class="auth-container">
      <div class="auth-card">
        
        <!-- LEFT SIDE: Cinema Experience Showcase (Desktop) -->
        <div class="cinema-showcase hidden-md-down">
          <div class="showcase-content">
            <!-- Back to Home Link -->
            <NuxtLink to="/" class="back-link">
              <i class="mdi mdi-arrow-left"></i>
              <span>Tornar a la cartellera</span>
            </NuxtLink>

            <!-- Brand Section -->
            <div class="brand-section">
              <div class="logo-wrapper">
                <img 
                  src="/logo/logofilmgalaxyTransparente.png" 
                  alt="FilmGalaxy Logo" 
                  class="brand-logo"
                />
              </div>
              <div class="badge-vip">
                <i class="mdi mdi-star-shooting-outline"></i>
                <span>EXPERIÈNCIA CINEMA VIP</span>
              </div>
              <h2 class="showcase-title">
                Viu la màgia del cinema <br/>
                <span class="text-gold-gradient">com mai abans</span>
              </h2>
              <p class="showcase-description">
                Submergeix-te en les millors estrenes amb màxima qualitat d'àudio i vídeo, reserves en temps real i seients de primera classe.
              </p>
            </div>

            <!-- Cinema Perks Grid -->
            <div class="perks-list">
              <div class="perk-item">
                <div class="perk-icon">
                  <i class="mdi mdi-seat-recline-extra"></i>
                </div>
                <div class="perk-text">
                  <strong>Butaques VIP Reclinables</strong>
                  <span>Màxim confort i espai a la sala</span>
                </div>
              </div>

              <div class="perk-item">
                <div class="perk-icon">
                  <i class="mdi mdi-surround-sound"></i>
                </div>
                <div class="perk-text">
                  <strong>So Envoltant Dolby Atmos</strong>
                  <span>Acústica que et posa al centre de l'acció</span>
                </div>
              </div>

              <div class="perk-item">
                <div class="perk-icon">
                  <i class="mdi mdi-ticket-percent-outline"></i>
                </div>
                <div class="perk-text">
                  <strong>Dia de l'Espectador & Promos</strong>
                  <span>Descomptes exclusius cada setmana</span>
                </div>
              </div>
            </div>

            <!-- Stylized Cinema Ticket Mockup -->
            <div class="ticket-preview">
              <div class="ticket-notch notch-left"></div>
              <div class="ticket-notch notch-right"></div>
              <div class="ticket-body">
                <div class="ticket-info">
                  <span class="ticket-tag">ENTRADA PREMIERE</span>
                  <span class="ticket-hall">SALA 01 • ACCÉS VIP</span>
                </div>
                <div class="ticket-barcode">
                  <span>||| | | |||| || | ||||| | ||</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- RIGHT SIDE: Interactive Form -->
        <div class="auth-form-section">
          <!-- Mobile Brand Header -->
          <div class="mobile-header hidden-lg-up">
            <NuxtLink to="/" class="mobile-back">
              <i class="mdi mdi-arrow-left"></i>
            </NuxtLink>
            <img src="/logo/logofilmgalaxyTransparente.png" alt="FilmGalaxy" class="mobile-logo" />
          </div>

          <!-- Tab Selector Pill -->
          <div class="tab-selector-container">
            <div class="tab-selector">
              <button 
                type="button"
                :class="['tab-btn', { active: currentTab === 'login' }]"
                @click="switchTab('login')"
              >
                <i class="mdi mdi-login mr-1"></i>
                Iniciar Sessió
              </button>
              <button 
                type="button"
                :class="['tab-btn', { active: currentTab === 'register' }]"
                @click="switchTab('register')"
              >
                <i class="mdi mdi-account-plus mr-1"></i>
                Crear Compte
              </button>
              <!-- Animated Pill Background -->
              <div 
                class="tab-indicator"
                :style="{ transform: currentTab === 'login' ? 'translateX(0)' : 'translateX(100%)' }"
              ></div>
            </div>
          </div>

          <!-- Animated Form Container -->
          <Transition name="fade-slide" mode="out-in">
            <!-- LOGIN FORM -->
            <div v-if="currentTab === 'login'" key="login" class="form-wrapper">
              <div class="form-heading">
                <h3 class="form-title">Benvingut de nou! 🎬</h3>
                <p class="form-subtitle">Introdueix les teves credencials per accedir al teu compte</p>
              </div>

              <form @submit.prevent="handleLoginSubmit" class="space-y-4">
                <!-- Email Field -->
                <div class="input-group">
                  <label for="login-email" class="input-label">Correu electrònic</label>
                  <div class="input-container" :class="{ 'has-error': loginErrors.email }">
                    <span class="input-icon">
                      <i class="mdi mdi-email-outline"></i>
                    </span>
                    <input 
                      id="login-email"
                      v-model="loginEmail" 
                      type="email" 
                      placeholder="nom@exemple.cat"
                      required
                      autocomplete="email"
                      @blur="validateLoginEmail"
                    />
                  </div>
                  <span v-if="loginErrors.email" class="error-text">
                    {{ loginErrors.email }}
                  </span>
                </div>

                <!-- Password Field -->
                <div class="input-group">
                  <div class="flex justify-between items-center mb-1">
                    <label for="login-password" class="input-label">Contrasenya</label>
                    <button 
                      type="button" 
                      class="forgot-link"
                      @click="handleForgotPassword"
                    >
                      Has oblidat la contrasenya?
                    </button>
                  </div>
                  <div class="input-container" :class="{ 'has-error': loginErrors.password }">
                    <span class="input-icon">
                      <i class="mdi mdi-lock-outline"></i>
                    </span>
                    <input 
                      id="login-password"
                      v-model="loginPassword" 
                      :type="showLoginPassword ? 'text' : 'password'" 
                      placeholder="••••••••"
                      required
                      autocomplete="current-password"
                      @blur="validateLoginPassword"
                    />
                    <button 
                      type="button" 
                      class="toggle-visibility-btn"
                      @click="showLoginPassword = !showLoginPassword"
                      :title="showLoginPassword ? 'Amaga la contrasenya' : 'Mostra la contrasenya'"
                    >
                      <i :class="showLoginPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
                    </button>
                  </div>
                  <span v-if="loginErrors.password" class="error-text">
                    {{ loginErrors.password }}
                  </span>
                </div>

                <!-- Submit Button -->
                <button 
                  type="submit" 
                  class="btn-gold-action"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner"></span>
                  <span v-else class="flex items-center justify-center gap-2">
                    <i class="mdi mdi-movie-open-play"></i>
                    Iniciar Sessió
                  </span>
                </button>
              </form>

              <!-- Footer Switch -->
              <div class="form-footer">
                <span>No tens cap compte a FilmGalaxy?</span>
                <button type="button" class="switch-link" @click="switchTab('register')">
                  Registra't gratis
                </button>
              </div>
            </div>

            <!-- REGISTER FORM -->
            <div v-else key="register" class="form-wrapper">
              <div class="form-heading">
                <h3 class="form-title">Uneix-te a FilmGalaxy 🎟️</h3>
                <p class="form-subtitle">Crea el teu compte gratuït per reservar butaques i gaudir de descomptes</p>
              </div>

              <!-- Demo Project Notice Alert -->
              <v-alert
                type="warning"
                variant="tonal"
                density="comfortable"
                class="mb-4 text-sm font-medium"
                icon="mdi-alert-circle-outline"
              >
                Demo: no se realiza ningún cobro real. No introduzcas datos de tarjeta ni información personal real.
              </v-alert>

              <form @submit.prevent="handleRegisterSubmit" class="space-y-3.5">
                <!-- Name & Surname (Grid 2 cols) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div class="input-group">
                    <label for="reg-nombre" class="input-label">Nom</label>
                    <div class="input-container">
                      <span class="input-icon">
                        <i class="mdi mdi-account-outline"></i>
                      </span>
                      <input 
                        id="reg-nombre"
                        v-model="registerData.nombre" 
                        type="text" 
                        placeholder="Purvish"
                        required
                        autocomplete="given-name"
                      />
                    </div>
                  </div>

                  <div class="input-group">
                    <label for="reg-apellido" class="input-label">Cognom</label>
                    <div class="input-container">
                      <span class="input-icon">
                        <i class="mdi mdi-account-outline"></i>
                      </span>
                      <input 
                        id="reg-apellido"
                        v-model="registerData.apellido" 
                        type="text" 
                        placeholder="Patel"
                        required
                        autocomplete="family-name"
                      />
                    </div>
                  </div>
                </div>

                <!-- Email Field -->
                <div class="input-group">
                  <label for="reg-email" class="input-label">Correu electrònic</label>
                  <div class="input-container" :class="{ 'has-error': registerErrors.email }">
                    <span class="input-icon">
                      <i class="mdi mdi-email-outline"></i>
                    </span>
                    <input 
                      id="reg-email"
                      v-model="registerData.email" 
                      type="email" 
                      placeholder="nom@exemple.cat"
                      required
                      autocomplete="email"
                      @blur="validateRegisterEmail"
                    />
                  </div>
                  <span v-if="registerErrors.email" class="error-text">
                    {{ registerErrors.email }}
                  </span>
                </div>

                <!-- Phone Field -->
                <div class="input-group">
                  <label for="reg-phone" class="input-label">Telèfon de contacte</label>
                  <div class="input-container">
                    <span class="input-icon">
                      <i class="mdi mdi-phone-outline"></i>
                    </span>
                    <input 
                      id="reg-phone"
                      v-model="registerData.telefono" 
                      type="tel" 
                      placeholder="612 345 678"
                      required
                      autocomplete="tel"
                    />
                  </div>
                </div>

                <!-- Password Field -->
                <div class="input-group">
                  <label for="reg-password" class="input-label">Contrasenya</label>
                  <div class="input-container" :class="{ 'has-error': registerErrors.password }">
                    <span class="input-icon">
                      <i class="mdi mdi-lock-outline"></i>
                    </span>
                    <input 
                      id="reg-password"
                      v-model="registerData.password" 
                      :type="showRegisterPassword ? 'text' : 'password'" 
                      placeholder="Mínim 8 caràcters"
                      required
                      autocomplete="new-password"
                      @input="checkPasswordStrength"
                    />
                    <button 
                      type="button" 
                      class="toggle-visibility-btn"
                      @click="showRegisterPassword = !showRegisterPassword"
                      :title="showRegisterPassword ? 'Amaga la contrasenya' : 'Mostra la contrasenya'"
                    >
                      <i :class="showRegisterPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
                    </button>
                  </div>
                  
                  <!-- Dynamic Password Strength Meter -->
                  <div v-if="registerData.password" class="password-meter">
                    <div class="meter-bars">
                      <div class="bar" :class="{ active: passwordScore >= 1, 'bar-weak': passwordScore === 1, 'bar-med': passwordScore === 2, 'bar-good': passwordScore >= 3 }"></div>
                      <div class="bar" :class="{ active: passwordScore >= 2, 'bar-med': passwordScore === 2, 'bar-good': passwordScore >= 3 }"></div>
                      <div class="bar" :class="{ active: passwordScore >= 3, 'bar-good': passwordScore >= 3 }"></div>
                      <div class="bar" :class="{ active: passwordScore >= 4, 'bar-strong': passwordScore === 4 }"></div>
                    </div>
                    <span class="meter-text" :class="passwordStrengthClass">{{ passwordStrengthLabel }}</span>
                  </div>
                  <span v-if="registerErrors.password" class="error-text">
                    {{ registerErrors.password }}
                  </span>
                </div>

                <!-- Terms notice -->
                <p class="terms-note">
                  En registrar-te, acceptes les nostres condicions d'ús i la política de privacitat de FilmGalaxy.
                </p>

                <!-- Submit Button -->
                <button 
                  type="submit" 
                  class="btn-gold-action"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner"></span>
                  <span v-else class="flex items-center justify-center gap-2">
                    <i class="mdi mdi-account-plus"></i>
                    Crear el meu compte VIP
                  </span>
                </button>
              </form>

              <!-- Footer Switch -->
              <div class="form-footer">
                <span>Ja tens un compte creat?</span>
                <button type="button" class="switch-link" @click="switchTab('login')">
                  Inicia sessió
                </button>
              </div>
            </div>
          </Transition>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '~/composables/useAuth'
import Swal from 'sweetalert2'

const props = defineProps({
  defaultMode: {
    type: String,
    default: 'login' // 'login' | 'register'
  }
})

const router = useRouter()
const { login, register } = useAuth()

// Tab state
const currentTab = ref(props.defaultMode)

watch(() => props.defaultMode, (newVal) => {
  currentTab.value = newVal
})

const switchTab = (tab) => {
  currentTab.value = tab
  // Synchronize browser history and path without full page reload
  if (tab === 'login') {
    router.replace('/login')
  } else {
    router.replace('/register')
  }
}

// Loading state
const loading = ref(false)

// Login Form Data
const loginEmail = ref('')
const loginPassword = ref('')
const showLoginPassword = ref(false)
const loginErrors = ref({
  email: '',
  password: ''
})

// Register Form Data
const registerData = ref({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  password: ''
})
const showRegisterPassword = ref(false)
const registerErrors = ref({
  email: '',
  password: ''
})

// Validation functions
const validateLoginEmail = () => {
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!loginEmail.value) {
    loginErrors.value.email = 'Aquest camp és obligatori'
  } else if (!pattern.test(loginEmail.value)) {
    loginErrors.value.email = 'Format de correu invàlid'
  } else {
    loginErrors.value.email = ''
  }
}

const validateLoginPassword = () => {
  if (!loginPassword.value) {
    loginErrors.value.password = 'La contrasenya és obligatòria'
  } else {
    loginErrors.value.password = ''
  }
}

const validateRegisterEmail = () => {
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!registerData.value.email) {
    registerErrors.value.email = 'El correu és obligatori'
  } else if (!pattern.test(registerData.value.email)) {
    registerErrors.value.email = 'Format de correu invàlid'
  } else {
    registerErrors.value.email = ''
  }
}

// Password Strength
const passwordScore = ref(0)
const checkPasswordStrength = () => {
  const pwd = registerData.value.password
  let score = 0
  if (!pwd) {
    passwordScore.value = 0
    registerErrors.value.password = ''
    return
  }
  if (pwd.length >= 8) score++
  if (/[0-9]/.test(pwd)) score++
  if (/[a-zA-Z]/.test(pwd)) score++
  if (/[^a-zA-Z0-9]/.test(pwd) || pwd.length >= 12) score++
  passwordScore.value = score

  if (pwd.length < 8) {
    registerErrors.value.password = 'Ha de tenir almenys 8 caràcters'
  } else {
    registerErrors.value.password = ''
  }
}

const passwordStrengthLabel = computed(() => {
  switch (passwordScore.value) {
    case 1: return 'Massa curta'
    case 2: return 'Acceptable'
    case 3: return 'Segura'
    case 4: return 'Molt Segura! 🛡️'
    default: return ''
  }
})

const passwordStrengthClass = computed(() => {
  switch (passwordScore.value) {
    case 1: return 'text-red-400'
    case 2: return 'text-amber-400'
    case 3: return 'text-emerald-400'
    case 4: return 'text-green-300 font-bold'
    default: return 'text-slate-400'
  }
})

// Login Submission
const handleLoginSubmit = async () => {
  validateLoginEmail()
  validateLoginPassword()
  if (loginErrors.value.email || loginErrors.value.password) return

  loading.value = true
  try {
    const response = await login(loginEmail.value, loginPassword.value)
    if (response?.token) {
      await Swal.fire({
        icon: 'success',
        title: 'Benvingut a FilmGalaxy!',
        text: 'Inici de sessió correcte. Que gaudeixis de la sessió! 🍿',
        showConfirmButton: false,
        timer: 1800,
        timerProgressBar: true,
        background: '#161a2e',
        color: '#F2E9E4',
        iconColor: '#C8A96E',
        customClass: {
          popup: 'swal-cinema-popup',
          title: 'swal-cinema-title',
          htmlContainer: 'swal-cinema-content',
          timerProgressBar: 'swal-cinema-progress'
        }
      })
      router.push('/')
    } else {
      throw new Error(response?.error || 'Credencials incorrectes')
    }
  } catch (error) {
    console.error('Login error:', error)
    await Swal.fire({
      icon: 'error',
      title: 'Error d\'accés',
      text: error?.message || 'Credencials incorrectes. Si us plau, revisa el teu correu i contrasenya.',
      confirmButtonText: 'Tornar a provar',
      background: '#161a2e',
      color: '#F2E9E4',
      confirmButtonColor: '#C8A96E',
      customClass: {
        popup: 'swal-cinema-popup',
        confirmButton: 'swal-cinema-confirm'
      }
    })
  } finally {
    loading.value = false
  }
}

// Register Submission
const handleRegisterSubmit = async () => {
  validateRegisterEmail()
  checkPasswordStrength()
  if (registerErrors.value.email || registerErrors.value.password) return

  if (registerData.value.password.length < 8) {
    registerErrors.value.password = 'La contrasenya ha de tenir com a mínim 8 caràcters'
    return
  }

  loading.value = true
  try {
    const result = await register(
      registerData.value.nombre,
      registerData.value.apellido,
      registerData.value.email,
      registerData.value.telefono,
      registerData.value.password
    )

    if (!result?.error) {
      await Swal.fire({
        icon: 'success',
        title: 'Compte creat amb èxit! 🎉',
        text: 'Benvingut al club de FilmGalaxy. Ara pots iniciar sessió.',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#161a2e',
        color: '#F2E9E4',
        iconColor: '#C8A96E',
        customClass: {
          popup: 'swal-cinema-popup',
          title: 'swal-cinema-title',
          htmlContainer: 'swal-cinema-content',
          timerProgressBar: 'swal-cinema-progress'
        }
      })
      // Switch to login tab and prefill email
      loginEmail.value = registerData.value.email
      switchTab('login')
    } else {
      throw new Error(result.error)
    }
  } catch (error) {
    console.error('Registration error:', error)
    await Swal.fire({
      icon: 'error',
      title: 'Error en el registre',
      text: error?.message || 'No s\'ha pogut completar el registre. Revisa les teves dades.',
      confirmButtonText: 'Entès',
      background: '#161a2e',
      color: '#F2E9E4',
      confirmButtonColor: '#C8A96E',
      customClass: {
        popup: 'swal-cinema-popup',
        confirmButton: 'swal-cinema-confirm'
      }
    })
  } finally {
    loading.value = false
  }
}

const handleForgotPassword = () => {
  Swal.fire({
    title: 'Recuperar contrasenya',
    text: 'Introdueix el teu correu i t\'enviarem instruccions per restablir el teu accés.',
    input: 'email',
    inputPlaceholder: 'nom@exemple.cat',
    showCancelButton: true,
    confirmButtonText: 'Enviar enllaç',
    cancelButtonText: 'Cancel·lar',
    background: '#161a2e',
    color: '#F2E9E4',
    confirmButtonColor: '#C8A96E',
    cancelButtonColor: '#4A4E69',
    customClass: {
      popup: 'swal-cinema-popup',
      confirmButton: 'swal-cinema-confirm'
    }
  }).then((res) => {
    if (res.isConfirmed && res.value) {
      Swal.fire({
        icon: 'success',
        title: 'Correu enviat',
        text: `Hem enviat les instruccions a ${res.value}`,
        background: '#161a2e',
        color: '#F2E9E4',
        confirmButtonColor: '#C8A96E'
      })
    }
  })
}
</script>

<style scoped>
/* Page Layout & Background */
.auth-page-wrapper {
  position: relative;
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 15% 15%, #1f2544 0%, #0c0e1a 50%, #07080f 100%);
  padding: 1.5rem 1rem;
  overflow: hidden;
  color: #F2E9E4;
}

/* Ambient Animated Glowing Nebulae */
.ambient-nebula {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  pointer-events: none;
  opacity: 0.45;
  will-change: transform;
}

.nebula-1 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, #c8a96e 0%, rgba(200, 169, 110, 0) 70%);
  top: -100px;
  left: -100px;
  animation: floatOrb1 18s ease-in-out infinite alternate;
}

.nebula-2 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, #5b3d7a 0%, rgba(91, 61, 122, 0) 70%);
  bottom: -150px;
  right: -100px;
  animation: floatOrb2 22s ease-in-out infinite alternate;
}

.nebula-3 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, #2b3b75 0%, rgba(43, 59, 117, 0) 70%);
  top: 40%;
  left: 60%;
  animation: floatOrb3 15s ease-in-out infinite alternate;
}

/* Projector Beam Effect */
.projector-light {
  position: absolute;
  top: 0;
  left: 20%;
  width: 60%;
  height: 100%;
  background: radial-gradient(ellipse at 50% 0%, rgba(200, 169, 110, 0.08) 0%, transparent 60%);
  pointer-events: none;
}

/* Stars Layer */
.stars-layer {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.star {
  position: absolute;
  width: 2px;
  height: 2px;
  background-color: #ffffff;
  border-radius: 50%;
  opacity: 0.3;
  animation: twinkle 4s ease-in-out infinite;
}

.star-1 { top: 12%; left: 8%; animation-delay: 0.2s; }
.star-2 { top: 25%; left: 85%; animation-delay: 1.1s; width: 3px; height: 3px; }
.star-3 { top: 78%; left: 12%; animation-delay: 0.7s; }
.star-4 { top: 65%; left: 92%; animation-delay: 2.3s; }
.star-5 { top: 40%; left: 3%; animation-delay: 1.8s; }
.star-6 { top: 88%; left: 75%; animation-delay: 3.1s; }
.star-7 { top: 15%; left: 62%; animation-delay: 0.5s; width: 3px; height: 3px; }
.star-8 { top: 52%; left: 96%; animation-delay: 2.7s; }
.star-9 { top: 92%; left: 32%; animation-delay: 1.4s; }
.star-10 { top: 8%; left: 38%; animation-delay: 3.5s; }
.star-11 { top: 34%; left: 72%; animation-delay: 0.9s; }
.star-12 { top: 61%; left: 45%; animation-delay: 2.1s; width: 3px; height: 3px; }

/* Main Glassmorphic Container */
.auth-container {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 1100px;
}

.auth-card {
  display: flex;
  background: rgba(22, 26, 46, 0.85);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(200, 169, 110, 0.25);
  box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 40px rgba(200, 169, 110, 0.08);
  border-radius: 28px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Left Showcase Panel */
.cinema-showcase {
  flex: 1.1;
  background: linear-gradient(145deg, rgba(34, 34, 59, 0.85) 0%, rgba(15, 17, 30, 0.95) 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  padding: 3rem 2.75rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
}

.cinema-showcase::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  width: 1px;
  background: linear-gradient(to bottom, transparent, rgba(200, 169, 110, 0.4), transparent);
}

.showcase-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #9A8C98;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.25s ease;
  margin-bottom: 2rem;
  align-self: flex-start;
}

.back-link:hover {
  color: #C8A96E;
  transform: translateX(-4px);
}

.brand-section {
  margin-bottom: 2rem;
}

.logo-wrapper {
  margin-bottom: 1.25rem;
}

.brand-logo {
  height: 52px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 4px 12px rgba(200, 169, 110, 0.3));
}

.badge-vip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.35rem 0.85rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.3);
  border-radius: 9999px;
  color: #C8A96E;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 1rem;
}

.showcase-title {
  font-size: 2.1rem;
  font-weight: 800;
  line-height: 1.25;
  color: #F2E9E4;
  margin-bottom: 1rem;
  letter-spacing: -0.5px;
}

.text-gold-gradient {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.showcase-description {
  color: #C9ADA7;
  font-size: 0.975rem;
  line-height: 1.6;
}

/* Perks List */
.perks-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin: 1.5rem 0 2rem 0;
}

.perk-item {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.perk-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: rgba(200, 169, 110, 0.12);
  border: 1px solid rgba(200, 169, 110, 0.25);
  color: #C8A96E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.perk-text {
  display: flex;
  flex-direction: column;
}

.perk-text strong {
  color: #F2E9E4;
  font-size: 0.925rem;
  font-weight: 600;
}

.perk-text span {
  color: #9A8C98;
  font-size: 0.8rem;
}

/* Ticket Preview Mockup */
.ticket-preview {
  position: relative;
  background: rgba(255, 255, 255, 0.04);
  border: 1px dashed rgba(200, 169, 110, 0.35);
  border-radius: 14px;
  padding: 1rem 1.5rem;
  margin-top: auto;
}

.ticket-notch {
  position: absolute;
  width: 16px;
  height: 16px;
  background: #0d101d;
  border-radius: 50%;
  top: 50%;
  transform: translateY(-50%);
}

.notch-left {
  left: -8px;
  border-right: 1px dashed rgba(200, 169, 110, 0.35);
}

.notch-right {
  right: -8px;
  border-left: 1px dashed rgba(200, 169, 110, 0.35);
}

.ticket-body {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ticket-info {
  display: flex;
  flex-direction: column;
}

.ticket-tag {
  color: #C8A96E;
  font-size: 0.725rem;
  font-weight: 700;
  letter-spacing: 1px;
}

.ticket-hall {
  color: #F2E9E4;
  font-size: 0.85rem;
  font-weight: 600;
}

.ticket-barcode {
  font-family: monospace;
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.85rem;
  letter-spacing: 2px;
}

/* Right Form Section */
.auth-form-section {
  flex: 1;
  padding: 3rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.mobile-back {
  color: #9A8C98;
  font-size: 1.5rem;
  text-decoration: none;
}

.mobile-logo {
  height: 38px;
  width: auto;
}

/* Tab Selector */
.tab-selector-container {
  margin-bottom: 2rem;
}

.tab-selector {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: rgba(10, 12, 22, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 4px;
}

.tab-btn {
  position: relative;
  z-index: 2;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  font-weight: 600;
  color: #9A8C98;
  background: transparent;
  border: none;
  cursor: pointer;
  border-radius: 12px;
  transition: color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tab-btn.active {
  color: #121526;
}

.tab-indicator {
  position: absolute;
  top: 4px;
  bottom: 4px;
  left: 4px;
  width: calc(50% - 4px);
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(200, 169, 110, 0.3);
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1;
}

/* Form Styles */
.form-wrapper {
  display: flex;
  flex-direction: column;
}

.form-heading {
  margin-bottom: 1.5rem;
}

.form-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.4rem;
}

.form-subtitle {
  color: #9A8C98;
  font-size: 0.88rem;
}

.input-group {
  display: flex;
  flex-direction: column;
}

.input-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #EAE0D5;
  margin-bottom: 0.4rem;
}

.input-container {
  position: relative;
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  transition: all 0.25s ease;
}

.input-container:hover {
  border-color: rgba(255, 255, 255, 0.25);
  background: rgba(255, 255, 255, 0.06);
}

.input-container:focus-within {
  border-color: #C8A96E;
  background: rgba(255, 255, 255, 0.07);
  box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.2);
}

.input-container.has-error {
  border-color: #f87171 !important;
  box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.15) !important;
}

.input-icon {
  position: absolute;
  left: 14px;
  color: #9A8C98;
  font-size: 1.25rem;
  pointer-events: none;
}

.input-container input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  color: #F2E9E4;
  font-size: 0.95rem;
  padding: 0.85rem 1rem 0.85rem 44px;
}

.input-container input::placeholder {
  color: #6c7293;
}

.toggle-visibility-btn {
  background: transparent;
  border: none;
  color: #9A8C98;
  font-size: 1.2rem;
  padding: 0 14px;
  cursor: pointer;
  transition: color 0.2s ease;
  display: flex;
  align-items: center;
}

.toggle-visibility-btn:hover {
  color: #C8A96E;
}

.error-text {
  color: #f87171;
  font-size: 0.775rem;
  margin-top: 0.3rem;
  padding-left: 0.25rem;
}

.forgot-link {
  background: transparent;
  border: none;
  color: #C8A96E;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #F5D78E;
  text-decoration: underline;
}

/* Password Strength Meter */
.password-meter {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 0.4rem;
  gap: 0.75rem;
}

.meter-bars {
  display: flex;
  gap: 4px;
  flex: 1;
}

.meter-bars .bar {
  height: 4px;
  flex: 1;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  transition: all 0.3s ease;
}

.bar-weak { background: #f87171 !important; }
.bar-med { background: #fbbf24 !important; }
.bar-good { background: #34d399 !important; }
.bar-strong { background: #10b981 !important; box-shadow: 0 0 8px rgba(16, 185, 129, 0.5); }

.meter-text {
  font-size: 0.75rem;
  font-weight: 600;
}

.terms-note {
  font-size: 0.775rem;
  color: #8c8291;
  line-height: 1.4;
  margin: 0.5rem 0;
}

/* Action Button */
.btn-gold-action {
  width: 100%;
  padding: 0.95rem 1.5rem;
  margin-top: 0.5rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
  color: #121526;
  border: none;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(200, 169, 110, 0.35);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-gold-action:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(200, 169, 110, 0.5);
  background: linear-gradient(135deg, #FFF0B8 0%, #D4B679 50%, #C8A96E 100%);
}

.btn-gold-action:active:not(:disabled) {
  transform: translateY(0);
}

.btn-gold-action:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Spinner */
.spinner {
  width: 22px;
  height: 22px;
  border: 3px solid rgba(18, 21, 38, 0.3);
  border-top-color: #121526;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

/* Form Footer */
.form-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  margin-top: 1.75rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 0.875rem;
  color: #9A8C98;
}

.switch-link {
  background: transparent;
  border: none;
  color: #C8A96E;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.switch-link:hover {
  color: #F5D78E;
  text-decoration: underline;
}

/* Tab Fade-Slide Animation */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(12px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

/* Media Query Responsive */
@media (max-width: 960px) {
  .cinema-showcase {
    display: none !important;
  }

  .auth-form-section {
    padding: 2.25rem 1.75rem;
  }

  .auth-card {
    max-width: 520px;
    margin: 0 auto;
  }
}

/* Keyframes */
@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes floatOrb1 {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(60px, 40px) scale(1.1); }
  100% { transform: translate(-30px, 80px) scale(0.95); }
}

@keyframes floatOrb2 {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(-70px, -50px) scale(1.15); }
  100% { transform: translate(40px, -90px) scale(0.9); }
}

@keyframes floatOrb3 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(-50px, 40px); }
}

@keyframes twinkle {
  0%, 100% { opacity: 0.2; transform: scale(0.8); }
  50% { opacity: 0.85; transform: scale(1.3); }
}

/* Accessibility: prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .ambient-nebula,
  .star,
  .tab-indicator,
  .btn-gold-action,
  .back-link {
    animation: none !important;
    transition: none !important;
  }
}

/* SweetAlert2 Custom Cinema Styles */
:global(.swal-cinema-popup) {
  border-radius: 20px !important;
  border: 1px solid rgba(200, 169, 110, 0.3) !important;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8) !important;
}

:global(.swal-cinema-title) {
  color: #F2E9E4 !important;
  font-weight: 700 !important;
}

:global(.swal-cinema-content) {
  color: #C9ADA7 !important;
}

:global(.swal-cinema-confirm) {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%) !important;
  color: #121526 !important;
  font-weight: 700 !important;
  border-radius: 10px !important;
  padding: 0.6rem 1.8rem !important;
}

:global(.swal-cinema-progress) {
  background: #C8A96E !important;
}
</style>
