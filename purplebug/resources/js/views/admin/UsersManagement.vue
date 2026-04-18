<template>
  <div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-semibold text-purple-800">Users Management</h2>
        <p class="text-gray-500 text-sm">Manage administrative and customer access</p>
      </div>
      <button @click="openCreateModal"
              class="bg-[#7D3C98] text-white px-6 py-2.5 rounded-lg font-medium hover:bg-purple-800 transition-colors shadow-sm flex items-center gap-2">
        <span class="text-lg">+</span> Add New User
      </button>
    </div>

    <div class="bg-white rounded-xl border border-[#C8A2C8] shadow-sm overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-[#7D3C98] text-white">
          <tr>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Full Name</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Email</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Role</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="user in users" :key="user.id" class="hover:bg-purple-50/30 transition-colors">
            <td class="px-6 py-4">
              <span class="font-medium text-gray-700">{{ user.full_name }}</span>
            </td>
            <td class="px-6 py-4 text-gray-600 text-sm">
              {{ user.email }}
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-md tracking-wider"
                    :class="user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'">
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4">
              <button @click="toggleActive(user)"
                      :class="user.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                      class="px-3 py-1 rounded-md text-[10px] font-bold uppercase transition-colors">
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </button>
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-center items-center gap-4">
                <button @click="editUser(user)" class="hover:scale-125 transition-transform" title="Edit">
                  <span class="text-lg">✏️</span>
                </button>

                <button @click="toggleActive(user)" class="hover:scale-125 transition-transform" :title="user.is_active ? 'Deactivate' : 'Activate'">
                  <span class="text-lg">{{ user.is_active ? '🚫' : '✅' }}</span>
                </button>

                <button @click="deleteUser(user)" class="hover:scale-125 transition-transform text-red-500" title="Delete">
                  <span class="text-lg">🗑️</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-200">
        <div class="bg-[#7D3C98] p-4 px-8 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-white">{{ isEditing ? 'Edit User' : 'Create New User' }}</h3>
          <div class="flex gap-2">
            <button @click="saveUser" class="bg-white text-[#7D3C98] px-5 py-1.5 rounded-md font-bold text-xs hover:bg-gray-100 transition-all">SAVE</button>
            <button @click="closeModal" class="bg-[#7D3C98] text-white px-5 py-1.5 rounded-md font-bold text-xs hover:bg-purple-900 transition-all border border-purple-500/30">CANCEL</button>
          </div>
        </div>

        <form @submit.prevent="saveUser" class="p-8 space-y-4 bg-[#fdfcff]">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label>
              <input v-model="form.full_name" type="text" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
              <input v-model="form.email" type="email" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
            </div>

            <template v-if="!isEditing">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                <input v-model="form.password" type="password" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm Password</label>
                <input v-model="form.password_confirmation" type="password" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
              </div>
            </template>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const users = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentUserId = ref(null);
const form = ref({ full_name: '', email: '', password: '', password_confirmation: '' });

const fetchUsers = async () => {
  try {
    const res = await axios.get('/api/users');
    users.value = res.data;
  } catch (err) {
    console.error("Error fetching users");
  }
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
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to save user');
  }
};

const toggleActive = async (user) => {
  if (confirm(`Change status for ${user.full_name}?`)) {
    try {
      await axios.patch(`/api/users/${user.id}/toggle-active`);
      fetchUsers();
    } catch (err) {
      console.error("Status toggle failed");
    }
  }
};

const deleteUser = async (user) => {
  if (confirm(`Permanently delete user: ${user.full_name}?`)) {
    try {
      await axios.delete(`/api/users/${user.id}`);
      fetchUsers();
    } catch (err) {
      console.error("Delete failed");
    }
  }
};

const closeModal = () => {
  showModal.value = false;
};

fetchUsers();
</script>
