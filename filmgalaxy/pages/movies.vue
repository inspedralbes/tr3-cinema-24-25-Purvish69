<template>
    <div>
      <Navbar />
      <nav>
        <nuxt-link to="/">Home</nuxt-link>
        <nuxt-link to="/movies">Movies</nuxt-link>
        <!-- Otros enlaces -->
      </nav>
      <h1>Movies Page</h1>
      <p>Welcome to the movies page!</p>
    </div>
  </template>
  
  <script>
  import Navbar from '~/components/Navbar.vue'
  
  export default {
    name: 'MoviesPage',
    components: {
      Navbar
    }
  }
  </script>
  
  <style scoped>
 
 
  </style>