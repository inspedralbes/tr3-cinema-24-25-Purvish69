<template>
    <!-- Desktop Navigation Bar -->
    <v-app-bar color="primary" elevation="4" height="80" class="hidden-sm-and-down">
        <v-toolbar-title class="text-cream font-weight-bold">
            <router-link to="/">
                <img src="../assets/logo/logofilmgalaxyTransparente.png" alt="Logo" class="logo-image desktop-logo" />
            </router-link>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <!-- Desktop-specific menu items -->
        <div class="d-flex align-center">
            <v-btn v-for="(item, i) in desktopMenuItems" :key="i" :to="item.to" class="mx-2" color="cream"
                variant="text">
                <v-icon :icon="item.icon" class="mr-2"></v-icon>
                {{ item.title }}
            </v-btn>

            <client-only>
                <v-btn icon class="ml-2" @click="handleProfileClick">
                    <v-icon :icon="isUserLoggedIn ? 'mdi-account-circle' : 'mdi-account'" color="cream"></v-icon>
                </v-btn>
            </client-only>
        </div>
    </v-app-bar>

    <!-- Mobile Navigation Bar -->
    <v-app-bar color="primary" elevation="4" height="60" class="hidden-md-and-up mobile-navbar" fixed>
        <v-app-bar-nav-icon color="cream" @click="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title class="text-cream font-weight-bold">
            <router-link to="/">
                <img src="../assets/logo/logofilmgalaxyTransparente.png" alt="Logo" class="logo-image mobile-logo" />
            </router-link>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <client-only>
            <v-btn icon class="mr-2" @click="handleProfileClick">
                <v-icon :icon="isUserLoggedIn ? 'mdi-account-circle' : 'mdi-account'" color="cream"></v-icon>
            </v-btn>
        </client-only>
    </v-app-bar>

    <!-- Mobile Navigation Drawer fixed to top -->
    <v-navigation-drawer v-model="drawer" temporary color="primary" location="start" class="mobile-drawer"
        :style="{ top: '60px', height: 'calc(100% - 60px)' }">
        <v-list color="cream">
            <v-list-item v-for="(item, i) in mobileMenuItems" :key="i" :to="item.to" :prepend-icon="item.icon"
                :title="item.title" color="cream"></v-list-item>
            <!-- Mobile-specific drawer items -->
            <v-divider class="my-2" color="cream" opacity="0.5"></v-divider>
            <client-only>
                <v-list-item :title="isUserLoggedIn ? 'El Meu Perfil' : 'Iniciar Sessió'"
                    :prepend-icon="isUserLoggedIn ? 'mdi-account-circle' : 'mdi-login'" @click="handleProfileClick"
                    color="cream">
                </v-list-item>
            </client-only>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '~/composables/useAuth'

const drawer = ref(false)
const router = useRouter()
const { token } = useAuth()
// Use ref with a default value for SSR compatibility
const isUserLoggedIn = computed(() => process.client ? !!token.value : false)

// Desktop-specific menu items
const desktopMenuItems = [
    { title: 'Inici', icon: 'mdi-home', to: '/' },
    { title: 'Pel·lícules', icon: 'mdi-movie', to: '/movies' },
    { title: 'Sessions', icon: 'mdi-ticket', to: '/movieDetails/moviSession' },
    { title: 'Entrades Comprades', icon: 'mdi-ticket-confirmation', to: '/billetesDeUsuario' },
    // { title: 'informacion', icon: 'mdi-phone', to: '/informacion' }
]

// Mobile-specific menu items - simplified
const mobileMenuItems = [
    { title: 'Inici', icon: 'mdi-home', to: '/' },
    { title: 'Pel·lícules', icon: 'mdi-movie', to: '/movies' },
    { title: 'Sessions', icon: 'mdi-ticket', to: '/movieDetails/moviSession' },
    { title: 'Entrades Comprades', icon: 'mdi-ticket-confirmation', to: '/billetesDeUsuario' },
    // { title: 'informacion', icon: 'mdi-phone', to: '/informacion' }
]

const handleProfileClick = () => {
    if (isUserLoggedIn.value) {
        router.push('/profile')
    } else {
        router.push('/login')
    }
    drawer.value = false
}
</script>

<style scoped>
.text-cream {
    color: #EAE0D5 !important;
}

.logo-image {
    height: 90px;
}

.desktop-logo {
    margin-right: 200px;
}

.mobile-logo {
    height: 70px;
    position: absolute;
    left: 170px;
    top: -5px;
    /* margin-top: 10px; */
}

.mobile-navbar {
    z-index: 2000;
    top: 0;
    position: fixed;
    margin-top: -85px;
}

.mobile-drawer {
    z-index: 1999 !important;
    overflow-y: auto;

}

/* Asegurar que el diseño general sea correcto para el drawer */
:deep(.v-navigation-drawer__content) {
    overflow-y: auto;
    /* padding-bottom: 1000px; */
}
</style>