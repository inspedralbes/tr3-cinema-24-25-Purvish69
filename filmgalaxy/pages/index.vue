<template>
  <v-app>
    <Navbar />
    <v-main>
      <v-container class="py-5">
        <h1>Bienvenido a Cinema App</h1>
        <p>Aquí puedes comprar tus entradas de cine y descubrir las últimas películas.</p>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import Navbar from '~/components/Navbar.vue'
</script>

<style scoped>
/* Tus estilos aquí */
</style>