<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from '@vue/runtime-core';

interface GameHistory {
    id: number;
    opponent: string;
    result: 'win' | 'loss' | 'draw';
    rating_change: number;
    played_at: string;
    duration: string;
    moves: number;
}

const history = ref<GameHistory[]>([
    {
        id: 1,
        opponent: 'Player123',
        result: 'win',
        rating_change: 15,
        played_at: '2024-01-20 15:30',
        duration: '12:35',
        moves: 45
    },
    // Add more sample data
]);
</script>

<template>
    <Head title="Game History" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game History
                </h2>
                <div class="flex gap-2">
                    <select class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm dark:text-gray-50">
                        <option value="all">All Games</option>
                        <option value="wins">Wins</option>
                        <option value="losses">Losses</option>
                        <option value="draws">Draws</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-500">
                                        <th class="pb-4 font-medium">Date</th>
                                        <th class="pb-4 font-medium">Opponent</th>
                                        <th class="pb-4 font-medium">Result</th>
                                        <th class="pb-4 font-medium">Rating Change</th>
                                        <th class="pb-4 font-medium">Duration</th>
                                        <th class="pb-4 font-medium">Moves</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 dark:text-gray-50">
                                    <tr v-for="game in history" 
                                        :key="game.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="py-4">{{ game.played_at }}</td>
                                        <td class="py-4 font-medium">{{ game.opponent }}</td>
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
                                        <td class="py-4">{{ game.duration }}</td>
                                        <td class="py-4">{{ game.moves }}</td>
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