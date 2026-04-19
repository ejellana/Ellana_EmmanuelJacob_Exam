<template>
  <div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-semibold text-purple-800">Orders Management</h2>
        <p class="text-gray-500 text-sm">View and manage all customer orders</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-[#C8A2C8] shadow-sm overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-[#7D3C98] text-white">
          <tr>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Order ID</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Customer Name</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Products</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Total</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-purple-50/30 transition-colors">
            <td class="px-6 py-4 font-mono text-sm">#{{ order.id }}</td>
            <td class="px-6 py-4">
              <span class="font-medium text-gray-700">{{ order.user.full_name }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <span class="text-sm">{{ order.items[0]?.product?.name }}</span>
                <button v-if="order.items.length > 1"
                        @click="showProductsModal(order)"
                        class="text-[#7D3C98] hover:underline text-xs font-medium">
                  +{{ order.items.length - 1 }} more
                </button>
              </div>
            </td>
            <td class="px-6 py-4 font-semibold text-[#7D3C98]">
              ₱{{ parseFloat(order.total_amount).toLocaleString() }}
            </td>
            <td class="px-6 py-4">
              <span :class="getStatusClass(order.status)"
                    class="px-4 py-1.5 text-xs font-bold uppercase rounded-full">
                {{ order.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-center items-center gap-5">
                <button @click="viewOrder(order)" class="hover:scale-125 transition-transform text-lg" title="View Order">
                  👁️
                </button>
                <button @click="deleteOrder(order)" class="hover:scale-125 transition-transform text-red-500 text-lg" title="Delete Order">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- View Order Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden border border-gray-200">
        <div class="bg-[#7D3C98] px-8 py-4 flex justify-between items-center">
        <h3 class="text-white font-semibold">Order #{{ selectedOrder?.id }}</h3>
        <button @click="closeModal" class="text-white text-2xl hover:text-gray-200">×</button>
        </div>

        <div class="p-8 space-y-6">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer</label>
            <p class="font-medium text-gray-800">{{ selectedOrder?.user?.full_name }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Products Ordered</label>
            <div v-for="item in selectedOrder?.items" :key="item.id" class="flex justify-between py-2 border-b last:border-none">
            <div>
                <p class="font-medium">{{ item.product.name }}</p>
                <p class="text-xs text-gray-500">Qty: {{ item.quantity }}</p>
            </div>
            <p class="font-medium">₱{{ (item.price * item.quantity).toLocaleString() }}</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Status</label>
            <select v-model="selectedStatus"
                    :disabled="selectedOrder?.status === 'Delivered' || selectedOrder?.status === 'Canceled'"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#7D3C98] disabled:bg-gray-100 disabled:cursor-not-allowed">
            <option value="Pending">Pending</option>
            <option value="For Delivery">For Delivery</option>
            <option value="Delivered">Delivered</option>
            <option value="Canceled">Canceled</option>
            </select>
            <p v-if="selectedOrder?.status === 'Delivered' || selectedOrder?.status === 'Canceled'" class="text-[10px] text-red-500 font-bold mt-2 uppercase">
            This order is finalized and cannot be updated.
            </p>
        </div>
        </div>

        <div class="p-4 border-t flex gap-3 justify-end bg-gray-50">
        <button @click="closeModal" class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium hover:bg-gray-100">Cancel</button>
        <button @click="updateOrderStatus"
                :disabled="selectedOrder?.status === 'Delivered' || selectedOrder?.status === 'Canceled'"
                class="bg-[#7D3C98] text-white px-6 py-2.5 rounded-lg font-medium hover:bg-purple-800 disabled:bg-gray-300 disabled:cursor-not-allowed">
            Update Status
        </button>
        </div>
    </div>
    </div>

    <!-- All Products in Order Modal -->
    <div v-if="showProductsListModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="bg-[#7D3C98] px-6 py-4 text-white flex justify-between">
          <h3 class="font-semibold">All Products in Order #{{ selectedOrderForProducts?.id }}</h3>
          <button @click="showProductsListModal = false" class="text-2xl">×</button>
        </div>
        <div class="p-6 max-h-[60vh] overflow-auto space-y-4">
          <div v-for="item in selectedOrderForProducts?.items" :key="item.id" class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center gap-3">
              <img v-if="item.product.image" :src="'/storage/' + item.product.image" class="w-12 h-12 object-cover rounded">
              <div>
                <p class="font-medium">{{ item.product.name }}</p>
                <p class="text-xs text-gray-500">Qty: {{ item.quantity }} × ₱{{ parseFloat(item.price).toLocaleString() }}</p>
              </div>
            </div>
            <p class="font-semibold">₱{{ (item.price * item.quantity).toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const showOrderModal = ref(false);
const showProductsListModal = ref(false);
const selectedOrder = ref(null);
const selectedOrderForProducts = ref(null);
const selectedStatus = ref('');

const fetchOrders = async () => {
  const res = await axios.get('/api/admin/orders');
  orders.value = res.data;
};

const viewOrder = (order) => {
  selectedOrder.value = order;
  selectedStatus.value = order.status;
  showOrderModal.value = true;
};

const showProductsModal = (order) => {
  selectedOrderForProducts.value = order;
  showProductsListModal.value = true;
};

const closeModal = () => {
  showOrderModal.value = false;
};

const updateOrderStatus = async () => {
  await axios.put(`/api/admin/orders/${selectedOrder.value.id}/status`, {
    status: selectedStatus.value
  });
  alert('Status updated successfully!');
  closeModal();
  fetchOrders();
};

const deleteOrder = async (order) => {
  if (confirm('Delete this order?')) {
    await axios.delete(`/api/admin/orders/${order.id}`);
    fetchOrders();
  }
};

const getStatusClass = (status) => {
  switch(status) {
    case 'Delivered': return 'bg-green-100 text-green-700';
    case 'For Delivery': return 'bg-yellow-100 text-yellow-700';
    case 'Pending': return 'bg-orange-100 text-orange-700';
    case 'Canceled': return 'bg-red-100 text-red-700';
    default: return 'bg-gray-100 text-gray-700';
  }
};

onMounted(fetchOrders);
</script>
