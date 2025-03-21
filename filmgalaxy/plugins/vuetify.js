import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'vuetify/styles'

export default defineNuxtPlugin(nuxtApp => {
  const vuetify = createVuetify({
    components,
    directives,
    theme: {
      themes: {
        light: {
          dark: false,
          colors: {
            primary: '#22223B',
            secondary: '#4A4E69',
            accent: '#9A8C98',
            neutral: '#C9ADA7',
            light: '#F2E9E4',
            dark: '#1C1C1C',
            gold: '#C8A96E',
            cream: '#EAE0D5'
          }
        }
      }
    }
  })

  nuxtApp.vueApp.use(vuetify)
})