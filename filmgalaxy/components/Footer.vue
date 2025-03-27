<template>
  <v-footer class="bg-primary" elevation="4">
    <v-container>
      <v-row class="py-8" align="center" justify="space-between">
        <!-- Columna Esquerra: FilmGalaxy -->
        <v-col cols="12" md="4" class="text-left">
          <h3 class="text-h5 text-light mb-4">FilmGalaxy</h3>
          <p class="text-accent mb-4">
            Compra les teves entrades a FilmGalaxy, i <br> gaudeix de la millor experiència de cinema.
          </p>
          <div class="d-flex gap-4">
            <v-btn icon="mdi-facebook" variant="text" color="gold"
              href="https://github.com/inspedralbes/tr3-cinema-24-25-Purvish69/tree/dev"></v-btn>
            <v-btn icon="mdi-twitter" variant="text" color="gold" href="https://www.imdb.com/?ref_=nv_home"></v-btn>
            <v-btn icon="mdi-instagram" variant="text" color="gold"></v-btn>
          </div>
        </v-col>

        <!-- Columna Central: Preus -->
        <v-col cols="12" md="4" class="text-center">
          <h4 class="text-h6 text-light mb-4">Preus</h4>
          <v-list bg-color="transparent" class="pa-0">
            <v-list-item class="px-0" color="accent">
              <span class="text-light">Dia d'espectador: Normal 4€, VIP 6€</span>
            </v-list-item>
            <v-list-item class="px-0" color="accent">
              <span class="text-light">Dia normal: Normal 6€, VIP 8€</span>
            </v-list-item>
          </v-list>
        </v-col>

        <!-- Columna Dreta: Contacte -->
        <v-col cols="12" md="4" class="text-right">
          <h4 class="text-h6 text-light mb-4">Contacte</h4>
          <v-list bg-color="transparent" class="pa-0">
            <v-list-item v-for="(contact, i) in contactInfo" :key="i" :title="contact" class="px-0"
              color="accent"></v-list-item>
          </v-list>
        </v-col>
      </v-row>

      <v-divider class="border-opacity-25"></v-divider>
    </v-container>
  </v-footer>
</template>

<script setup>
const contactInfo = [
  '📞 +34 900 123 456',
  '📧 info@cinemax.com',
  '📍 Carrer Avenida Sempreviva, 69, Barcelona',
  '🕒 Dl - Ds: 16:00 - 18:00 - 20:00'
];
</script>

<style>
.v-footer .v-list-item {
  min-height: 32px;
}
</style>
