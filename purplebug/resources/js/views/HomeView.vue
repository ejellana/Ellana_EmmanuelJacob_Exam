I have updated the colors in your code to use the specific hex code #7D3C98.

Since Tailwind's default purple-700 or purple-800 won't exactly match that custom hex, I've swapped those utility classes for inline styles or custom arbitrary values (like bg-[#7D3C98]) to ensure the brand alignment is perfect.

Code snippet
<template>
  <div class="min-h-screen bg-[#f8f7ff] flex flex-col">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
      <div class="max-w-[1440px] mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center">
          <img
            src="/PurpleBug-Logo.png"
            alt="PurpleBug Logo"
            class="h-9 w-auto object-contain">
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center border border-purple-100">
              <span class="text-[#7D3C98] text-sm">👤</span>
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-bold text-gray-700 leading-none mb-1">
                Hi, {{ authStore.isLoggedIn ? authStore.user?.full_name : 'Guest' }}!
              </span>
              <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Store Access</span>
            </div>
          </div>

          <button v-if="authStore.isLoggedIn" @click="showCartModal = true"
                  class="relative text-gray-400 hover:text-[#7D3C98] transition-colors p-2">
            <span class="text-xl">🛒</span>
            <span v-if="cartStore.totalItems > 0"
                  class="absolute top-0 right-0 bg-[#7D3C98] text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-md font-bold shadow-sm">
              {{ cartStore.totalItems }}
            </span>
          </button>
          <!-- Orders Button -->
          <button v-if="authStore.isLoggedIn" @click="showOrdersModal = true"
                  class="text-gray-400 hover:text-[#7D3C98] transition-colors p-2 text-xl">
            📋
          </button>

          <template v-if="!authStore.isLoggedIn">
            <router-link to="/login" class="text-[#7D3C98] px-4 py-2 rounded-lg hover:bg-purple-50 transition font-bold text-xs border border-purple-100 uppercase tracking-wide">
              Login
            </router-link>
            <router-link to="/register" class="bg-[#7D3C98] text-white px-5 py-2 rounded-lg hover:opacity-90 transition font-bold text-xs shadow-sm uppercase tracking-wide">
              Sign Up
            </router-link>
          </template>

          <button v-else @click="logout" class="text-gray-500 hover:text-red-600 px-4 py-2 rounded-lg transition text-xs font-bold uppercase border border-gray-200">
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-[1440px] mx-auto w-full px-6 py-10 flex-1">
      <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="relative w-full max-w-md">
          <input v-model="searchQuery" type="text" placeholder="Search product name..."
                 class="w-full border border-gray-200 rounded-lg py-2.5 px-6 pl-12 text-sm text-gray-700 placeholder-gray-400 focus:ring-1 focus:ring-[#7D3C98] outline-none transition-all shadow-sm bg-white">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <button @click="sortOrder = 'asc'"
                  :class="sortOrder === 'asc' ? 'bg-[#7D3C98] text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-gray-50 border-gray-200'"
                  class="flex-1 md:flex-none border px-4 py-2 rounded-lg text-xs font-bold transition-all uppercase tracking-wide">
            Price Ascending
          </button>
          <button @click="sortOrder = 'desc'"
                  :class="sortOrder === 'desc' ? 'bg-[#7D3C98] text-white shadow-sm' : 'bg-white text-gray-500 hover:bg-gray-50 border-gray-200'"
                  class="flex-1 md:flex-none border px-4 py-2 rounded-lg text-xs font-bold transition-all uppercase tracking-wide">
            Price Descending
          </button>
        </div>
      </div>

      <div v-if="paginatedProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="product in paginatedProducts" :key="product.id"
              class="flex flex-col group bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all">

          <div class="aspect-[4/3] bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden mb-4 border border-gray-100 relative">
            <img v-if="product.image"
                  :src="'/storage/' + product.image"
                  class="w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-110"
                  @error="(e) => { e.target.src = 'https://placehold.co/600x400?text=No+Image' }">
            <div v-else class="text-4xl opacity-20">🖼️</div>
          </div>

          <div class="flex-1">
            <h3 class="text-gray-500 font-bold text-[10px] uppercase tracking-widest mb-1">Premium Product</h3>
            <h3 class="text-gray-800 font-semibold text-lg mb-2">{{ product.name }}</h3>
            <div class="flex items-end justify-between">
              <p class="text-2xl font-bold text-[#7D3C98]">
                ₱{{ parseFloat(product.price).toLocaleString() }}
              </p>
              <p class="text-[10px] text-gray-400 font-bold uppercase">Stock: {{ product.stocks }}</p>
            </div>
          </div>

          <button @click="openQuickView(product)"
                  :disabled="product.stocks <= 0"
                  class="mt-5 w-full bg-[#7D3C98] text-white py-2.5 rounded-lg hover:opacity-90 disabled:bg-gray-100 disabled:text-gray-400 transition-all font-bold text-xs uppercase tracking-widest active:scale-95 shadow-sm">
            {{ product.stocks > 0 ? 'Add to Cart' : 'Sold Out' }}
          </button>
        </div>
      </div>

      <div v-if="products.length > 0" class="flex justify-center items-center gap-2 mt-12">
        <button @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-3 py-2 text-xs font-bold transition-colors border border-gray-200 rounded-lg hover:bg-white disabled:opacity-30">
            ← PREV
        </button>
        <button v-for="page in totalPages" :key="page"
                @click="changePage(page)"
                :class="currentPage === page ? 'bg-[#7D3C98] text-white border-[#7D3C98]' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'"
                class="w-9 h-9 flex items-center justify-center rounded-lg font-bold text-xs transition-all border shadow-sm">
            {{ page }}
        </button>
        <button @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages || totalPages === 0"
                class="px-3 py-2 text-xs font-bold transition-colors border border-gray-200 rounded-lg hover:bg-white disabled:opacity-30">
            NEXT →
        </button>
      </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-10">
      <div class="max-w-7xl mx-auto px-6 flex flex-col items-center">
        <div class="flex items-center">
          <img
            src="/PurpleBug-Logo.png"
            alt="PurpleBug Logo"
            class="h-8 w-auto object-contain">
        </div>
        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">© 2026 PurpleBug Administrative Suite</p>
      </div>
    </footer>

    <div v-if="showQuickView" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-xl w-full max-w-xl shadow-2xl overflow-hidden border border-gray-200">
        <div class="flex flex-col md:flex-row">
          <div class="w-full md:w-1/2 bg-gray-50 p-8 flex items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
            <img v-if="selectedProduct.image" :src="'/storage/' + selectedProduct.image"
                 class="max-w-full max-h-48 object-contain drop-shadow-lg">
            <div v-else class="text-6xl opacity-20">🖼️</div>
          </div>

          <div class="w-full md:w-1/2 p-8 flex flex-col justify-center">
            <div class="mb-6">
              <h3 class="text-xs font-bold text-[#7D3C98] uppercase tracking-widest mb-1">Add to Cart</h3>
              <h2 class="text-2xl font-bold text-gray-800 leading-tight mb-2">{{ selectedProduct.name }}</h2>
              <p class="text-3xl font-black text-gray-900">₱{{ parseFloat(selectedProduct.price).toLocaleString() }}</p>
            </div>

            <div class="space-y-6">
              <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-2">Quantity</label>
                <div class="flex items-center w-32 border border-gray-200 rounded-lg bg-white overflow-hidden shadow-sm">
                  <button @click="quickQty > 1 ? quickQty-- : null" class="flex-1 py-2 hover:bg-gray-50 transition-colors border-r border-gray-100 font-bold">-</button>
                  <span class="flex-1 text-center font-bold text-sm">{{ quickQty }}</span>
                  <button @click="quickQty < selectedProduct.stocks ? quickQty++ : null" class="flex-1 py-2 hover:bg-gray-50 transition-colors border-l border-gray-100 font-bold">+</button>
                </div>
              </div>

              <div class="flex flex-col gap-2">
                <button @click="confirmAddToCart"
                        class="w-full bg-[#7D3C98] text-white py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:opacity-90 transition-all shadow-md active:scale-95">
                  Confirm Add
                </button>
                <button @click="showQuickView = false"
                        class="w-full bg-white text-gray-400 py-2 rounded-lg font-bold text-[10px] uppercase tracking-widest hover:text-gray-600 transition-all">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Modal (updated) -->
    <div v-if="showCartModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl w-full max-w-2xl max-h-[85vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200">
        <div class="p-5 flex justify-between items-center bg-white border-b border-gray-100">
          <div class="flex items-center gap-2">
            <span class="text-xl">🛒</span>
            <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Shopping Cart</h2>
          </div>
          <button @click="showCartModal = false" class="text-2xl text-gray-300 hover:text-gray-600 transition-colors">&times;</button>
        </div>

        <div class="flex-1 p-6 overflow-auto space-y-4">
          <div v-if="cartStore.items.length === 0" class="text-center py-10 text-gray-400 italic">Cart is empty</div>
          <div v-for="item in cartStore.items" :key="item.id" class="flex gap-4 items-center bg-[#fdfcff] p-4 rounded-lg border border-gray-100">
            <div class="w-20 h-16 bg-white rounded border border-gray-100 flex-shrink-0 p-1">
              <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-contain">
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-bold text-gray-800">{{ item.name }}</h4>
              <p class="text-[#7D3C98] font-bold text-sm">₱{{ (item.price * item.quantity).toLocaleString() }}</p>
            </div>
            <div class="flex items-center border border-gray-200 rounded overflow-hidden h-8">
              <button @click="decreaseQty(item)" class="px-2 hover:bg-gray-50 transition-colors">-</button>
              <span class="px-3 text-xs font-bold">{{ item.quantity }}</span>
              <button @click="increaseQty(item)" class="px-2 hover:bg-gray-50 transition-colors">+</button>
            </div>
            <button @click="removeItem(item.id)" class="text-red-400 hover:text-red-600 transition-colors">🗑️</button>
          </div>
        </div>

        <div class="p-6 bg-[#7D3C98] text-white flex justify-between items-center">
          <div>
            <p class="text-[10px] font-bold uppercase opacity-70">Total Amount</p>
            <p class="text-3xl font-bold">₱{{ cartStore.totalAmount.toLocaleString() }}</p>
          </div>
          <button @click="checkout" :disabled="cartStore.items.length === 0"
                  class="bg-white text-[#7D3C98] px-8 py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-gray-100 disabled:opacity-50">
            Place Order
          </button>
        </div>
      </div>
    </div>

    <!-- My Orders Modal -->
    <div v-if="showOrdersModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center">
          <h2 class="text-2xl font-bold">📋 My Orders</h2>
          <button @click="showOrdersModal = false" class="text-3xl text-gray-400 hover:text-gray-600">×</button>
        </div>
        <div class="flex-1 p-6 overflow-auto">
          <MyOrders />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useRouter } from 'vue-router';
