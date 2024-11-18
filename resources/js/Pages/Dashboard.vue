<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CreateRoomModal from '@/Pages/Room/CreateRoomModal.vue';
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

    <Head title="Game Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game Dashboard
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Welcome, {{ $page.props.auth.user.name }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Game Actions -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <PrimaryButton @click="showCreateModal = true">
                                Create Room
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Player Stats -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                            Your Statistics
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ stats.wins }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Wins</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ stats.losses }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Losses</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ stats.draws }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Draws</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Features -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                            Rankings
                        </h3>
                        <div class="text-center p-4">
                            <div class="text-3xl font-bold text-indigo-600 mb-2">#{{ stats.rank }}</div>
                            <div class="text-lg text-gray-600 dark:text-gray-400">
                                Current Ranking
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                Rating:
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link :href="route('leaderboard')"
                                class="w-full flex justify-center py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            View Leaderboard
                            </Link>
                        </div>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Recent Activity
                            </h3>
                            <Link :href="route('history')"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            View All
                            </Link>
                        </div>

                        <div class="space-y-2">
                            <div v-if="recentActivities && recentActivities.length > 0">
                                <div v-for="(activity, index) in recentActivities" :key="index"
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex items-center gap-3">
                                        <span :class="{
                                            'text-green-600 dark:text-green-400': activity.type === 'win',
                                            'text-red-600 dark:text-red-400': activity.type === 'loss',
                                            'text-yellow-600 dark:text-yellow-400': activity.type === 'draw'
                                        }">
                                            {{ activity.type === 'win' ? '🏆' : activity.type === 'loss' ? '❌' : '🤝' }}
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ activity.description }}</span>
                                        <span v-if="activity.rating_change" :class="{
                                            'text-green-600 dark:text-green-400': activity.rating_change > 0,
                                            'text-red-600 dark:text-red-400': activity.rating_change < 0
                                        }">
                                            ({{ activity.rating_change > 0 ? '+' : '' }}{{ activity.rating_change }})
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ activity.time }}</span>
                                </div>
                            </div>
                            <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
                                No recent activities
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Room Modal -->
        <CreateRoomModal :show="showCreateModal" @close="showCreateModal = false" />
    </AuthenticatedLayout>
</template>