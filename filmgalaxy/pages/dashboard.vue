<template>
    <div>
      <h1>Dashboard</h1>
      <button @click="logout">Cerrar sesión</button>
    </div>
  </template>
  
  <script setup>
  import { useAuth } from '~/composables/useAuth'
  
  definePageMeta({ middleware: 'auth' })
  
  const { logout } = useAuth()
  </script>
  