<template>
  <v-app>
    <div class="min-h-screen custom-bg">
      <Navbar />

      <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">
          Compra de entradas para la película {{ id }}
        </h1>
        <!-- Agrega aquí el contenido y lógica para la compra de entradas -->
      </div>

      <Footer />
    </div>
  </v-app>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();
const id = route.params.id;
</script>

<style scoped>
.custom-bg {
  background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
}
</style>