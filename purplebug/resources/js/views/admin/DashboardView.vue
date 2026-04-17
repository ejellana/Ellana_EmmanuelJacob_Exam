<template>
  <div class="min-h-screen bg-gray-100 flex">

    <!-- Left Side Panel -->
    <div class="w-72 bg-purple-700 text-white h-screen fixed left-0 top-0 flex flex-col shadow-xl">

      <!-- Logo -->
      <div class="p-6 border-b border-purple-600">
        <h1 class="text-4xl font-bold tracking-tight">PurpleBug</h1>
      </div>

      <!-- Navigation Menu -->
      <div class="flex-1 p-6 space-y-2">
        <button
          @click="switchPage('users')"
          class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg"
          :class="{ 'bg-purple-600': currentPage === 'users' }">
          👥 Users Management
        </button>

        <button
          @click="switchPage('products')"
          class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg opacity-75 cursor-not-allowed">
          📦 Products Management
        </button>

        <button
          @click="switchPage('orders')"
          class="w-full text-left px-5 py-4 rounded-xl hover:bg-purple-600 transition flex items-center gap-3 text-lg opacity-75 cursor-not-allowed">
          📋 Orders
        </button>
      </div>

      <!-- Bottom User Info -->
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

    <!-- Main Content Area -->
    <div class="flex-1 ml-72 p-8">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-semibold mb-8">User Management</h2>

        <button @click="openCreateModal"
                class="mb-6 bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 flex items-center gap-2">
          <span>+</span> Add New User
        </button>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left">Full Name</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Role</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Joined</th>
                <th class="px-6 py-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ user.full_name }}</td>
                <td class="px-6 py-4">{{ user.email }}</td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'">
                    {{ user.role.toUpperCase() }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ new Date(user.created_at).toLocaleDateString() }}
                </td>
                <td class="px-6 py-4 text-center space-x-4">
                  <button @click="editUser(user)" class="text-blue-600 hover:underline">Edit</button>
                  <button @click="toggleActive(user)" class="text-yellow-600 hover:underline">
                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button @click="deleteUser(user)" class="text-red-600 hover:underline">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <h3 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit User' : 'Create New User' }}</h3>

        <form @submit.prevent="saveUser" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1">Full Name</label>
            <input v-model="form.full_name" type="text" required class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input v-model="form.email" type="email" required class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500">
          </div>

          <div v-if="!isEditing">
            <label class="block text-sm font-medium mb-1">Password</label>
            <input v-model="form.password" type="password" required class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500">
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500">
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" @click="closeModal" class="flex-1 py-3 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
              {{ isEditing ? 'Update User' : 'Create User' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();

const users = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentUserId = ref(null);
const currentPage = ref('users');

const form = ref({
  full_name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const fetchUsers = async () => {
  const res = await axios.get('/api/users');
  users.value = res.data;
};

const openCreateModal = () => {
  form.value = { full_name: '', email: '', password: '', password_confirmation: '' };
  isEditing.value = false;
  showModal.value = true;
};

const editUser = (user) => {
  currentUserId.value = user.id;
  form.value = { full_name: user.full_name, email: user.email, password: '', password_confirmation: '' };
  isEditing.value = true;
  showModal.value = true;
};

const saveUser = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/api/users/${currentUserId.value}`, {
        full_name: form.value.full_name,
        email: form.value.email
      });
    } else {
      await axios.post('/api/users', form.value);
    }
    closeModal();
    fetchUsers();
    alert(isEditing.value ? 'User updated!' : 'User created successfully!');
  } catch (error) {
    alert(error.response?.data?.message || 'Failed');
  }
};

const closeModal = () => {
  showModal.value = false;
};

const toggleActive = async (user) => {
  if (confirm(`Are you sure?`)) {
    await axios.patch(`/api/users/${user.id}/toggle-active`);
    fetchUsers();
  }
};

const deleteUser = async (user) => {
  if (confirm('Delete permanently?')) {
    await axios.delete(`/api/users/${user.id}`);
    fetchUsers();
  }
};

const logout = () => {
  authStore.logout();
  router.push('/login');
};

const switchPage = (page) => {
  if (page === 'users') {
    currentPage.value = 'users';
  } else {
    alert("This module is coming soon!");
  }
};

onMounted(() => {
  if (!authStore.isAdmin) router.push('/');
  fetchUsers();
});
</script>
