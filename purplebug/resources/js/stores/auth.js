import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    lastActivity: Date.now(),
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user?.role === 'admin',
    isGuest: (state) => state.user?.role === 'guest',
  },

  actions: {
    setAuth(data) {
      this.token = data.token;
      this.user = data.user;
      this.lastActivity = Date.now();
      localStorage.setItem('token', this.token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    },

    async login(credentials) {
      const response = await axios.post('/api/login', credentials);
      this.setAuth(response.data);
      return response.data;
    },

    async register(userData) {
      const response = await axios.post('/api/register', userData);
      this.setAuth(response.data);
      return response.data;
    },

    logout() {
      localStorage.removeItem('token');
      this.token = null;
      this.user = null;
      delete axios.defaults.headers.common['Authorization'];
    },

    checkInactivity() {
      const inactiveTime = Date.now() - this.lastActivity;
      if (inactiveTime > 1800000) { // 30 minutes
        this.logout();
        alert('You have been logged out due to inactivity (30 minutes). Please login again.');
        window.location.href = '/login';
      }
    },

    updateActivity() {
      this.lastActivity = Date.now();
    }
  }
});
