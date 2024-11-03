<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Gomoku Online" />
    <div class="bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="bg-white dark:bg-gray-800 shadow px-4 py-4">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-white">
                        Gomoku Online
                    </div>

                    <DarkModeToggle class="mr-4" />
                    
                    <div class="flex items-center space-x-4">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="route('dashboard')"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                            >
                                Play Game
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="flex-grow">
                <div class="max-w-7xl mx-auto px-4 py-16">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            Welcome to Gomoku Online
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
                            Play the classic game of Five in a Row with players around the world
                        </p>
                        
                        <div class="flex justify-center">
                            <Link
                                :href="$page.props.auth.user ? route('dashboard') : route('register')"
                                class="px-8 py-3 bg-indigo-600 text-white text-lg rounded-lg hover:bg-indigo-700 transition"
                            >
                                {{ $page.props.auth.user ? 'Play Now' : 'Get Started' }}
                            </Link>
                        </div>
                    </div>

                    <!-- Game Features -->
                    <div class="grid md:grid-cols-3 gap-8 mt-16">
                        <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Real-time Matches</h3>
                            <p class="text-gray-600 dark:text-gray-400">Play live games with opponents from anywhere</p>
                        </div>
                        <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Global Leaderboard</h3>
                            <p class="text-gray-600 dark:text-gray-400">Compete for top rankings</p>
                        </div>
                        <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">In-game Chat</h3>
                            <p class="text-gray-600 dark:text-gray-400">Chat with your opponents while playing</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <div class="text-center text-gray-600 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Gomoku Online. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>