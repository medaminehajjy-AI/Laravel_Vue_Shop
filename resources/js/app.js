import './bootstrap';

import { createApp } from 'vue';
import App from './Components/App.vue';
import router from './router';
import { useAuth } from './composables/useAuth';

const app = createApp(App);
app.use(router);

const { initAuth, fetchUser } = useAuth();

initAuth().then(() => {
    app.mount('#app');
});
