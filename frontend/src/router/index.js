import { createRouter, createWebHistory } from 'vue-router';
import { authService } from '../services/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/Register.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/blogs',
        name: 'Blogs',
        component: () => import('../views/Blogs.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/',
        redirect: '/blogs'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = authService.isAuthenticated();
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if ((to.name === 'Login' || to.name === 'Register') && isAuthenticated) {
        next('/blogs');
    } else {
        next();
    }
});

export default router;