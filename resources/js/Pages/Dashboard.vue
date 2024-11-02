<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

interface PlayerStats {
    wins: number;
    losses: number;
    draws: number;
    rank: number;
    rating: number;
}

const stats: PlayerStats = {
    wins: 10,
    losses: 5,
    draws: 2,
    rank: 42,
    rating: 1500
};
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
                            <!-- <Link :href="route('rooms.create')"
                                class="flex items-center justify-center p-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">🎮</div>
                                    <div>Create Room</div>
                                </div>
                            </Link> -->
                            <!-- <Link :href="route('rooms.index')"
                                class="flex items-center justify-center p-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <div class="text-center">
                                    <div class="text-2xl mb-1">🔍</div>
                                    <div>Join Room</div>
                                </div>
                            </Link> -->
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
                                Rating: {{ stats.rating }}
                            </div>
                        </div>
                        <div class="mt-4">
                            <!-- <Link :href="route('leaderboard')"
                                class="w-full flex justify-center py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                View Leaderboard
                            </Link> -->
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                            Recent Activity
                        </h3>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span>Won against Player123</span>
                                <span class="text-sm text-gray-500">2m ago</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span>Lost to Player456</span>
                                <span class="text-sm text-gray-500">15m ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>