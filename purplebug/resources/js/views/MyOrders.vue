<template>
  <div class="max-w-5xl mx-auto">
    <h2 class="text-3xl font-semibold mb-8">My Orders</h2>

    <div v-if="orders.length === 0" class="text-center py-20 text-gray-500">
      You have no orders yet.
    </div>

    <div v-for="order in orders" :key="order.id" class="bg-white rounded-2xl shadow mb-6 overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 flex justify-between items-center border-b">
        <div>
          <span class="font-medium">Order #{{ order.id }}</span>
          <span class="ml-4 text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</span>
        </div>
        <span :class="getStatusClass(order.status)" class="px-4 py-1 rounded-full text-sm font-medium">
          {{ order.status }}
        </span>
      </div>

      <div class="p-6">
        <div v-for="item in order.items" :key="item.id" class="flex justify-between py-3 border-b last:border-none">
          <div class="flex items-center gap-4">
            <img v-if="item.product.image" :src="'/storage/' + item.product.image" class="w-12 h-12 object-cover rounded">
            <div>
              <p class="font-medium">{{ item.product.name }}</p>
              <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
            </div>
          </div>
          <p class="font-medium">₱{{ (item.price * item.quantity).toFixed(2) }}</p>
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
        <div class="font-bold text-lg">
          Total: ₱{{ parseFloat(order.total_amount).toFixed(2) }}
        </div>

        <!-- Cancel Button - Only for Pending orders -->
        <button v-if="order.status === 'Pending'"
                @click="cancelOrder(order)"
                class="px-5 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
          <span>✕</span> Cancel Order
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);

const fetchOrders = async () => {
  const res = await axios.get('/api/orders');
  orders.value = res.data;
};

const getStatusClass = (status) => {
  if (status === 'Delivered') return 'bg-green-100 text-green-700';
  if (status === 'For Delivery') return 'bg-blue-100 text-blue-700';
  if (status === 'Canceled') return 'bg-red-100 text-red-700';
  return 'bg-yellow-100 text-yellow-700';
};

const cancelOrder = async (order) => {
  if (!confirm('Are you sure you want to cancel this order?')) return;

  try {
    await axios.put(`/api/orders/${order.id}/cancel`);
    alert('Order cancelled successfully');
    fetchOrders(); // Refresh list
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to cancel order');
  }
};

onMounted(fetchOrders);
</script>
