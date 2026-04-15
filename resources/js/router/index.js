import { createRouter, createWebHashHistory } from 'vue-router';

import Login from '../Components/Auth/Login.vue';
import Register from '../Components/Auth/Register.vue';
import AuthDebug from '../Components/AuthDebug.vue';
import Home from '../Components/Home.vue';
import About from '../Components/About.vue';
import Contact from '../Components/Contact.vue';
import ProductDetails from '../Components/ProductDetails.vue';
import Cart from '../Components/Cart.vue';
import Checkout from '../Components/Checkout.vue';
import CategoryProducts from '../Components/CategoryProducts.vue';
import AdminLayout from '../Components/Admin/AdminLayout.vue';
import Dashboard from '../Components/Admin/Dashboard.vue';
import ProductManagement from '../Components/Admin/ProductManagement.vue';
import Orders from '../Components/Admin/Orders.vue';
import Categories from '../Components/Admin/Categories.vue';
import Messages from '../Components/Admin/Messages.vue';

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
    routes
});

export default router;
