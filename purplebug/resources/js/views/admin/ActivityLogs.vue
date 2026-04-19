<template>
  <div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-semibold text-purple-800">Activity Logs</h2>
        <p class="text-gray-500 text-sm">System activity history</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-[#C8A2C8] shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-[#7D3C98] text-white">
          <tr>
            <th class="px-6 py-4 text-left">Time</th>
            <th class="px-6 py-4 text-left">User</th>
            <th class="px-6 py-4 text-left">Action</th>
            <th class="px-6 py-4 text-left">Details</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="log in logs" :key="log.id" class="hover:bg-purple-50/30 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-500">
              {{ new Date(log.created_at).toLocaleString() }}
            </td>
            <td class="px-6 py-4 font-medium">{{ log.causer?.full_name || 'System' }}</td>
            <td class="px-6 py-4">
              <span class="capitalize font-medium text-gray-700">{{ log.description }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
              {{ formatDetails(log) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);

const fetchLogs = async () => {
  const res = await axios.get('/api/admin/activity-logs');
  logs.value = res.data;
};

const formatDetails = (log) => {
  const prop = log.properties || {};

  if (log.description.includes('placed new order')) {
    return `Order #${prop.order_id || '-'} • ₱${parseFloat(prop.total_amount || 0).toLocaleString()} • ${prop.items_count || 0} item(s)`;
  }

  if (log.description.includes('updated order status')) {
    return `${prop.old_status || '-'} → ${prop.new_status || '-'}`;
  }

  if (log.description.includes('User created')) {
    return `Created user: ${prop.attributes?.full_name || prop.full_name || '-'}`;
  }

  if (log.description.includes('User updated')) {
    let changes = [];
    if (prop.attributes?.is_active !== undefined) {
      changes.push(`Active: ${prop.old?.is_active ? 'Yes' : 'No'} → ${prop.attributes.is_active ? 'Yes' : 'No'}`);
    }
    return changes.length ? changes.join(', ') : 'User details updated';
  }

  if (log.description.includes('User deleted') || log.description.includes('deleted')) {
    return `Deleted user: ${prop.old?.full_name || '-'}`;
  }

  return '-';
};

onMounted(fetchLogs);
</script>
