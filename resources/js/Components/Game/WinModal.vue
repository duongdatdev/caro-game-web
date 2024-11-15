<script setup lang="ts">
import { defineProps } from '@vue/runtime-core';
import Modal from '@/Components/Modal.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
  show: boolean;
  winner: string;
}>();
</script>

<template>
  <Modal :show="show">
    <div class="p-6">
      <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          🎉 Game Over! 🎉
        </h3>
        <p class="mb-6 text-gray-600 dark:text-gray-400">
          {{ winner }} has won the game!
        </p>
        <Link
          :href="route('rooms.index')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Return to Rooms
        </Link>
      </div>
    </div>
  </Modal>
</template>