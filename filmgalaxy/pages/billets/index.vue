<template>
  <div class="min-h-screen bg-[#0c0e1a] flex items-center justify-center text-white p-6">
    <div class="text-center">
      <div class="w-12 h-12 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-gray-300 font-medium">Cargando cartelera de asientos...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePeliculas } from '~/composables/communicationManagerPelicula'

const router = useRouter()
const { getSesiones } = usePeliculas()

onMounted(async () => {
  try {
    const data = await getSesiones()
    const sessions = data?.sessions || []
    if (sessions.length > 0) {
      router.replace(`/billets/${sessions[0].id}`)
    } else {
      router.replace('/movieDetails/moviSession')
    }
  } catch (e) {
    router.replace('/movieDetails/moviSession')
  }
})
</script>
