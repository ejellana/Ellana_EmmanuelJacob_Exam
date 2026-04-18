<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-semibold">Products Management</h2>
      <button @click="openCreateModal"
              class="bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 flex items-center gap-2">
        <span>+</span> Add New Product
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left">Image</th>
            <th class="px-6 py-4 text-left">Product Name</th>
            <th class="px-6 py-4 text-left">Price</th>
            <th class="px-6 py-4 text-left">Stocks</th>
            <th class="px-6 py-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <img v-if="product.image" :src="'/storage/' + product.image"
                   class="w-12 h-12 object-cover rounded-lg border">
              <span v-else class="text-gray-400 text-sm">No image</span>
            </td>
            <td class="px-6 py-4 font-medium">{{ product.name }}</td>
            <td class="px-6 py-4">₱{{ parseFloat(product.price).toFixed(2) }}</td>
            <td class="px-6 py-4">
              <span :class="product.stocks > 0 ? 'text-green-600 font-medium' : 'text-red-600'">
                {{ product.stocks }}
              </span>
            </td>
            <td class="px-6 py-4 text-center space-x-4">
              <button @click="editProduct(product)" class="text-blue-600 hover:underline">Edit</button>
              <button @click="deleteProduct(product)" class="text-red-600 hover:underline">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-8">
        <h3 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h3>

        <form @submit.prevent="saveProduct" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-1">Product Name</label>
            <input v-model="form.name" type="text" required class="w-full px-4 py-3 border rounded-lg focus:border-purple-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Price (₱)</label>
            <input v-model="form.price" type="number" step="0.01" required class="w-full px-4 py-3 border rounded-lg focus:border-purple-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Stocks</label>
            <input v-model="form.stocks" type="number" required class="w-full px-4 py-3 border rounded-lg focus:border-purple-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full px-4 py-3 border rounded-lg focus:border-purple-500"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Product Image</label>
            <input type="file" @change="handleImage" accept="image/*" class="w-full px-4 py-3 border rounded-lg">
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" @click="closeModal" class="flex-1 py-3 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
              {{ isEditing ? 'Update Product' : 'Create Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue';
import axios from 'axios';

const products = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const form = ref({ name: '', price: '', stocks: '', description: '' });
const imageFile = ref(null);

const emit = defineEmits(['refresh']);

const fetchProducts = async () => {
  const res = await axios.get('/api/products');
  products.value = res.data;
};

const openCreateModal = () => {
  form.value = { name: '', price: '', stocks: '', description: '' };
  imageFile.value = null;
  isEditing.value = false;
  showModal.value = true;
};

const editProduct = (product) => {
  currentId.value = product.id;
  form.value = {
    name: product.name,
    price: product.price,
    stocks: product.stocks,
    description: product.description || ''
  };
  isEditing.value = true;
  showModal.value = true;
};

const handleImage = (e) => {
  imageFile.value = e.target.files[0];
};

const saveProduct = async () => {
  const data = new FormData();
  data.append('name', form.value.name);
  data.append('price', form.value.price);
  data.append('stocks', form.value.stocks);
  data.append('description', form.value.description || '');

  if (imageFile.value) data.append('image', imageFile.value);

  try {
    if (isEditing.value) {
      await axios.post(`/api/products/${currentId.value}?_method=PUT`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await axios.post('/api/products', data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    closeModal();
    fetchProducts();
    alert(isEditing.value ? 'Product updated successfully!' : 'Product created successfully!');
  } catch (error) {
    alert(error.response?.data?.message || 'Failed');
  }
};

const deleteProduct = async (product) => {
  if (confirm('Delete this product?')) {
    await axios.delete(`/api/products/${product.id}`);
    fetchProducts();
  }
};

const closeModal = () => {
  showModal.value = false;
};

defineExpose({ fetchProducts });

fetchProducts();
</script>
