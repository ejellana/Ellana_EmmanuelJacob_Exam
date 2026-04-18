<template>
  <div class="min-h-screen bg-[#f8f7ff] flex">
    <div class="w-64 bg-white h-screen fixed left-0 top-0 flex flex-col border-r border-gray-200 z-50 shadow-sm">
      <div class="p-6">
        <div class="flex items-center gap-3">
          <span class="text-2xl">🐞</span>
          <h1 class="text-xl font-bold text-gray-800 tracking-tight">PurpleBug</h1>
        </div>
        <div class="h-[1px] bg-gray-100 mt-6 w-full"></div>
      </div>

      <nav class="flex-1 px-3 space-y-1">
        <button @click="switchPage('products')"
                class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 text-sm font-medium"
                :class="currentPage === 'products' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700'">
          <span class="text-xl">📦</span> Products Management
        </button>

        <button @click="switchPage('orders')"
                class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 text-sm font-medium"
                :class="currentPage === 'orders' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700'">
          <span class="text-xl">📋</span> Orders
        </button>

        <button @click="switchPage('users')"
                class="w-full text-left px-4 py-3 rounded-lg transition-all flex items-center gap-3 text-sm font-medium"
                :class="currentPage === 'users' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700'">
          <span class="text-xl">👥</span> Users Management
        </button>
      </nav>

      <div class="p-4 mt-auto border-t border-gray-100">
        <div class="bg-gray-50 rounded-xl p-3 flex items-center justify-between border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-lg">
              👤
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-bold text-gray-700">Hi Admin!</span>
              <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Administrator</span>
            </div>
          </div>
          <button @click="logout"
                  class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                  title="Logout">
            <span class="text-xl">🚪</span>
          </button>
        </div>
      </div>
    </div>

    <div class="flex-1 ml-64">
      <div class="p-8 max-w-[1400px] mx-auto">
        <UsersManagement v-if="currentPage === 'users'" />
        <ProductManagement v-if="currentPage === 'products'" />
        <div v-if="currentPage === 'orders'" class="bg-white p-10 rounded-xl border border-gray-200 text-center text-gray-400">
           Order Management Module Coming Soon
        </div>
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
const currentPage = ref('products');

const logout = () => {
  if(confirm("Are you sure you want to logout?")) {
    authStore.logout();
    router.push('/login');
  }
};

const switchPage = (page) => {
  currentPage.value = page;
};

onMounted(() => {
  if (!authStore.isAdmin) router.push('/');
});
</script>

<style scoped>
/* Ensure the sidebar text looks crisp */
h1, button, span {
  -webkit-font-smoothing: antialiased;
}
</style>
