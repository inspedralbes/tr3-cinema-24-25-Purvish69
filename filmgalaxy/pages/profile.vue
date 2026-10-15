<template>
  <v-app class="profile-v-app">
    <!-- Navbar Component -->
    <Navbar />

    <div class="profile-page-wrapper">
      <!-- Ambient Cosmic Glows -->
      <div class="ambient-glow glow-1" aria-hidden="true"></div>
      <div class="ambient-glow glow-2" aria-hidden="true"></div>
      <div class="ambient-glow glow-3" aria-hidden="true"></div>
      <div class="projector-beam" aria-hidden="true"></div>

      <!-- Twinkling Starfield -->
      <div class="starfield" aria-hidden="true">
        <span v-for="n in 24" :key="n" :class="`star-dot dot-${n}`"></span>
      </div>

      <ClientOnly>
        <main class="profile-main-layout">
          <div class="profile-card-container">

            <!-- Loading State -->
            <div v-if="loading" class="cinema-state-box">
              <div class="cinema-reel-spinner">
                <i class="mdi mdi-filmstrip"></i>
              </div>
              <h3 class="state-title">Carregant perfil...</h3>
              <p class="state-subtitle">Recuperant la informació del teu compte de cinema</p>
            </div>

            <!-- Error / Unauthenticated State -->
            <div v-else-if="!userData" class="cinema-state-box">
              <div class="state-icon-alert">
                <i class="mdi mdi-account-off-outline"></i>
              </div>
              <h3 class="state-title">Sessió no iniciada</h3>
              <p class="state-subtitle">No hem pogut carregar les dades del teu perfil o la sessió ha expirat.</p>
              <div class="state-btn-row">
                <button @click="loadUserData" class="btn-secondary-action">
                  <i class="mdi mdi-refresh"></i>
                  Tornar a provar
                </button>
                <NuxtLink to="/login" class="btn-gold-action">
                  <i class="mdi mdi-login"></i>
                  Iniciar sessió
                </NuxtLink>
              </div>
            </div>

            <!-- Active Seamless Profile Card -->
            <div v-else class="seamless-profile-card">
              
              <!-- Card Top Ambient Glow & VIP Pill -->
              <div class="card-hero-header">
                <div class="vip-crown-badge">
                  <i class="mdi mdi-crown"></i>
                  <span>SOCI VIP FILMGALAXY</span>
                </div>

                <!-- Centered Glowing Avatar -->
                <div class="avatar-center-wrapper">
                  <div class="avatar-halo"></div>
                  <div class="avatar-frame">
                    <span v-if="userInitials" class="avatar-letters">{{ userInitials }}</span>
                    <i v-else class="mdi mdi-account text-4xl text-amber-300"></i>
                  </div>
                  <div class="avatar-star-badge" title="Soci VIP">
                    <i class="mdi mdi-star"></i>
                  </div>
                </div>

                <!-- User Name & Bio Subtitle -->
                <h1 class="user-name-title">
                  {{ userData.nombre }} {{ userData.apellido }}
                </h1>
                <p class="user-email-text">
                  <i class="mdi mdi-email-check-outline"></i>
                  {{ userData.email }}
                </p>

                <!-- Status Chips -->
                <div class="status-chips-row">
                  <span class="chip-status-active">
                    <span class="live-dot"></span>
                    Compte Actiu
                  </span>
                  <span class="chip-status-vip">
                    <i class="mdi mdi-ticket-percent-outline"></i>
                    Descompte Dia de l'Espectador
                  </span>
                </div>
              </div>

              <!-- Sleek Stats Bar -->
              <div class="stats-cards-bar">
                <NuxtLink to="/billetesDeUsuario" class="mini-stat-tile">
                  <div class="stat-icon-circle gold">
                    <i class="mdi mdi-ticket-confirmation-outline"></i>
                  </div>
                  <div class="stat-details">
                    <span class="stat-muted">Les Meves Entrades</span>
                    <span class="stat-action-link">Veure reserves →</span>
                  </div>
                </NuxtLink>

                <div class="mini-stat-tile">
                  <div class="stat-icon-circle purple">
                    <i class="mdi mdi-star-shooting-outline"></i>
                  </div>
                  <div class="stat-details">
                    <span class="stat-muted">Punts Galaxy</span>
                    <strong class="stat-main-val">350 <small class="unit">PTS</small></strong>
                  </div>
                </div>

                <div class="mini-stat-tile">
                  <div class="stat-icon-circle blue">
                    <i class="mdi mdi-movie-filter-outline"></i>
                  </div>
                  <div class="stat-details">
                    <span class="stat-muted">Experiència</span>
                    <strong class="stat-main-val">Sala VIP 4K</strong>
                  </div>
                </div>
              </div>

              <!-- Personal Information Tiles -->
              <div class="info-tiles-section">
                <h2 class="section-title">
                  <i class="mdi mdi-card-account-details-outline"></i>
                  Dades Personals
                </h2>

                <div class="tiles-grid">
                  <!-- Nom -->
                  <div class="tile-item">
                    <div class="tile-icon-box">
                      <i class="mdi mdi-account-outline"></i>
                    </div>
                    <div class="tile-content">
                      <span class="tile-label">Nom</span>
                      <strong class="tile-value">{{ userData.nombre || '—' }}</strong>
                    </div>
                  </div>

                  <!-- Cognom -->
                  <div class="tile-item">
                    <div class="tile-icon-box">
                      <i class="mdi mdi-account-multiple-outline"></i>
                    </div>
                    <div class="tile-content">
                      <span class="tile-label">Cognom</span>
                      <strong class="tile-value">{{ userData.apellido || '—' }}</strong>
                    </div>
                  </div>

                  <!-- Correu -->
                  <div class="tile-item">
                    <div class="tile-icon-box">
                      <i class="mdi mdi-email-outline"></i>
                    </div>
                    <div class="tile-content">
                      <span class="tile-label">Correu electrònic</span>
                      <strong class="tile-value break-all">{{ userData.email || '—' }}</strong>
                    </div>
                  </div>

                  <!-- Telèfon -->
                  <div class="tile-item">
                    <div class="tile-icon-box">
                      <i class="mdi mdi-phone-outline"></i>
                    </div>
                    <div class="tile-content">
                      <span class="tile-label">Telèfon mòbil</span>
                      <strong class="tile-value">{{ userData.telefono || 'No especificat' }}</strong>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cinema Quick Shortcuts & Logout -->
              <div class="card-footer-actions">
                <div class="shortcuts-row">
                  <NuxtLink to="/movies" class="btn-action-cinema primary">
                    <i class="mdi mdi-movie-open-play-outline"></i>
                    <span>Explorar Cartellera</span>
                  </NuxtLink>
                  <NuxtLink to="/billetesDeUsuario" class="btn-action-cinema secondary">
                    <i class="mdi mdi-ticket-account"></i>
                    <span>Entrades Comprades</span>
                  </NuxtLink>
                </div>

                <div class="logout-container">
                  <button 
                    type="button" 
                    class="btn-logout-styled"
                    @click="handleLogoutConfirm"
                  >
                    <i class="mdi mdi-logout-variant"></i>
                    <span>Tancar Sessió</span>
                  </button>
                </div>
              </div>

            </div>

          </div>
        </main>
      </ClientOnly>
      <Footer />
    </div>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import useAuth from '~/composables/useAuth'
