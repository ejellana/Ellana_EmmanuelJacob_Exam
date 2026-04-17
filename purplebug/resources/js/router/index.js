import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/auth/RegisterView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/auth/LoginView.vue')
    },
    // Protected routes will be added later
  ]
});

// Navigation Guard (Role-based + Inactivity later)
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && authStore.isLoggedIn) {
    next(authStore.isAdmin ? '/admin/dashboard' : '/');
  } else {
    next();
  }
});

export default router;
