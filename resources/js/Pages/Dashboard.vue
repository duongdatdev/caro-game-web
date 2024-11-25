<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CreateRoomModal from '@/Components/Game/CreateRoomModal.vue';
import { Link, Head } from '@inertiajs/vue3';
import { onMounted,ref } from '@vue/runtime-core';
import { defineProps } from '@vue/runtime-core';

interface PlayerStats {
    wins: number;
    losses: number;
    draws: number;
    rank: number;
    rating: number;
}

interface Activity {
    type: 'win' | 'loss' | 'draw';
    description: string;
    time: string;
    rating_change?: number;
}

const showCreateModal = ref(false);

const props = defineProps<{
    rooms: any[];
    stats: PlayerStats;
    recentActivities: Activity[];
}>();
onMounted(() => {
    if (window.Echo?.connector?.pusher) {
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('Successfully connected to Pusher');
        });

        window.Echo.connector.pusher.connection.bind('error', (err: any) => {
            console.error('Pusher connection error:', err);
        });
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 animate-fade-in">
                    Game Dashboard
                </h2>
                <div class="flex items-center space-x-4 animate-fade-in">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Welcome back, {{ $page.props.auth.user.name }}! 👋
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Quick Actions Card -->
                <div class="grid gap-6 md:grid-cols-2 animate-fade-in-up">
                    <div class="transform overflow-hidden rounded-xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="showCreateModal = true"
                                class="group flex items-center justify-center space-x-2 rounded-lg bg-indigo-600 px-4 py-2 text-white transition-all duration-300 hover:bg-indigo-700">
                                <span>Create Room</span>
                                <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                            </button>
                            <Link :href="route('rooms.index')"
                                class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-all duration-300 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                <span>Join Room</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Player Stats -->
                    <div class="transform overflow-hidden rounded-xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Your Statistics
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="transform rounded-lg bg-green-50 p-4 text-center transition-all duration-300 hover:-translate-y-1 dark:bg-green-900/20">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.wins }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Wins</div>
                            </div>
                            <div class="transform rounded-lg bg-red-50 p-4 text-center transition-all duration-300 hover:-translate-y-1 dark:bg-red-900/20">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.losses }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Losses</div>
                            </div>
                            <div class="transform rounded-lg bg-yellow-50 p-4 text-center transition-all duration-300 hover:-translate-y-1 dark:bg-yellow-900/20">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.draws }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Draws</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Recent Activity
                            </h3>
                            <Link :href="route('history')"
                                class="text-sm text-indigo-600 transition-colors hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                View All
                            </Link>
                        </div>
                        <div class="space-y-4">
                            <div v-if="recentActivities?.length > 0">
                                <div v-for="(activity, index) in recentActivities" :key="index"
                                    class="transform rounded-lg bg-gray-50 p-4 transition-all duration-300 hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-2xl">
                                                {{ activity.type === 'win' ? '🏆' : activity.type === 'loss' ? '❌' : '🤝' }}
                                            </span>
                                            <span class="text-gray-700 dark:text-gray-300">{{ activity.description }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span v-if="activity.rating_change" 
                                                :class="{
                                                    'text-green-600 dark:text-green-400': activity.rating_change > 0,
                                                    'text-red-600 dark:text-red-400': activity.rating_change < 0
                                                }"
                                                class="font-medium">
                                                {{ activity.rating_change > 0 ? '+' : '' }}{{ activity.rating_change }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ activity.time }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center text-gray-500 dark:text-gray-400">
                                No recent activities
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CreateRoomModal :show="showCreateModal" @close="showCreateModal = false" />
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
    opacity: 0;
    animation-fill-mode: forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Add smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Hover effects for cards */
.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

/* Dark mode improvements */
.dark .dark\:bg-gray-800 {
    background-color: rgba(31, 41, 55, 0.95);
}
</style>