import Swal from 'sweetalert2'

useHead({
  title: 'FilmGalaxy - El Meu Perfil',
  meta: [
    { name: 'description', content: 'Gestiona el teu compte de soci de FilmGalaxy i consulta les teves entrades de cinema.' }
  ]
})

const router = useRouter()
const auth = useAuth()
const { getUserData, logout } = auth

const userData = ref(null)
const loading = ref(true)

// User initials computation
const userInitials = computed(() => {
  if (!userData.value) return ''
  const first = userData.value.nombre ? userData.value.nombre[0].toUpperCase() : ''
  const last = userData.value.apellido ? userData.value.apellido[0].toUpperCase() : ''
  return `${first}${last}`
})

// Cookie helper
const getCookie = (name) => {
  if (typeof document === 'undefined') return null
  const cookies = document.cookie.split('; ').reduce((prev, current) => {
    const [cookieName, value] = current.split('=')
    if (cookieName) prev[cookieName] = value
    return prev
  }, {})
  return cookies[name]
}

const loadUserData = async () => {
  try {
    loading.value = true
    const response = await getUserData()
    if (response) {
      userData.value = response
    } else {
      const cookieUserId = getCookie('userId')
      if (cookieUserId) {
        const retryResponse = await getUserData()
        if (retryResponse) userData.value = retryResponse
      }
    }
  } catch (error) {
    console.error('Error al carregar les dades de l\'usuari:', error)
  } finally {
    loading.value = false
  }
}

