import { createRouter, createWebHashHistory } from "vue-router";
import Home from './pages/home/index.vue'
import Login from './pages/login/index.vue'
import { components } from "vuetify/dist/vuetify-labs.js";
// import Register from './pages/register/index.vue'

const routes = [
    {path: '/', component: Home},
    {path: '/', component: Login},
    // {path: '/', component: Register},

]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router