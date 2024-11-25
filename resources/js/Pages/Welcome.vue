<script setup lang="ts">
// script setup
import { Head, Link } from '@inertiajs/vue3';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { onMounted, ref } from '@vue/runtime-core';

const showHero = ref(false);
const showFeatures = ref(false);
const showStats = ref(false);

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string; 
    phpVersion: string;
}>();

function getFeatureDescription(feature: string): string {
  const descriptions = {
    'Real-time Matches': 'Enjoy smooth real-time gameplay with WebSocket technology',
    'Global Rankings': 'Compete globally and track your progress on our leaderboards',
    'Smart Matchmaking': 'Get matched with players of similar skill level',
    'Game Analysis': 'Review your matches and improve your strategy',
    'Custom Rooms': 'Create private rooms to play with friends',
    'Achievement System': 'Earn badges and rewards as you play'
  };
  return descriptions[feature as keyof typeof descriptions];
}

onMounted(() => {
  // Animate sections sequentially
  setTimeout(() => showHero.value = true, 100);
  setTimeout(() => showFeatures.value = true, 500);
  setTimeout(() => showStats.value = true, 900);
});
</script>

<template>
  <Head title="Gomoku Online - Play Five in a Row" />
  
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 
              dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <Link :href="route('dashboard')" class="flex items-center space-x-2">
              <img src="/images/logo.svg" alt="Logo" class="h-8 w-8" />
              <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 
                         bg-clip-text text-transparent">Gomoku Online</span>
            </Link>
          </div>
          
          <div class="flex items-center space-x-4">
            <DarkModeToggle />
            <template v-if="canLogin">
              <Link v-if="$page.props.auth.user" 
                    :href="route('dashboard')"
                    class="inline-flex items-center px-4 py-2 border border-transparent 
                           rounded-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                           transition-all duration-150">
                Play Now
              </Link>
              <template v-else>
                <Link :href="route('login')" 
                      class="text-gray-600 dark:text-gray-300 hover:text-indigo-600 
                             dark:hover:text-indigo-400">
                  Log in
                </Link>
                <Link v-if="canRegister" 
                      :href="route('register')"
                      class="inline-flex items-center px-4 py-2 border border-transparent 
                             rounded-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                  Get Started
                </Link>
              </template>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="pt-24 pb-16 sm:pt-32 relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center transform transition-all duration-700"
             :class="showHero ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
          <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight mb-8">
            <span class="block bg-gradient-to-r from-indigo-600 to-purple-600 
                       bg-clip-text text-transparent">
              Master the Game of Five in a Row
            </span>
          </h1>
          <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600 dark:text-gray-300">
            Challenge players worldwide in the classic game of strategy. 
            Improve your skills, climb the ranks, and become a Gomoku master.
          </p>
          <div class="mt-10">
            <Link :href="$page.props.auth.user ? route('dashboard') : route('register')"
                  class="inline-flex items-center px-8 py-4 rounded-xl text-lg font-semibold
                         text-white bg-gradient-to-r from-indigo-600 to-purple-600 
                         hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105
                         transition-all duration-200 shadow-lg hover:shadow-xl">
              {{ $page.props.auth.user ? 'Start Playing' : 'Join Free Now' }}
              <svg class="ml-3 -mr-1 h-5 w-5 animate-bounce" fill="none" 
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Grid -->
    <div class="py-16 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Everything You Need to Play
          </h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
             :class="showFeatures ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
          <template v-for="(feature, index) in [
            'Real-time Matches',
            'Global Rankings', 
            'Smart Matchmaking',
            'Game Analysis',
            'Custom Rooms',
            'Achievement System'
          ]" :key="index">
            <div class="relative group">
              <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-8
                          transform transition-all duration-200 group-hover:scale-105">
                <div class="text-indigo-600 dark:text-indigo-400 mb-4">
                  <!-- Add feature icon here -->
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                  {{ feature }}
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                  {{ getFeatureDescription(feature) }}
                </p>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="py-16"
         :class="showStats ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
          <div class="mx-auto flex max-w-xs flex-col gap-y-4">
            <dt class="text-base leading-7 text-gray-600 dark:text-gray-400">Active players</dt>
            <dd class="order-first text-3xl font-semibold tracking-tight sm:text-5xl 
                     text-indigo-600 dark:text-indigo-400">10K+</dd>
          </div>
          <div class="mx-auto flex max-w-xs flex-col gap-y-4">
            <dt class="text-base leading-7 text-gray-600 dark:text-gray-400">Games played</dt>
            <dd class="order-first text-3xl font-semibold tracking-tight sm:text-5xl 
                     text-indigo-600 dark:text-indigo-400">100K+</dd>
          </div>
          <div class="mx-auto flex max-w-xs flex-col gap-y-4">
            <dt class="text-base leading-7 text-gray-600 dark:text-gray-400">Countries</dt>
            <dd class="order-first text-3xl font-semibold tracking-tight sm:text-5xl 
                     text-indigo-600 dark:text-indigo-400">50+</dd>
          </div>
        </dl>
      </div>
    </div>

  </div>
</template>

<style>
.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 4s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>

<style>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 4s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}


</style>