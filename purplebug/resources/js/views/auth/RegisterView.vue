<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <div class="flex justify-center mb-6">
        <img src="/PurpleBug-Logo.png" alt="PurpleBug Logo" class="h-12 w-auto object-contain">
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <input v-model="form.full_name" type="text" placeholder="Full Name"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98]" required />
        </div>
        <div>
          <input v-model="form.email" type="email" placeholder="Email"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98]" required />
        </div>
        <div>
          <input v-model="form.password" type="password" placeholder="Password"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98]" required />
        </div>
        <div>
          <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password"
                 class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#7D3C98]" required />
        </div>

        <button type="submit"
                :disabled="isLoading"
                class="w-full bg-[#7D3C98] text-white py-3 rounded-lg hover:opacity-90 transition font-bold disabled:opacity-70">
          {{ isLoading ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <p class="text-center mt-4 text-sm">
        Already have an account?
        <router-link to="/login" class="text-[#7D3C98] font-bold hover:underline">Login here</router-link>
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

const isLoading = ref(false);

const handleRegister = async () => {
  isLoading.value = true;

  try {
    await authStore.register(form.value);

    alert('Registration successful!');
    router.push('/login');

  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || 'Registration failed. Please try again.');
  } finally {
    isLoading.value = false;
  }
};
</script>
