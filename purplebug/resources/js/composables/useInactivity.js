import { onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../stores/auth';

export function useInactivity() {
  const authStore = useAuthStore();
  let interval;

  const resetTimer = () => {
    authStore.updateActivity();
  };

  onMounted(() => {
    // Check inactivity every 60 seconds
    interval = setInterval(() => {
      authStore.checkInactivity();
    }, 60000);

    // Reset timer on user activity
    window.addEventListener('mousemove', resetTimer);
    window.addEventListener('keypress', resetTimer);
    window.addEventListener('click', resetTimer);
    window.addEventListener('scroll', resetTimer);
  });

  onUnmounted(() => {
    clearInterval(interval);
    window.removeEventListener('mousemove', resetTimer);
    window.removeEventListener('keypress', resetTimer);
    window.removeEventListener('click', resetTimer);
    window.removeEventListener('scroll', resetTimer);
  });
}
