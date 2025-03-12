import { createApp } from "vue";
import App from "./app.vue";
import router from "./router/router.js";
import { createPinia } from "pinia";
import './assets/tailwind.css'; // Importar TailwindCSS

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

app.mount('#app');