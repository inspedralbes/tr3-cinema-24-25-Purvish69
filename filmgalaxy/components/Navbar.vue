<template>
  <v-app-bar 
    :class="['cinema-navbar', { 'is-scrolled': isScrolled }]"
    :elevation="0" 
    :height="isScrolled ? 56 : 74"
    flat
    color="transparent"
  >
    <div class="navbar-inner" :class="{ 'scrolled-inner': isScrolled }">
      <!-- Left: Brand Logo -->
      <div class="brand-container">
        <NuxtLink to="/" class="brand-link">
          <img 
            src="/logo/logofilmgalaxyTransparente.png" 
            alt="FilmGalaxy" 
            class="brand-logo-img" 
            :class="{ 'scrolled-logo': isScrolled }"
          />
        </NuxtLink>
      </div>

      <!-- Center: Desktop Navigation Links with animated indicators -->
      <nav class="desktop-nav hidden-sm-and-down">
        <NuxtLink 
          v-for="(item, i) in menuItems" 
          :key="i" 
          :to="item.to"
          class="nav-link"
          :class="{ 'scrolled-link': isScrolled }"
        >
          <i :class="['mdi', item.icon, 'nav-link-icon']"></i>
          <span class="nav-link-text">{{ item.title }}</span>
          <span class="nav-indicator"></span>
        </NuxtLink>
      </nav>

      <!-- Right: User Profile / Auth Area -->
      <div class="user-actions">
        <!-- Desktop Auth Action Button -->
        <client-only>
          <div class="hidden-sm-and-down">
            <button 
              v-if="isUserLoggedIn" 
              @click="handleLogout" 
              class="btn-logout-nav"
              :class="{ 'scrolled-login-btn': isScrolled }"
              title="Tancar Sessió"
            >
              <i class="mdi mdi-logout"></i>
              <span>Tancar Sessió</span>
            </button>

            <NuxtLink 
              v-else 
              to="/login" 
              class="btn-login-nav"
              :class="{ 'scrolled-login-btn': isScrolled }"
            >
              <i class="mdi mdi-login"></i>
              <span>Iniciar Sessió</span>
            </NuxtLink>
          </div>
        </client-only>

        <!-- Mobile Drawer Toggle Hamburger -->
        <button 
          type="button" 
          class="mobile-toggle-btn hidden-md-and-up"
          :class="{ 'scrolled-toggle': isScrolled }"
          @click="drawer = !drawer"
          aria-label="Menú"
        >
          <i :class="drawer ? 'mdi mdi-close' : 'mdi mdi-menu'"></i>
        </button>
      </div>
    </div>
  </v-app-bar>

  <!-- Mobile Navigation Drawer -->
  <v-navigation-drawer 
    v-model="drawer" 
    temporary 
    location="end" 
    class="mobile-cinema-drawer hidden-md-and-up"
  >
    <div class="drawer-header">
      <img src="/logo/logofilmgalaxyTransparente.png" alt="FilmGalaxy" class="drawer-logo" />
      <button class="drawer-close-btn" @click="drawer = false">
        <i class="mdi mdi-close"></i>
      </button>
    </div>

    <div class="drawer-content">
      <nav class="mobile-nav-list">
        <NuxtLink 
          v-for="(item, i) in menuItems" 
          :key="i" 
          :to="item.to"
          class="mobile-nav-link"
          @click="drawer = false"
        >
          <div class="mobile-link-icon-box">
            <i :class="['mdi', item.icon]"></i>
          </div>
          <span>{{ item.title }}</span>
          <i class="mdi mdi-chevron-right mobile-arrow"></i>
        </NuxtLink>
      </nav>

      <div class="drawer-divider"></div>

      <!-- Mobile User Status -->
      <client-only>
        <div class="drawer-footer-actions">
          <button 
            v-if="isUserLoggedIn" 
            @click="handleLogout" 
            class="mobile-logout-btn"
          >
            <i class="mdi mdi-logout mr-1"></i>
            Tancar Sessió
          </button>

          <NuxtLink 
            v-else 
            to="/login" 
            class="mobile-login-btn"
            @click="drawer = false"
          >
            <i class="mdi mdi-login mr-1"></i>
            Iniciar Sessió
          </NuxtLink>
        </div>
      </client-only>
    </div>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '~/composables/useAuth'

