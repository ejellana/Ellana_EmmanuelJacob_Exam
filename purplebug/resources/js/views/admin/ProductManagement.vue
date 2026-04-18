<template>
  <div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-semibold text-purple-800">Products Management</h2>
        <p class="text-gray-500 text-sm">Inventory and catalog control</p>
      </div>
      <button @click="openCreateModal"
              class="bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-purple-800 transition-colors shadow-sm flex items-center gap-2">
        <span class="text-lg">+</span> Add New Product
      </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-purple-600 text-white">
          <tr>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Product Name</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Price</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-center">Stocks</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="product in products" :key="product.id" class="hover:bg-purple-50/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                  <img v-if="product.image" :src="'/storage/' + product.image" class="w-full h-full object-cover">
                  <span v-else class="text-gray-300">🖼️</span>
                </div>
                <span class="font-medium text-gray-700">{{ product.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-900 font-semibold">
              ₱{{ parseFloat(product.price).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="product.stocks > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                    class="px-3 py-1 rounded-md text-xs font-bold">
                {{ product.stocks }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-center gap-4">
                <button @click="editProduct(product)" class="hover:scale-125 transition-transform" title="Edit">
                  <span class="text-lg">✏️</span>
                </button>
                <button @click="deleteProduct(product)" class="hover:scale-125 transition-transform" title="Delete">
                  <span class="text-lg">🗑️</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden border border-gray-200">
        <div class="bg-purple-700 p-4 px-8 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-white">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h3>
          <div class="flex gap-2">
            <button @click="saveProduct" class="bg-white text-purple-700 px-5 py-1.5 rounded-md font-bold text-xs hover:bg-gray-100 transition-all">SAVE</button>
            <button @click="closeModal" class="bg-purple-800 text-white px-5 py-1.5 rounded-md font-bold text-xs hover:bg-purple-900 transition-all border border-purple-500/30">CANCEL</button>
          </div>
        </div>

        <form @submit.prevent="saveProduct" class="p-8 flex flex-col md:flex-row gap-8 bg-[#fdfcff]">
          <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Product Image</label>
            <div class="aspect-square bg-white rounded-lg border border-gray-200 flex flex-col items-center justify-center relative overflow-hidden transition-all hover:border-purple-300">
              <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
              <div v-else class="text-center p-4">
                <span class="text-4xl opacity-20 block mb-2">🖼️</span>
                <p class="text-[10px] text-gray-400 font-bold">UPLOAD IMAGE</p>
              </div>
              <input type="file" @change="handleImage" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            </div>
          </div>

          <div class="flex-1 space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product Name</label>
              <input v-model="form.name" type="text" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price (₱)</label>
                <input v-model="form.price" type="number" step="0.01" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stocks</label>
                <input v-model="form.stocks" type="number" required class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none">
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-1 focus:ring-purple-500 outline-none resize-none"></textarea>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const imagePreview = ref(null);
const imageFile = ref(null);
const form = ref({ name: '', price: '', stocks: '', description: '' });

const fetchProducts = async () => {
  const res = await axios.get('/api/products');
  products.value = res.data;
};

const openCreateModal = () => {
  form.value = { name: '', price: '', stocks: '', description: '' };
  imageFile.value = null;
  imagePreview.value = null;
  isEditing.value = false;
  showModal.value = true;
};

const editProduct = (product) => {
  currentId.value = product.id;
  // Load existing data
  form.value = {
    name: product.name,
    price: product.price,
    stocks: product.stocks,
    description: product.description || ''
  };
  // Show the existing image as preview, but keep imageFile null
  // unless a new file is picked
  imagePreview.value = product.image ? `/storage/${product.image}` : null;
  imageFile.value = null;
  isEditing.value = true;
  showModal.value = true;
};

const handleImage = (e) => {
  const file = e.target.files[0];
  if (file) {
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const saveProduct = async () => {
  const data = new FormData();
  data.append('name', form.value.name);
  data.append('price', form.value.price);
  data.append('stocks', form.value.stocks);
  data.append('description', form.value.description || '');

  // YOUR OLD LOGIC: Only append if a NEW file was selected
  if (imageFile.value) {
    data.append('image', imageFile.value);
  }

  try {
    const url = isEditing.value ? `/api/products/${currentId.value}?_method=PUT` : '/api/products';
    await axios.post(url, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    closeModal();
    fetchProducts();
  } catch (error) {
    alert(error.response?.data?.message || 'Error saving product');
  }
};

const deleteProduct = async (product) => {
  if (confirm(`Delete ${product.name}?`)) {
    await axios.delete(`/api/products/${product.id}`);
    fetchProducts();
  }
};

const closeModal = () => {
  showModal.value = false;
  imagePreview.value = null;
  imageFile.value = null;
};

onMounted(fetchProducts);
</script>

<style>
body {
  background-color: #f8f7ff !important;
}
</style>
