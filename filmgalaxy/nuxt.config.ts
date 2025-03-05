// https://nuxt.com/docs/api/configuration/nuxt-config
import { defineNuxtConfig } from 'nuxt/config';

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  plugins: [
    '~/plugins/vuetify.js'
  ],
  css: [
    'vuetify/styles'
  ],

  modules: [
    '@pinia/nuxt',
  ]
})
