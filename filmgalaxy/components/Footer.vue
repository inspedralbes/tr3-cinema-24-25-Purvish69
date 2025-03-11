<template>
    <v-footer class="bg-primary" elevation="4">
      <v-container>
        <v-row class="py-8">
          <v-col cols="12" md="4">
            <h3 class="text-h5 text-light mb-4">FilmGalaxy</h3>
            <p class="text-accent mb-4">Compra tus entradas en FilmGalaxy, y <br> disfruta de la mejor experiencia de cine.</p>
            <div class="d-flex gap-4">
              <v-btn icon="mdi-facebook" variant="text" color="gold"></v-btn>
              <v-btn icon="mdi-twitter" variant="text" color="gold"></v-btn>
              <v-btn icon="mdi-instagram" variant="text" color="gold"></v-btn>
            </div>
          </v-col>
  
          <v-col cols="12" md="4">
            <h4 class="text-h6 text-light mb-4">Enlaces Rápidos</h4>
            <v-list bg-color="transparent" class="pa-0">
              <v-list-item
                v-for="(link, i) in quickLinks"
                :key="i"
                :title="link"
                class="px-0"
                color="accent"
              ></v-list-item>
            </v-list>
          </v-col>
  
          <v-col cols="12" md="4">
            <h4 class="text-h6 text-light mb-4">Contacto</h4>
            <v-list bg-color="transparent" class="pa-0">
              <v-list-item
                v-for="(contact, i) in contactInfo"
                :key="i"
                :title="contact"
                class="px-0"
                color="accent"
              ></v-list-item>
            </v-list>
          </v-col>
        </v-row>
  
        <v-divider class="border-opacity-25"></v-divider>
  
        <v-row class="py-4">
          <v-col cols="12" class="text-center text-accent">
            © {{ new Date().getFullYear() }} CineMax. Todos los derechos reservados.
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </template>
  
  <script setup>
  const quickLinks = [
    'Cartelera',
    'Próximos Estrenos',
    'Promociones',
    'Sobre Nosotros',
    'Términos y Condiciones'
  ];
  
  const contactInfo = [
    '📞 +34 900 123 456',
    '📧 info@cinemax.com',
    '📍 Calle Avenida Siempreviva, 69, Barcelona',
    '🕒 Lun - Dom: 11:00 - 23:00'
  ];
  </script>
  
  <style>
  .v-footer .v-list-item {
    min-height: 32px;
  }
  </style>