<template>
  <AuthCard default-mode="login" />
</template>

<script setup>
import AuthCard from '~/components/AuthCard.vue'

useHead({
  title: 'FilmGalaxy - Iniciar Sessió',
  meta: [
    { name: 'description', content: 'Inicia sessió a FilmGalaxy per reservar entrades de cinema i accedir a les teves butaques VIP.' }
  ]
})
</script>