import MyOrders from './MyOrders.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();

// State
const products = ref([]);
const showCartModal = ref(false);
const showOrdersModal = ref(false);
const showQuickView = ref(false);
const selectedProduct = ref(null);
const quickQty = ref(1);

const searchQuery = ref('');
const sortOrder = ref('latest');
const currentPage = ref(1);
const itemsPerPage = 8;
let refreshInterval = null;

// Methods
const fetchProducts = async () => {
  try {
    const res = await axios.get('/api/products');
    products.value = res.data;
  } catch (error) {
    console.error("Error fetching products:", error);
  }
};

const logout = () => {
  authStore.logout();
  router.push('/login');
};

const openQuickView = (product) => {
  if (!authStore.isLoggedIn) {
    alert("Please login first to add items to cart!");
    router.push('/login');
    return;
  }
  selectedProduct.value = product;
  quickQty.value = 1;
  showQuickView.value = true;
};

const confirmAddToCart = async () => {
  for(let i = 0; i < quickQty.value; i++) {
    await cartStore.addToCart(selectedProduct.value);
  }
  showQuickView.value = false;
  // Optional: Open cart modal automatically to show it was added
  // showCartModal.value = true;
};

// Cart Controls
const increaseQty = (item) => cartStore.updateQuantity(item.id, item.quantity + 1);
const decreaseQty = (item) => {
  if (item.quantity > 1) cartStore.updateQuantity(item.id, item.quantity - 1);
};
const removeItem = (id) => cartStore.removeFromCart(id);

