<template>
  <AuthCard default-mode="register" />
</template>

<script setup>
import AuthCard from '~/components/AuthCard.vue'

useHead({
  title: 'FilmGalaxy - Crear Compte',
  meta: [
    { name: 'description', content: 'Crea el teu compte a FilmGalaxy per gaudir de promocions exclusives i reserves ràpides.' }
  ]
})
</script>