const handleLogoutConfirm = () => {
  Swal.fire({
    title: 'Vols tancar la sessió?',
    text: 'Hauràs d\'iniciar sessió de nou per reservar butaques o consultar les teves entrades.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, tancar sessió',
    cancelButtonText: 'Cancel·lar',
    background: '#161a2e',
    color: '#F2E9E4',
    confirmButtonColor: '#e11d48',
    cancelButtonColor: '#4A4E69',
    iconColor: '#C8A96E',
    customClass: {
      popup: 'swal-cinema-popup',
      title: 'swal-cinema-title',
      confirmButton: 'swal-cinema-danger-btn'
    }
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        if (typeof document !== 'undefined') {
          document.cookie = "userId=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
          document.cookie = "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
          localStorage.removeItem('token')
          localStorage.removeItem('userId')
        }
        await logout()
        userData.value = null

        await Swal.fire({
          icon: 'success',
          title: 'Sessió tancada',
          text: 'Fins aviat a FilmGalaxy! 🎬',
          showConfirmButton: false,
          timer: 1500,
          background: '#161a2e',
          color: '#F2E9E4',
          customClass: {
            popup: 'swal-cinema-popup'
          }
        })
        router.push('/login')
      } catch (err) {
        console.error('Error logout:', err)
        router.push('/login')
      }
    }
  })
}

onMounted(() => {
  loadUserData()
})
</script>

<style scoped>
/* Vuetify App Level Root */
.profile-v-app {
  background: #080a14 !important;
  min-height: 100vh;
}

:deep(.v-application__wrap) {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: transparent !important;
}

/* Page Layout */
.profile-page-wrapper {
  position: relative;
  min-height: calc(100vh - 76px);
  width: 100%;
  background: radial-gradient(circle at 50% 0%, #1a203a 0%, #0c0f1c 45%, #06070e 100%);
  color: #F2E9E4;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.profile-main-layout {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 1.25rem 3.5rem 1.25rem;
  position: relative;
  z-index: 10;
}

.profile-card-container {
  width: 100%;
  max-width: 760px;
  margin: 0 auto;
}

/* Ambient Floating Lights */
.ambient-glow {
  position: fixed;
  border-radius: 50%;
  filter: blur(120px);
  pointer-events: none;
  opacity: 0.35;
  will-change: transform;
  z-index: 1;
}

.glow-1 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, #C8A96E 0%, transparent 70%);
  top: 5%;
  left: -100px;
  animation: orbDrift1 20s ease-in-out infinite alternate;
}

.glow-2 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, #5b3d7a 0%, transparent 70%);
  bottom: -50px;
  right: -100px;
  animation: orbDrift2 24s ease-in-out infinite alternate;
}

.glow-3 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, #253366 0%, transparent 70%);
  top: 40%;
  left: 60%;
  animation: orbDrift3 16s ease-in-out infinite alternate;
}

.projector-beam {
  position: fixed;
  top: 0;
  left: 20%;
  width: 60%;
  height: 60%;
  background: radial-gradient(ellipse at 50% 0%, rgba(200, 169, 110, 0.07) 0%, transparent 70%);
  pointer-events: none;
  z-index: 1;
}

/* Starfield */
.starfield {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 2;
}

.star-dot {
  position: absolute;
  width: 2px;
  height: 2px;
  background-color: #fff;
  border-radius: 50%;
  opacity: 0.25;
  animation: starTwinkle 4s ease-in-out infinite;
}

.dot-1 { top: 12%; left: 10%; animation-delay: 0.3s; }
.dot-2 { top: 25%; left: 85%; animation-delay: 1.1s; width: 3px; height: 3px; }
.dot-3 { top: 70%; left: 15%; animation-delay: 2.3s; }
.dot-4 { top: 80%; left: 75%; animation-delay: 0.7s; }
.dot-5 { top: 40%; left: 5%; animation-delay: 1.8s; }
.dot-6 { top: 18%; left: 50%; animation-delay: 2.8s; }
.dot-7 { top: 62%; left: 92%; animation-delay: 0.5s; width: 3px; height: 3px; }
.dot-8 { top: 88%; left: 38%; animation-delay: 3.2s; }

