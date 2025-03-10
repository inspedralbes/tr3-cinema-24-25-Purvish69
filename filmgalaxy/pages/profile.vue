<template>
    <v-app>
        <Navbar />
        <ClientOnly>
            <div class="profile-container">
                <v-container>
                    <v-row justify="center">
                        <v-col cols="12" sm="10" md="8" lg="6">
                            <v-card class="profile-card" elevation="8">
                                <!-- Profile Header -->
                                <div class="profile-header">
                                    <v-avatar size="120" class="user-avatar elevation-5">
                                        <v-icon size="72" icon="mdi-account" color="#F2E9E4"></v-icon>
                                    </v-avatar>
                                </div>

                                <!-- Loading State -->
                                <div v-if="loading" class="text-center py-8">
                                    <v-progress-circular indeterminate color="#4A4E69" class="mb-4"
                                        size="56"></v-progress-circular>
                                    <p class="text-body-1">Cargando información de usuario...</p>
                                </div>

                                <!-- User Data -->
                                <v-card-text v-else-if="userData" class="user-details pa-6">
                                    <p><strong>ID:</strong> {{ userData.id }}</p>
                                    <p><strong>Nombre:</strong> {{ userData.nombre }}</p>
                                    <p><strong>Apellido:</strong> {{ userData.apellido }}</p>
                                    <p><strong>Email:</strong> {{ userData.email }}</p>
                                    <p v-if="userData.telefono">
                                        <strong>Teléfono:</strong> {{ userData.telefono }}
                                    </p>
                                </v-card-text>

                                <!-- Error State -->
                                <v-card-text v-else class="text-center py-6">
                                    <v-icon size="48" icon="mdi-alert-circle" color="#9A8C98" class="mb-4"></v-icon>
                                    <p class="text-body-1">No se pudo cargar la información del usuario.</p>
                                    <v-btn color="#4A4E69" class="mt-4" @click="loadUserData">
                                        Intentar nuevamente
                                    </v-btn>
                                </v-card-text>

                                <!-- Action Button: Logout -->
                                <v-card-actions v-if="userData"
                                    class="actions-container pa-6 d-flex gap-4 justify-center">
                                    <v-btn color="#9A8C98" size="large" @click="handleLogout" variant="elevated"
                                        prepend-icon="mdi-logout" class="logout-btn">
                                        Cerrar Sesión
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
        </ClientOnly>
    </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import useAuth from '~/composables/useAuth'

// En lugar de destructuring, usamos el objeto completo
const auth = useAuth()
const { getUserData, logout } = auth

const userData = ref(null)
const loading = ref(true)

const getCookie = (name) => {
    const cookies = document.cookie.split('; ').reduce((prev, current) => {
        const [cookieName, value] = current.split('=');
        prev[cookieName] = value;
        return prev;
    }, {});
    
    return cookies[name];
}

const loadUserData = async () => {
    try {
        loading.value = true
        
        // Obtener userId de las cookies directamente
        const userId = getCookie('userId');
        
        if (!userId) {
            console.error("No se encontró la cookie userId o es inválida");
            // Intentar obtener todos los usuarios de todas formas
            const response = await getUserData();
            
            if (!response || !response.users || response.users.length === 0) {
                console.error('No se pudieron obtener datos de usuarios');
                return;
            }
            
            // Si no hay userId pero hay usuarios, mostrar el primero (solo para desarrollo)
            console.warn('Usando el primer usuario por defecto (solo para desarrollo)');
            userData.value = response.users[0];
            return;
        }
        
        // Convertir a número
        const currentUserId = parseInt(userId);
        
        const response = await getUserData();

        if (response && response.users) {
            // Filtramos el array para obtener el usuario logueado
            const currentUser = response.users.find(user => user.id === currentUserId);

            if (currentUser) {
                userData.value = currentUser;
                console.log('Usuario logueado encontrado:', currentUser);
            } else {
                console.error('Usuario no encontrado en la lista de usuarios');
            }
        } else {
            console.error('Respuesta de usuario inesperada:', response);
        }
    } catch (error) {
        console.error('Error al cargar los datos del usuario:', error);
    } finally {
        loading.value = false;
        console.log('Carga de datos completada. Estado de loading:', loading.value);
    }
}

const handleLogout = async () => {
    try {
        // Eliminar la cookie userId manualmente
        document.cookie = "userId=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
        await logout();
        userData.value = null;
        navigateTo('/login');
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
    }
}

onMounted(() => {
    loadUserData();
})
</script>

<style scoped>
.profile-container {
    min-height: 100vh;
    padding: 2rem 1rem;
    background-color: #F2E9E4;
    display: flex;
    align-items: center;
}

.profile-card {
    border-radius: 20px;
    overflow: hidden;
    background-color: white;
    box-shadow: 0 10px 30px rgba(34, 34, 59, 0.1);
    transition: transform 0.3s ease;
}

.profile-header {
    padding: 2.5rem 1.5rem 2rem;
    background: linear-gradient(135deg, #22223B 0%, #4A4E69 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.user-avatar {
    border: 5px solid #EAE0D5;
    background-color: #4A4E69;
    margin-bottom: 1rem;
}

.user-details p {
    font-size: 1.1rem;
    color: #22223B;
    margin-bottom: 1rem;
}

.actions-container {
    background-color: #F2E9E4;
    padding: 1.5rem;
    border-top: 1px solid rgba(201, 173, 167, 0.3);
}

.logout-btn {
    transition: all 0.3s ease;
    min-width: 180px;
    letter-spacing: 0.5px;
    color: white;
    box-shadow: 0 4px 8px rgba(154, 140, 152, 0.3);
}

.logout-btn:hover {
    background-color: #22223B !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(34, 34, 59, 0.2);
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .profile-header {
        padding: 2rem 1rem 1.5rem;
    }

    .user-details p {
        font-size: 1rem;
    }

    .logout-btn {
        min-width: 100%;
    }
}
</style>