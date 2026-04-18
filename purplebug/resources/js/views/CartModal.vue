<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col">

      <!-- Modal Header -->
      <div class="flex justify-between items-center px-8 py-6 border-b">
        <h2 class="text-2xl font-bold">🛒 Your Cart</h2>
        <button @click="close" class="text-3xl text-gray-400 hover:text-gray-600">×</button>
      </div>

      <!-- Cart Items -->
      <div class="flex-1 overflow-auto p-6 space-y-4">
        <div v-if="cartStore.items.length === 0" class="text-center py-16 text-gray-500">
          Your cart is empty
        </div>

        <div v-for="item in cartStore.items" :key="item.id" class="flex gap-4 bg-gray-50 rounded-2xl p-4">
          <img v-if="item.image" :src="'/storage/' + item.image" class="w-20 h-20 object-cover rounded-xl">
          <div class="flex-1">
            <h4 class="font-semibold">{{ item.name }}</h4>
            <p class="text-purple-600 font-bold">₱{{ (item.price * item.quantity).toFixed(2) }}</p>

            <div class="flex items-center gap-3 mt-3">
              <button @click="decreaseQty(item)" class="w-8 h-8 border rounded-lg hover:bg-gray-100">-</button>
              <span class="font-medium w-8 text-center">{{ item.quantity }}</span>
              <button @click="increaseQty(item)" class="w-8 h-8 border rounded-lg hover:bg-gray-100">+</button>
              <button @click="removeItem(item.id)" class="ml-auto text-red-600 hover:text-red-700">Remove</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-6 border-t bg-gray-50 rounded-b-3xl">
        <div class="flex justify-between text-xl font-bold mb-6">
          <span>Total</span>
          <span>₱{{ cartStore.totalAmount.toFixed(2) }}</span>
        </div>

        <button @click="checkout"
                :disabled="cartStore.items.length === 0"
                class="w-full bg-purple-600 text-white py-4 rounded-2xl text-lg font-semibold hover:bg-purple-700 disabled:bg-gray-300">
          Place Order
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart';

const cartStore = useCartStore();
const emit = defineEmits(['close']);

const close = () => emit('close');

const increaseQty = (item) => {
  cartStore.updateQuantity(item.id, item.quantity + 1);
};

const decreaseQty = (item) => {
  if (item.quantity > 1) {
    cartStore.updateQuantity(item.id, item.quantity - 1);
  }
};

const removeItem = (id) => {
  cartStore.removeFromCart(id);
};

const checkout = () => {
  if (confirm('Proceed to checkout?')) {
    // Will implement order later
    alert('Order placed successfully! (Orders module coming soon)');
    cartStore.clearCart();
    close();
  }
};
</script>
