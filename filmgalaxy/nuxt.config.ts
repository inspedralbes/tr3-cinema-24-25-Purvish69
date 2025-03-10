// nuxt.config.ts
import { defineNuxtConfig } from 'nuxt/config';

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  plugins: [
    '~/plugins/vuetify.js'
  ],
  css: [
    'vuetify/styles', // Importar estilos de Vuetify
    '@mdi/font/css/materialdesignicons.css' 
  ],

  build: {
    transpile: ['vuetify']
  },

  modules: [
    '@pinia/nuxt',
  ],
  
  // Enable pages directory for file-based routing
  experimental: {
    payloadExtraction: false
  }
});