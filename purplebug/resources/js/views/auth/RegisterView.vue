<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-3xl font-bold text-center text-purple-600 mb-6">Register</h2>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <input v-model="form.full_name" type="text" placeholder="Full Name"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>
        <div>
          <input v-model="form.email" type="email" placeholder="Email"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>
        <div>
          <input v-model="form.password" type="password" placeholder="Password"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>
        <div>
          <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password"
                 class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" required />
        </div>

        <button type="submit"
                class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">
          Register
        </button>
      </form>

      <p class="text-center mt-4">
        Already have an account?
        <router-link to="/login" class="text-purple-600 hover:underline">Login here</router-link>
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
  full_name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  try {
    await authStore.register(form.value);
    alert('Registration successful!');
    router.push(authStore.isAdmin ? '/admin/dashboard' : '/');
  } catch (error) {
    alert(error.response?.data?.message || 'Registration failed');
  }
};
</script>