const checkout = async () => {
  if (cartStore.items.length === 0) return;

  if (confirm("Proceed to checkout?")) {
    try {
      const res = await axios.post('/api/orders/checkout');
      alert(res.data.message || "✅ Order placed successfully!");
      cartStore.clearCart();
      showCartModal.value = false;
      showOrdersModal.value = true; // Open orders after checkout
    } catch (error) {
      alert(error.response?.data?.message || "Checkout failed");
    }
  }
};

// Filter, Sort, Pagination
const filteredAndSortedProducts = computed(() => {
  let result = [...products.value];
  if (searchQuery.value) {
    result = result.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
  }
  if (sortOrder.value === 'asc') {
    result.sort((a, b) => a.price - b.price);
  } else if (sortOrder.value === 'desc') {
    result.sort((a, b) => b.price - a.price);
  } else {
    result.sort((a, b) => b.id - a.id);
  }
  return result;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredAndSortedProducts.value.length / itemsPerPage)));
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredAndSortedProducts.value.slice(start, start + itemsPerPage);
});

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

watch(searchQuery, () => { currentPage.value = 1; });

onMounted(() => {
  fetchProducts();
  if (authStore.isLoggedIn) {
    cartStore.loadCart();
  }
  refreshInterval = setInterval(fetchProducts, 30000);
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>
