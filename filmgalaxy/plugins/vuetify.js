import { createVuetify } from 'vuetify'
import 'vuetify/styles'

const vuetify = createVuetify({
})

export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.vueApp.use(vuetify)
})
