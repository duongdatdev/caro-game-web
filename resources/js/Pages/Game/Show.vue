<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Board from '@/Components/Game/Board.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Playing Game" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game Room #1
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Your Turn
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <!-- Mobile Game Info -->
                    <div class="block lg:hidden mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-black mr-2"></div>
                                <span class="font-medium">Player 1 (1500)</span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-medium">Player 2 (1480)</span>
                                <div class="w-3 h-3 rounded-full bg-white border border-black ml-2"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <div>Time: 15:00</div>
                            <div>Moves: 12</div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row">
                        <!-- Game Info & Players (Desktop) -->
                        <div class="hidden lg:block w-1/4 pr-6">
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    Players
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-black mr-2"></div>
                                            <span class="font-medium">Player 1</span>
                                        </div>
                                        <span class="text-sm text-gray-600">1500</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-white border border-black mr-2"></div>
                                            <span class="font-medium">Player 2</span>
                                        </div>
                                        <span class="text-sm text-gray-600">1480</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Game Info</h3>
                                <div class="text-sm text-gray-600 space-y-2">
                                    <div>Time: 15:00</div>
                                    <div>Moves: 12</div>
                                </div>
                            </div>

                            <button class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
                                Forfeit Game
                            </button>
                        </div>

                        <!-- Game Board -->
                        <div class="w-full lg:w-2/4 flex justify-center mb-6 lg:mb-0 overflow-auto">
                            <div class="transform scale-90 sm:scale-100">
                                <Board />
                            </div>
                        </div>

                        <!-- Chat Section -->
                        <div class="w-full lg:w-1/4 lg:pl-6">
                            <div class="h-[300px] lg:h-[600px] flex flex-col">
                                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Chat</h3>
                                <div class="flex-1 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4 overflow-y-auto">
                                    <div class="space-y-2">
                                        <div class="text-sm">
                                            <span class="font-medium">Player 1:</span> Good luck!
                                        </div>
                                        <div class="text-sm">
                                            <span class="font-medium">Player 2:</span> You too!
                                        </div>
                                    </div>
                                </div>
                                <div class="flex">
                                    <input type="text" 
                                           class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                                           placeholder="Type a message...">
                                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Controls -->
                    <div class="block lg:hidden mt-6">
                        <button class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
                            Forfeit Game
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>