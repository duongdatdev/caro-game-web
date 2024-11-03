<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Room {
    id: number;
    name: string;
    owner: {
        name: string;
        rating: number;
    };
    player2?: {
        name: string;
        rating: number;
    };
    status: 'waiting' | 'playing' | 'finished';
    created_at: string;
}

// Sample data - will be replaced with real data from backend
const rooms = ref<Room[]>([
    {
        id: 1,
        name: 'Room #1',
        owner: {
            name: 'Player 1',
            rating: 1500
        },
        status: 'waiting',
        created_at: '2 mins ago'
    }
]);
</script>

<template>
    <Head title="Game Rooms" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game Rooms
                </h2>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Create Room
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search and Filters -->
                <div class="mb-6 flex gap-4">
                    <input 
                        type="text" 
                        placeholder="Search rooms..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                    >
                    <select class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="all">All Status</option>
                        <option value="waiting">Waiting</option>
                        <option value="playing">Playing</option>
                    </select>
                </div>

                <!-- Room List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid gap-4">
                            <div v-for="room in rooms" :key="room.id" 
                                 class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex items-center justify-between">
                                <!-- Room Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ room.name }}
                                        </h3>
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': room.status === 'waiting',
                                            'bg-green-100 text-green-800': room.status === 'playing',
                                            'bg-gray-100 text-gray-800': room.status === 'finished'
                                        }" class="px-2 py-1 text-xs rounded-full">
                                            {{ room.status }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Created {{ room.created_at }}
                                    </div>
                                </div>

                                <!-- Players -->
                                <div class="flex items-center gap-8 mr-8">
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ room.owner.name }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ room.owner.rating }} MMR
                                        </div>
                                    </div>
                                    <div class="text-gray-400">vs</div>
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ room.player2?.name || 'Waiting...' }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ room.player2?.rating || '---' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div>
                                    <button v-if="room.status === 'waiting'"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Join Game
                                    </button>
                                    <button v-else-if="room.status === 'playing'"
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                        Spectate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>