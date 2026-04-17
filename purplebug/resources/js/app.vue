<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue';
import { useInactivity } from './composables/useInactivity';
import axios from 'axios';
import { useAuthStore } from './stores/auth';

// Use inactivity auto logout
useInactivity();

const authStore = useAuthStore();

// Set Authorization header if token exists on page refresh
onMounted(() => {
  if (authStore.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;

    // Optional: Fetch latest user data from backend later
  }
});
</script>
