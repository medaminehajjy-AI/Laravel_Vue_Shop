import { createRouter, createWebHashHistory } from 'vue-router';

import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import AuthDebug from '../components/AuthDebug.vue';
import Home from '../components/Home.vue';
import About from '../components/About.vue';
import Contact from '../components/Contact.vue';
import ProductDetails from '../components/ProductDetails.vue';
import Cart from '../components/Cart.vue';
import Checkout from '../components/Checkout.vue';
import CategoryProducts from '../components/CategoryProducts.vue';
import AdminLayout from '../components/Admin/AdminLayout.vue';
import Dashboard from '../components/Admin/Dashboard.vue';
import ProductManagement from '../components/Admin/ProductManagement.vue';
import Orders from '../components/Admin/Orders.vue';
import Categories from '../components/Admin/Categories.vue';
import Messages from '../components/Admin/Messages.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/debug', component: AuthDebug },
    { path: '/about', component: About },
    { path: '/contact', component: Contact },
    { path: '/product/:id', component: ProductDetails },
    { path: '/cart', component: Cart },
    { path: '/checkout', component: Checkout },
    { path: '/category/:id', component: CategoryProducts },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAdmin: true },
        children: [
            { path: 'dashboard', component: Dashboard },
            { path: 'products', component: ProductManagement },
            { path: 'orders', component: Orders },
            { path: 'categories', component: Categories },
            { path: 'messages', component: Messages },
            { path: '', redirect: '/admin/dashboard' }
        ]
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes  : routes
});

export default router;
