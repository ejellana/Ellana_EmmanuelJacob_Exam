<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <div class="flex justify-center mb-6">
        <img src="/PurpleBug-Logo.png" alt="PurpleBug Logo" class="h-12 w-auto object-contain">
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <input v-model="form.email" type="email" placeholder="Email"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98] transition-colors" required />
        </div>
        <div>
          <input v-model="form.password" type="password" placeholder="Password"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98] transition-colors" required />
        </div>

        <button type="submit"
                class="w-full bg-[#7D3C98] text-white py-3 rounded-lg hover:opacity-90 transition font-bold">
          Login
        </button>
      </form>

      <p class="text-center mt-4 text-sm">
        Don't have an account?
        <router-link to="/register" class="text-[#7D3C98] font-bold hover:underline">Register here</router-link>
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
