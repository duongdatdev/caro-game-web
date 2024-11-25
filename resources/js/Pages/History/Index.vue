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
            <div class="flex justify-between items-center animate-fade-in">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game History
                </h2>
                <select 
                    v-model="filter"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm
                           transition-all duration-200 hover:border-indigo-500 focus:border-indigo-500"
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
                    <div class="stats-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-all duration-300">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Games</h3>
                        <p class="mt-2 text-3xl font-semibold animate-number">{{ stats.total_games }}</p>
                    </div>
                    <div class="stats-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-all duration-300">
                        <h3 class="text-sm font-medium text-green-500">Wins</h3>
                        <p class="mt-2 text-3xl font-semibold text-green-600 animate-number">{{ stats.wins }}</p>
                    </div>
                    <div class="stats-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-all duration-300">
                        <h3 class="text-sm font-medium text-red-500">Losses</h3>
                        <p class="mt-2 text-3xl font-semibold text-red-600 animate-number">{{ stats.losses }}</p>
                    </div>
                    <div class="stats-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-all duration-300">
                        <h3 class="text-sm font-medium text-blue-500">Win Rate</h3>
                        <p class="mt-2 text-3xl font-semibold text-blue-600 animate-number">{{ stats.win_rate }}%</p>
                    </div>
                </div>

                <!-- Games List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-300 hover:shadow-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400">
                                    <th class="pb-4 transition-colors hover:text-gray-700 dark:hover:text-gray-300">Date</th>
                                    <th class="pb-4 transition-colors hover:text-gray-700 dark:hover:text-gray-300">Opponent</th>
                                    <th class="pb-4 transition-colors hover:text-gray-700 dark:hover:text-gray-300">Result</th>
                                    <th class="pb-4 transition-colors hover:text-gray-700 dark:hover:text-gray-300">Rating Change</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="(game, index) in filteredGames" 
                                    :key="game.id"
                                    class="game-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                                    :style="{ animationDelay: `${index * 0.1}s` }">
                                    <td class="py-4">{{ game.played_at }}</td>
                                    <td class="py-4 font-medium">{{ game.opponent }}</td>
                                    <td class="py-4">
                                        <span :class="{
                                            'px-2 py-1 rounded text-sm font-medium': true,
                                            'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': game.result === 'win',
                                            'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100': game.result === 'loss',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': game.result === 'draw'
                                        }">
                                            {{ game.result.charAt(0).toUpperCase() + game.result.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <span :class="{
                                            'font-medium transition-all duration-200': true,
                                            'text-green-600 dark:text-green-400': game.rating_change > 0,
                                            'text-red-600 dark:text-red-400': game.rating_change < 0
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

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.stats-card {
    animation: slideIn 0.5s ease-out;
    transform-origin: center;
}

.game-row {
    animation: fadeInUp 0.5s ease-out;
    animation-fill-mode: both;
}

.animate-number {
    animation: countUp 1s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes countUp {
    from {
        transform: translateY(10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>