const drawer = ref(false)
const router = useRouter()
const { token, logout } = useAuth()

const isUserLoggedIn = computed(() => (process.client ? !!token.value : false))

const handleLogout = async () => {
  await logout()
  drawer.value = false
  router.push('/')
}

// Scroll listener to detect when user scrolls down
const isScrolled = ref(false)

const handleScroll = () => {
  if (typeof window !== 'undefined') {
    isScrolled.value = window.scrollY > 20
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', handleScroll)
  }
})

const menuItems = [
  { title: 'Inici', icon: 'mdi-home-variant-outline', to: '/' },
  { title: 'Pel·lícules', icon: 'mdi-movie-open-outline', to: '/movies' },
  { title: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/movieDetails/moviSession' },
  { title: 'Entrades Comprades', icon: 'mdi-ticket-confirmation-outline', to: '/billetesDeUsuario' },
  { title: 'El Meu Perfil', icon: 'mdi-account-circle-outline', to: '/profile' }
]
</script>

<style scoped>
/* Main Cinema Navbar: Subtle frosted transparency blending into cosmic background */
.cinema-navbar {
  background: rgba(12, 14, 26, 0.45) !important;
  backdrop-filter: blur(14px) !important;
  -webkit-backdrop-filter: blur(14px) !important;
  border-bottom: none !important;
  border: none !important;
  box-shadow: none !important;
  position: sticky !important;
  top: 0 !important;
  z-index: 100 !important;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

:deep(.v-toolbar__content) {
  background: transparent !important;
}

:deep(.v-toolbar__border) {
  display: none !important;
  opacity: 0 !important;
}

/* Scrolled state: shrinks softly and applies deeper frosted glass, strictly NO border line */
.cinema-navbar.is-scrolled {
  background: rgba(10, 12, 22, 0.85) !important;
  backdrop-filter: blur(18px) !important;
  -webkit-backdrop-filter: blur(18px) !important;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.45) !important;
  border: none !important;
  border-bottom: none !important;
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 1350px;
  height: 100%;
  margin: 0 auto;
  padding: 0 1.5rem;
  transition: padding 0.3s ease;
}

.scrolled-inner {
  padding: 0 1.25rem;
}

/* Brand */
.brand-container {
  display: flex;
  align-items: center;
}

.brand-link {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: transform 0.25s ease;
}

.brand-link:hover {
  transform: scale(1.03);
}

.brand-logo-img {
  height: 46px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.6));
  transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1), filter 0.3s ease;
}

/* Shrink logo smoothly on scroll */
.brand-logo-img.scrolled-logo {
  height: 35px;
}

/* Desktop Navigation */
.desktop-nav {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.nav-link {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.9rem;
  border-radius: 12px;
  color: #F2E9E4;
  font-size: 0.925rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8), 0 0 10px rgba(0, 0, 0, 0.5);
}

/* Shrink nav link padding on scroll */
.nav-link.scrolled-link {
  padding: 0.325rem 0.75rem;
  font-size: 0.875rem;
}

.nav-link-icon {
  font-size: 1.15rem;
  color: #EAE0D5;
  transition: transform 0.25s ease, color 0.25s ease;
}

.scrolled-link .nav-link-icon {
  font-size: 1.05rem;
}

.nav-link:hover {
  color: #F5D78E;
  background: rgba(255, 255, 255, 0.12);
}

.nav-link:hover .nav-link-icon {
  color: #F5D78E;
  transform: translateY(-1px);
}

/* Glowing Indicator Underline */
.nav-indicator {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #C8A96E 50%, transparent);
  border-radius: 9999px;
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-link:hover .nav-indicator,
.nav-link.router-link-active .nav-indicator,
.nav-link.router-link-exact-active .nav-indicator {
  width: 70%;
}

.nav-link.router-link-active,
.nav-link.router-link-exact-active {
  color: #F5D78E;
  font-weight: 600;
  background: rgba(200, 169, 110, 0.18);
  box-shadow: inset 0 0 12px rgba(200, 169, 110, 0.12);
}

.nav-link.router-link-active .nav-link-icon,
.nav-link.router-link-exact-active .nav-link-icon {
  color: #C8A96E;
}

.btn-logout-nav {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.45rem 1rem;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  color: #F2E9E4;
  font-size: 0.88rem;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-logout-nav.scrolled-login-btn {
  padding: 0.325rem 0.85rem;
  font-size: 0.825rem;
}

.btn-logout-nav:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.5);
  color: #fca5a5;
  transform: translateY(-1px);
}

