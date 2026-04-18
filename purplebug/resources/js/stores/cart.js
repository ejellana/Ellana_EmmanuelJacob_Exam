import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: []
  }),

  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalAmount: (state) => state.items.reduce((sum, item) => sum + (item.price * item.quantity), 0),
  },

  actions: {
    async loadCart() {
      try {
        const res = await axios.get('/api/cart');
        this.items = res.data;
      } catch (error) {
        console.error('Failed to load cart', error);
      }
    },

    async addToCart(product) {
      try {
        await axios.post('/api/cart', {
          product_id: product.id,
          quantity: 1
        });
        await this.loadCart();   // Refresh cart after add
        return true;
      } catch (error) {
        alert(error.response?.data?.message || "Failed to add to cart");
        return false;
      }
    },

    async updateQuantity(productId, quantity) {
      try {
        await axios.put(`/api/cart/${productId}`, { quantity });
        await this.loadCart();
      } catch (error) {
        alert(error.response?.data?.message || "Failed to update quantity");
      }
    },

    async removeFromCart(productId) {
      try {
        await axios.delete(`/api/cart/${productId}`);
        await this.loadCart();
      } catch (error) {
        alert("Failed to remove item");
      }
    },

    async clearCart() {
      try {
        await axios.delete('/api/cart');
        this.items = [];
      } catch (error) {
        console.error(error);
      }
    }
  }
});