/* SEAMLESS PROFILE CARD */
.seamless-profile-card {
  background: linear-gradient(180deg, rgba(25, 30, 52, 0.9) 0%, rgba(16, 19, 34, 0.95) 100%);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  border: 1px solid rgba(200, 169, 110, 0.22);
  border-radius: 28px;
  box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.75), 0 0 35px rgba(200, 169, 110, 0.06);
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.seamless-profile-card:hover {
  box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.85), 0 0 45px rgba(200, 169, 110, 0.1);
}

/* Card Hero Header */
.card-hero-header {
  position: relative;
  padding: 2.5rem 2rem 1.75rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  background: radial-gradient(circle at 50% 0%, rgba(200, 169, 110, 0.15) 0%, transparent 65%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.vip-crown-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.35);
  padding: 0.35rem 0.95rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: #F5D78E;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 10px rgba(200, 169, 110, 0.15);
}

/* Avatar Styling */
.avatar-center-wrapper {
  position: relative;
  margin-bottom: 1.25rem;
}

.avatar-halo {
  position: absolute;
  inset: -6px;
  background: radial-gradient(circle, #C8A96E 0%, transparent 70%);
  border-radius: 50%;
  filter: blur(12px);
  opacity: 0.65;
  animation: haloPulse 3s ease-in-out infinite alternate;
}

.avatar-frame {
  position: relative;
  width: 104px;
  height: 104px;
  border-radius: 50%;
  padding: 3px;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.avatar-letters {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: #15182a;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.2rem;
  font-weight: 800;
  background: linear-gradient(135deg, #FFF0B8 0%, #F5D78E 50%, #C8A96E 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 1px;
}

.avatar-star-badge {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, #F5D78E, #C8A96E);
  color: #121526;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  border: 2px solid #161a2e;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

/* User Headline */
.user-name-title {
  font-size: 1.85rem;
  font-weight: 800;
  color: #F2E9E4;
  margin-bottom: 0.25rem;
  letter-spacing: -0.3px;
}

.user-email-text {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #9A8C98;
  font-size: 0.925rem;
  margin-bottom: 0.85rem;
}

.status-chips-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
}

.chip-status-active {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.35);
  color: #34d399;
}

.live-dot {
  width: 7px;
  height: 7px;
  background-color: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 6px #10b981;
}

.chip-status-vip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.35);
  color: #F5D78E;
}

/* Stats Cards Bar */
.stats-cards-bar {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  padding: 1.75rem 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.mini-stat-tile {
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 1rem 1.15rem;
  display: flex;
  align-items: center;
  gap: 0.85rem;
  text-decoration: none;
  transition: all 0.25s ease;
}

.mini-stat-tile:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(200, 169, 110, 0.4);
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.3);
}

.stat-icon-circle {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}

.stat-icon-circle.gold {
  background: rgba(200, 169, 110, 0.15);
  border: 1px solid rgba(200, 169, 110, 0.3);
  color: #C8A96E;
}

.stat-icon-circle.purple {
  background: rgba(147, 51, 234, 0.15);
  border: 1px solid rgba(147, 51, 234, 0.3);
  color: #c084fc;
}

.stat-icon-circle.blue {
  background: rgba(59, 130, 246, 0.15);
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: #60a5fa;
}

.stat-details {
  display: flex;
  flex-direction: column;
}

.stat-muted {
  font-size: 0.75rem;
  color: #9A8C98;
  font-weight: 500;
}

.stat-action-link {
  font-size: 0.88rem;
  font-weight: 700;
  color: #F5D78E;
  margin-top: 0.1rem;
}

.stat-main-val {
  font-size: 1.15rem;
  font-weight: 800;
  color: #F2E9E4;
  margin-top: 0.05rem;
}

.stat-main-val .unit {
  font-size: 0.75rem;
  color: #F5D78E;
}

/* Info Tiles Section */
.info-tiles-section {
  padding: 1.75rem 2rem;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 1.15rem;
}

.section-title i {
  color: #C8A96E;
  font-size: 1.25rem;
}