/* User Actions */
.user-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.profile-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.9rem 0.35rem 0.4rem;
  background: rgba(16, 20, 36, 0.6);
  border: 1px solid rgba(200, 169, 110, 0.4);
  border-radius: 9999px;
  color: #F5D78E;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.25s ease;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
}

.profile-btn.scrolled-profile-btn {
  padding: 0.25rem 0.75rem 0.25rem 0.35rem;
  font-size: 0.825rem;
}

.profile-btn:hover,
.profile-btn.router-link-active {
  background: rgba(200, 169, 110, 0.25);
  border-color: #C8A96E;
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(200, 169, 110, 0.35);
}

.profile-avatar-mini {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #1f2544;
  color: #F5D78E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  transition: all 0.25s ease;
}

.profile-avatar-mini.scrolled-avatar {
  width: 24px;
  height: 24px;
  font-size: 1.1rem;
}

.vip-dot {
  width: 7px;
  height: 7px;
  background: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 6px #10b981;
}

.btn-login-nav {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.45rem 1.15rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-size: 0.88rem;
  font-weight: 700;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.25s ease;
  box-shadow: 0 4px 15px rgba(200, 169, 110, 0.3);
}

.btn-login-nav.scrolled-login-btn {
  padding: 0.325rem 0.9rem;
  font-size: 0.825rem;
}

.btn-login-nav:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(200, 169, 110, 0.45);
}

/* Mobile Toggle */
.mobile-toggle-btn {
  background: rgba(16, 20, 36, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #F2E9E4;
  font-size: 1.5rem;
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
  backdrop-filter: blur(8px);
}

.mobile-toggle-btn.scrolled-toggle {
  width: 36px;
  height: 36px;
  font-size: 1.3rem;
}

.mobile-toggle-btn:hover {
  background: rgba(200, 169, 110, 0.2);
  color: #F5D78E;
  border-color: #C8A96E;
}

/* Mobile Drawer */
.mobile-cinema-drawer {
  background: #0f1222 !important;
  border-left: 1px solid rgba(255, 255, 255, 0.08) !important;
  width: 290px !important;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.drawer-logo {
  height: 38px;
  width: auto;
}

.drawer-close-btn {
  background: transparent;
  border: none;
  color: #9A8C98;
  font-size: 1.5rem;
  cursor: pointer;
}

.drawer-content {
  padding: 1.25rem 1rem;
}

.mobile-nav-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.75rem 1rem;
  border-radius: 14px;
  color: #F2E9E4;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.mobile-link-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.06);
  color: #C8A96E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.mobile-link-icon-box.gold {
  background: rgba(200, 169, 110, 0.2);
}

.mobile-arrow {
  margin-left: auto;
  color: #6c7293;
}

.mobile-nav-link:hover,
.mobile-nav-link.router-link-active,
.mobile-nav-link.router-link-exact-active {
  background: rgba(200, 169, 110, 0.12);
  color: #F5D78E;
}

.mobile-nav-link.router-link-active .mobile-link-icon-box,
.mobile-nav-link.router-link-exact-active .mobile-link-icon-box {
  background: #C8A96E;
  color: #121526;
}

.drawer-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.08);
  margin: 1.5rem 0;
}

.drawer-footer-actions {
  display: flex;
  flex-direction: column;
}

.mobile-profile-link {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.75rem 1rem;
  border-radius: 14px;
  background: rgba(200, 169, 110, 0.1);
  border: 1px solid rgba(200, 169, 110, 0.25);
  text-decoration: none;
}

.mobile-login-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.85rem 1rem;
  background: linear-gradient(135deg, #F5D78E 0%, #C8A96E 100%);
  color: #121526;
  font-weight: 700;
  border-radius: 12px;
  text-decoration: none;
}

.mobile-logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.85rem 1rem;
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
  font-weight: 700;
  border-radius: 12px;
  cursor: pointer;
  width: 100%;
  transition: all 0.2s ease;
}

.mobile-logout-btn:hover {
  background: rgba(239, 68, 68, 0.25);
}
</style>