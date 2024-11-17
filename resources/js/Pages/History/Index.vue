<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    games: Array,
    stats: Object
});

const filter = ref('all');
const filteredGames = computed(() => {
    if (filter.value === 'all') return props.games;
    return props.games.filter(game => game.result === filter.value);
});
</script>

<template>
    <Head title="Game History" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game History
                </h2>
                <select 
                    v-model="filter"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm"
                >
                    <option value="all">All Games</option>
                    <option value="win">Wins</option>
                    <option value="loss">Losses</option>
                </select>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Summary -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Games</h3>
                        <p class="mt-2 text-3xl font-semibold">{{ stats.total_games }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-green-500">Wins</h3>
                        <p class="mt-2 text-3xl font-semibold text-green-600">{{ stats.wins }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-red-500">Losses</h3>
                        <p class="mt-2 text-3xl font-semibold text-red-600">{{ stats.losses }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-blue-500">Win Rate</h3>
                        <p class="mt-2 text-3xl font-semibold text-blue-600">{{ stats.win_rate }}%</p>
                    </div>
                </div>

                <!-- Games List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400">
                                    <th class="pb-4">Date</th>
                                    <th class="pb-4">Opponent</th>
                                    <th class="pb-4">Result</th>
                                    <th class="pb-4">Rating Change</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="game in filteredGames" 
                                    :key="game.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="py-4">{{ game.played_at }}</td>
                                    <td class="py-4">{{ game.opponent }}</td>
                                    <td class="py-4">
                                        <span :class="{
                                            'text-green-600': game.result === 'win',
                                            'text-red-600': game.result === 'loss',
                                            'text-yellow-600': game.result === 'draw'
                                        }">
                                            {{ game.result.charAt(0).toUpperCase() + game.result.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <span :class="{
                                            'text-green-600': game.rating_change > 0,
                                            'text-red-600': game.rating_change < 0
                                        }">
                                            {{ game.rating_change > 0 ? '+' : '' }}{{ game.rating_change }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>