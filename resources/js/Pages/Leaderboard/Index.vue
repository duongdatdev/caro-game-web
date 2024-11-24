<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from '@vue/runtime-core';

interface Player {
    rank: number;
    name: string;
    rating: number;
    wins: number;
    losses: number;
    winRate: number;
    isCurrentUser: boolean;
}

defineProps<{
    players: Player[];
}>();

const timeframes = ['All Time', 'This Month', 'This Week'];
const selectedTimeframe = ref('All Time');
</script>

<template>
    <Head title="Leaderboard" />
    <AuthenticatedLayout>
        <template #header>
           <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Leaderboard
                </h2>
                <div class="flex flex-wrap gap-2">
                    <button v-for="timeframe in timeframes" 
                            :key="timeframe"
                            @click="selectedTimeframe = timeframe"
                            :class="[
                                'px-3 py-1 rounded-lg text-sm transition flex-grow sm:flex-grow-0',
                                selectedTimeframe === timeframe 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'
                            ]">
                        {{ timeframe }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Top 3 Players -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div v-for="player in players.slice(0, 3)" 
                                 :key="player.rank"
                                 class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                                <div class="text-4xl mb-2">
                                    {{ player.rank === 1 ? '🥇' : player.rank === 2 ? '🥈' : '🥉' }}
                                </div>
                                <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                    {{ player.name }}
                                </div>
                                <div class="text-indigo-600 dark:text-indigo-400 font-semibold">
                                    {{ player.rating }} MMR
                                </div>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Win Rate: {{ player.winRate }}%
                                </div>
                            </div>
                        </div>

                        <!-- Rankings Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Rank</th>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Player</th>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Rating</th>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Wins</th>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Losses</th>
                                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Win Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="player in players" :key="player.id" 
                                        :class="[
                                            'hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out',
                                            player.isCurrentUser ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''
                                        ]">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700">
                                                <span class="text-sm font-medium">#{{ player.rank }}</span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ player.name }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                {{ player.rating }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex items-center rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1">
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ player.wins }}</span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-2 py-1">
                                                <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ player.losses }}</span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="text-sm font-medium">{{ player.winRate }}%</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>