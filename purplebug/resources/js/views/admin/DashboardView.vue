<template>
  <div class="min-h-screen bg-gray-100 flex">

    <!-- Left Sidebar -->
    <div class="w-72 bg-purple-700 text-white h-screen fixed left-0 top-0 flex flex-col shadow-xl">
      <div class="p-6 border-b border-purple-600">
        <h1 class="text-4xl font-bold tracking-tight">PurpleBug</h1>
      </div>

      <div class="flex-1 p-6 space-y-2">
        <button @click="switchPage('products')"
                class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg"
                :class="{ 'bg-purple-600': currentPage === 'products' }">
          📦 Products Management
        </button>

        <button @click="switchPage('users')"
                class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg"
                :class="{ 'bg-purple-600': currentPage === 'users' }">
          👥 Users Management
        </button>

        <button @click="switchPage('orders')"
                class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg opacity-75 cursor-not-allowed">
          📋 Orders
        </button>
      </div>

      <div class="p-6 border-t border-purple-600 mt-auto">
        <div class="mb-4">
          <p class="text-purple-200 text-sm">Hi,</p>
          <p class="font-semibold text-lg">{{ authStore.user?.full_name }}</p>
        </div>
        <button @click="logout"
                class="w-full bg-red-600 hover:bg-red-700 py-3 rounded-xl font-medium transition">
          Logout
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 ml-72 p-8">
      <div class="max-w-6xl mx-auto">
        <UsersManagement v-if="currentPage === 'users'" />
        <ProductManagement v-if="currentPage === 'products'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

import UsersManagement from './UsersManagement.vue';
import ProductManagement from './ProductManagement.vue';

const router = useRouter();
const authStore = useAuthStore();
const currentPage = ref('products');   // Default opens on Products

const logout = () => {
  authStore.logout();
  router.push('/login');
};

const switchPage = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  if (!authStore.isAdmin) router.push('/');
});
</script>
