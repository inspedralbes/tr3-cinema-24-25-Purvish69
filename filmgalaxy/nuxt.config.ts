// filepath: c:\2n DAW\tr3-cinema-24-25-Purvish69\filmgalaxy\nuxt.config.ts
import { defineNuxtConfig } from 'nuxt/config';
// import tailwindcss from 'tailwindcss/vite';

export default defineNuxtConfig({
  compatibilityDate: '2025-03-13',
  devtools: { enabled: true },

  app:{
    head: {
      title: 'FilmGalaxy',
  }
  },
  ssr: false,
  plugins: [
    '~/plugins/vuetify.js'
  ],
  css: [
    'vuetify/styles', // Importar estilos de Vuetify
    '@mdi/font/css/materialdesignicons.css',
    '@/assets/css/tailwind.css',
    '~/assets/css/tailwind.css',
  ],

  build: {
    transpile: ['vuetify']
  },

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  modules: [
    '@pinia/nuxt',
    '@nuxtjs/tailwindcss'
  ],

  // Enable pages directory for file-based routing
  experimental: {
    payloadExtraction: false
  }
});