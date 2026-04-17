<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-3xl font-bold text-center text-purple-600 mb-6">Login</h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <input v-model="form.email" type="email" placeholder="Email"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>
        <div>
          <input v-model="form.password" type="password" placeholder="Password"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>

        <button type="submit"
                class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">
          Login
        </button>
      </form>

      <p class="text-center mt-4">
        Don't have an account?
        <router-link to="/register" class="text-purple-600 hover:underline">Register here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: ''
});

const handleLogin = async () => {
  try {
    await authStore.login(form.value);
    alert('Login successful!');

    // Role-based redirect
    if (authStore.isAdmin) {
      router.push('/admin/dashboard');
    } else {
      router.push('/');
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Invalid credentials');
  }
};
</script>
