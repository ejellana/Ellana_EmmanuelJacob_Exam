<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-purple-600">PurpleBug</h1>

        <div class="flex items-center gap-6">
          <span class="text-gray-600">
            Hi, {{ authStore.isLoggedIn ? authStore.user?.full_name : 'Guest' }}!
          </span>

          <router-link v-if="!authStore.isLoggedIn" to="/login" class="px-5 py-2 text-purple-600 hover:bg-purple-50 rounded-xl font-medium">
            Login
          </router-link>

          <router-link v-if="!authStore.isLoggedIn" to="/register" class="bg-purple-600 text-white px-6 py-2 rounded-xl hover:bg-purple-700">
            Sign Up
          </router-link>

          <!-- Cart Button -->
          <button v-if="authStore.isLoggedIn" @click="showCartModal = true"
                  class="relative flex items-center gap-2 bg-white border border-purple-200 hover:border-purple-300 px-6 py-2.5 rounded-xl font-medium">
            🛒 Cart
            <span v-if="cartStore.totalItems > 0"
                  class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold">
              {{ cartStore.totalItems }}
            </span>
          </button>

          <button v-if="authStore.isLoggedIn" @click="logout" class="bg-red-600 text-white px-5 py-2 rounded-xl hover:bg-red-700">
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Products Grid -->
    <section class="max-w-7xl mx-auto px-6 py-12 flex-1">
      <h2 class="text-3xl font-semibold mb-8 text-center">All Products</h2>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-2xl shadow hover:shadow-xl overflow-hidden group">
          <div class="h-52 bg-gray-100 flex items-center justify-center overflow-hidden">
            <img v-if="product.image" :src="'/storage/' + product.image" class="w-full h-full object-cover group-hover:scale-105 transition">
          </div>
          <div class="p-5">
            <h3 class="font-semibold text-lg">{{ product.name }}</h3>
            <p class="text-2xl font-bold text-purple-600 mt-1">₱{{ parseFloat(product.price).toFixed(2) }}</p>
            <p class="text-sm text-gray-500">Stocks: {{ product.stocks }}</p>

            <button @click="addToCart(product)"
                    :disabled="product.stocks <= 0"
                    class="mt-4 w-full bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 disabled:bg-gray-400">
              {{ product.stocks > 0 ? 'Add to Cart' : 'Out of Stock' }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 mt-auto">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-white">PurpleBug &copy; 2025. All Rights Reserved.</p>
      </div>
    </footer>

    <!-- Cart Modal -->
    <div v-if="showCartModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center px-8 py-6 border-b">
          <h2 class="text-2xl font-bold">🛒 Your Cart</h2>
          <button @click="showCartModal = false" class="text-4xl text-gray-400 hover:text-gray-600">×</button>
        </div>

        <div class="flex-1 p-6 overflow-auto space-y-4">
          <div v-if="cartStore.items.length === 0" class="text-center py-16 text-gray-500">
            Your cart is empty
          </div>

          <div v-for="item in cartStore.items" :key="item.id" class="flex gap-4 bg-gray-50 p-4 rounded-2xl">
            <img v-if="item.image" :src="'/storage/' + item.image" class="w-20 h-20 object-cover rounded-xl">
            <div class="flex-1">
              <h4 class="font-semibold">{{ item.name }}</h4>
              <p class="text-purple-600 font-bold">₱{{ (item.price * item.quantity).toFixed(2) }}</p>

              <div class="flex items-center gap-4 mt-3">
                <button @click="decreaseQty(item)" class="w-8 h-8 border rounded-lg hover:bg-gray-100">-</button>
                <span class="font-medium w-6 text-center">{{ item.quantity }}</span>
                <button @click="increaseQty(item)" class="w-8 h-8 border rounded-lg hover:bg-gray-100">+</button>
                <button @click="removeItem(item.id)" class="ml-auto text-red-600 hover:text-red-700">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t bg-gray-50 rounded-b-3xl">
          <div class="flex justify-between text-xl font-bold mb-6">
            <span>Total</span>
            <span>₱{{ cartStore.totalAmount.toFixed(2) }}</span>
          </div>
          <button @click="checkout" class="w-full bg-purple-600 text-white py-4 rounded-2xl text-lg font-semibold hover:bg-purple-700">
            Place Order
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();

const products = ref([]);
const showCartModal = ref(false);
let refreshInterval = null;

const fetchProducts = async () => {
  const res = await axios.get('/api/products');
  products.value = res.data;
};

const addToCart = async (product) => {
  if (!authStore.isLoggedIn) {
    alert("Please login first to add items to cart!");
    router.push('/login');
    return;
  }

  const success = await cartStore.addToCart(product);
  if (success) {
    alert(`Added ${product.name} to cart`);
  }
};

const increaseQty = (item) => cartStore.updateQuantity(item.id, item.quantity + 1);
const decreaseQty = (item) => {
  if (item.quantity > 1) cartStore.updateQuantity(item.id, item.quantity - 1);
};
const removeItem = (id) => cartStore.removeFromCart(id);

const checkout = () => {
  if (confirm("Proceed to checkout?")) {
    alert("✅ Order placed successfully! (Orders module coming next)");
    cartStore.clearCart();
    showCartModal.value = false;
  }
};

const logout = () => {
  authStore.logout();
  router.push('/login');
};

onMounted(() => {
  fetchProducts();
  if (authStore.isLoggedIn) {
    cartStore.loadCart();
  }
  refreshInterval = setInterval(fetchProducts, 10000);
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>
