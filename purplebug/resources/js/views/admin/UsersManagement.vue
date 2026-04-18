<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-semibold">Users Management</h2>
      <button @click="openCreateModal"
              class="bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 flex items-center gap-2">
        <span>+</span> Add New User
      </button>
    </div>

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

    <!-- User Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <h3 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit User' : 'Create New User' }}</h3>

        <form @submit.prevent="saveUser" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1">Full Name</label>
            <input v-model="form.full_name" type="text" required class="w-full px-4 py-3 border rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input v-model="form.email" type="email" required class="w-full px-4 py-3 border rounded-lg">
          </div>

          <div v-if="!isEditing">
            <label class="block text-sm font-medium mb-1">Password</label>
            <input v-model="form.password" type="password" required class="w-full px-4 py-3 border rounded-lg">
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required class="w-full px-4 py-3 border rounded-lg">
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
import { ref } from 'vue';
import axios from 'axios';

const users = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentUserId = ref(null);
const form = ref({ full_name: '', email: '', password: '', password_confirmation: '' });

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

const closeModal = () => {
  showModal.value = false;
};

fetchUsers();
</script>
