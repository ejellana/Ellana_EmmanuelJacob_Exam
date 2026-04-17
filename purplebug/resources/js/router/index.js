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
    {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('../views/admin/DashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
    },
  ]
});

// Navigation Guard (Role-based + Inactivity later)
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login');
  }
  else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/'); // Guests cannot access admin
  }
  else if ((to.path === '/login' || to.path === '/register') && authStore.isLoggedIn) {
    next(authStore.isAdmin ? '/admin/dashboard' : '/');
  }
  else {
    next();
  }
});

export default router;