.tiles-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.85rem;
}

.tile-item {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 14px;
  padding: 0.95rem 1.15rem;
  display: flex;
  align-items: center;
  gap: 0.85rem;
  transition: all 0.25s ease;
}

.tile-item:hover {
  background: rgba(255, 255, 255, 0.055);
  border-color: rgba(200, 169, 110, 0.3);
}

.tile-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: rgba(200, 169, 110, 0.1);
  color: #C8A96E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.tile-content {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.tile-label {
  font-size: 0.75rem;
  color: #9A8C98;
  font-weight: 500;
  margin-bottom: 0.1rem;
}

.tile-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #F2E9E4;
}

/* Card Footer Actions */
.card-footer-actions {
  padding: 1.5rem 2rem 2rem 2rem;
  background: rgba(12, 15, 27, 0.55);
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.shortcuts-row {
  display: flex;
  gap: 1rem;
}

.btn-action-cinema {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.85rem 1.25rem;
  border-radius: 14px;
  font-weight: 700;
  font-size: 0.925rem;
  text-decoration: none;
  transition: all 0.25s ease;
}

.btn-action-cinema.primary {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 50%, #B89659 100%);
  color: #121526;
  box-shadow: 0 4px 18px rgba(200, 169, 110, 0.3);
}

.btn-action-cinema.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(200, 169, 110, 0.45);
}

.btn-action-cinema.secondary {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(200, 169, 110, 0.35);
  color: #F5D78E;
}

.btn-action-cinema.secondary:hover {
  background: rgba(200, 169, 110, 0.12);
  border-color: #C8A96E;
  color: #FFF0B8;
}

.logout-container {
  display: flex;
  justify-content: center;
}

.btn-logout-styled {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(244, 63, 94, 0.1);
  border: 1px solid rgba(244, 63, 94, 0.25);
  color: #fb7185;
  padding: 0.65rem 1.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-logout-styled:hover {
  background: rgba(244, 63, 94, 0.2);
  border-color: #f43f5e;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(244, 63, 94, 0.25);
}

/* State Box (Loading / Unauth) */
.cinema-state-box {
  background: rgba(22, 26, 46, 0.88);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(200, 169, 110, 0.25);
  border-radius: 24px;
  padding: 3.5rem 2rem;
  text-align: center;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
}

.cinema-reel-spinner {
  font-size: 3.2rem;
  color: #C8A96E;
  display: inline-block;
  animation: spinReel 2.5s linear infinite;
  margin-bottom: 1.25rem;
}

.state-icon-alert {
  font-size: 3.2rem;
  color: #fb7185;
  margin-bottom: 1.25rem;
}

.state-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #F2E9E4;
  margin-bottom: 0.4rem;
}

.state-subtitle {
  color: #9A8C98;
  font-size: 0.925rem;
  max-width: 400px;
  margin: 0 auto 1.75rem auto;
}

.state-btn-row {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.btn-secondary-action,
.btn-gold-action {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-secondary-action {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #F2E9E4;
}

.btn-secondary-action:hover {
  background: rgba(255, 255, 255, 0.15);
}

.btn-gold-action {
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  border: none;
}

.btn-gold-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(200, 169, 110, 0.35);
}

/* Keyframes */
@keyframes spinReel {
  to { transform: rotate(360deg); }
}

@keyframes haloPulse {
  0% { transform: scale(0.95); opacity: 0.5; }
  100% { transform: scale(1.1); opacity: 0.8; }
}

@keyframes orbDrift1 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(40px, 30px); }
}

@keyframes orbDrift2 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(-40px, -30px); }
}

@keyframes orbDrift3 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(-30px, 30px); }
}

@keyframes starTwinkle {
  0%, 100% { opacity: 0.2; transform: scale(0.8); }
  50% { opacity: 0.85; transform: scale(1.3); }
}

/* Responsive */
@media (max-width: 680px) {
  .stats-cards-bar {
    grid-template-columns: 1fr;
    padding: 1.25rem;
  }

  .tiles-grid {
    grid-template-columns: 1fr;
  }

  .shortcuts-row {
    flex-direction: column;
  }

  .card-hero-header,
  .info-tiles-section,
  .card-footer-actions {
    padding: 1.5rem 1.25rem;
  }
}
</style>
