<script setup lang="ts">
import { ref, onMounted, watch, nextTick, onBeforeUnmount } from '@vue/runtime-core';

const props = defineProps<{
  message: string;
  type?: 'error' | 'success' | 'info';
  duration?: number;
}>();

const show = ref(false);
let timer: ReturnType<typeof setTimeout>;

// Watch for changes in message to reset and show toast
watch(() => props.message, (newMessage) => {
  if (newMessage) {
    // Clear any existing timer
    if (timer) clearTimeout(timer);
    
    // Reset and show toast
    show.value = false;
    nextTick(() => {
      show.value = true;
      // Set new timer
      timer = setTimeout(() => {
        show.value = false;
      }, props.duration || 3000);
    });
  }
}, { immediate: true });

// Clean up timer on component unmount
onBeforeUnmount(() => {
  if (timer) clearTimeout(timer);
});
</script>

<template>
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" 
        class="fixed top-4 right-4 z-50 rounded-md p-4 shadow-lg"
        :class="{
          'bg-red-50 text-red-700': type === 'error',
          'bg-green-50 text-green-700': type === 'success',
          'bg-blue-50 text-blue-700': type === 'info'
        }"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <span v-if="type === 'error'" class="text-red-400">❌</span>
            <span v-if="type === 'success'" class="text-green-400">✅</span>
            <span v-if="type === 'info'" class="text-blue-400">ℹ️</span>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium">{{ message }}</p>
          </div>
        </div>
      </div>
    </Transition>
  </template>