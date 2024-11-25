<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from '@vue/runtime-core';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { defineProps, computed } from '@vue/runtime-core';
import PasswordModal from '@/Components/Game/PasswordModal.vue';
import Toast from '@/Components/Toast.vue';
import CreateRoomModal from '../../Components/Game/CreateRoomModal.vue';

type Room = {
    id: number;
    name: string;
    status: string;
    created_at: string;
    creator: {
        name: string;
    };
    players: Array<{
        name: string;
    }>;
    password?: string;
};

const showCreateModal = ref(false);
const showPasswordModal = ref(false);
const selectedRoom = ref<Room | null>(null);

const props = defineProps<{
    rooms: {
        id: number;
        name: string;
        status: string;
        created_at: string;
        creator: {
            name: string;
        };
        players: Array<{
            name: string;
        }>;
    }[];
}>();

const statusFilter = ref('active');

const filteredRooms = computed(() => {
    if (statusFilter.value === 'all') return props.rooms;
    if (statusFilter.value === 'active') {
        return props.rooms.filter(room => room.status !== 'finished');
    }
    return props.rooms.filter(room => room.status === 'finished');
});

const joinForm = useForm({
    password: '',
});

const toastMessage = ref('');
const toastType = ref<'error' | 'success' | 'info'>('error');
const showToast = ref(false);

const joinRoom = (room: Room) => {
    if (room.password) {
        selectedRoom.value = room;
        showPasswordModal.value = true;
    } else {
        joinForm.post(route('rooms.join', room.id), {
            onSuccess: () => {
                // Redirect on successful join
                window.location.href = route('game.show', room.id);
            },
            onError: (errors) => {
                if (errors.general) {
                    toastMessage.value = errors.general;
                } else if (errors.password) {
                    toastMessage.value = errors.password;
                } else {
                    toastMessage.value = 'Failed to join room';
                }
                toastType.value = 'error';
                showToast.value = true;
            }
        });
    }
};

const handlePasswordSubmit = (password: string) => {
    joinForm.password = password;
    joinForm.post(route('rooms.join', selectedRoom.value?.id), {
        onSuccess: () => {
            // Redirect on successful join
            window.location.href = route('game.show', selectedRoom.value?.id);
        },
        onError: (errors) => {
            if (errors.general) {
                toastMessage.value = errors.general;
            } else if (errors.password) {
                toastMessage.value = errors.password;
            } else {
                toastMessage.value = 'Invalid password';
            }
            toastType.value = 'error';
            showToast.value = true;
        }
    });
};
</script>

<template>
    <Head title="Game Rooms" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 
                         animate-fade-in">
                    Game Rooms
                </h2>
                <PrimaryButton @click="showCreateModal = true"
                    class="transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Room
                    </span>
                </PrimaryButton>
            </div>
        </template>

        <!-- Filter Section -->
        <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm 
                    animate-fade-in transition-all duration-300">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="text-gray-700 dark:text-gray-300">Filter:</label>
                    <select v-model="statusFilter" 
                        class="rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-300
                               focus:border-indigo-500 focus:ring-indigo-500
                               transition-colors duration-200">
                        <option value="active">Active Rooms</option>
                        <option value="finished">Finished Rooms</option>
                        <option value="all">All Rooms</option>
                    </select>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ filteredRooms.length }} rooms found
                </div>
            </div>
        </div>

        <!-- Room Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <TransitionGroup 
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 transform translate-y-4"
                enter-to-class="opacity-100 transform translate-y-0"
                leave-active-class="transition-all duration-300 ease-in"
                leave-from-class="opacity-100 transform scale-100"
                leave-to-class="opacity-0 transform scale-95"
            >
                <div v-for="room in filteredRooms" :key="room.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden
                           hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1
                           border border-gray-200 dark:border-gray-700">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ room.name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Created by {{ room.creator.name }}
                                </p>
                            </div>
                            <span :class="{
                                'px-3 py-1 rounded-full text-xs font-medium': true,
                                'bg-yellow-100 text-yellow-800': room.status === 'waiting',
                                'bg-green-100 text-green-800': room.status === 'playing',
                                'bg-gray-100 text-gray-800': room.status === 'finished'
                            }">
                                {{ room.status }}
                            </span>
                        </div>

                        <div class="flex items-center mb-4">
                            <div class="flex -space-x-2">
                                <div v-for="player in room.players" :key="player.name" 
                                     class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center
                                            text-white text-sm border-2 border-white dark:border-gray-800">
                                    {{ player.name.charAt(0) }}
                                </div>
                            </div>
                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ room.players.length }}/2 Players
                            </span>
                        </div>

                        <button @click="joinRoom(room)" 
                            :disabled="room.players.length >= 2 || room.status !== 'waiting'"
                            class="w-full px-4 py-2 text-sm font-medium rounded-md
                                   transition-colors duration-200 
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   bg-indigo-600 text-white hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   focus:ring-indigo-500">
                            {{ room.password ? '🔒 Join (Password Required)' : 'Join Room' }}
                        </button>
                    </div>
                </div>
            </TransitionGroup>
        </div>

        <PasswordModal v-if="selectedRoom" 
            :show="showPasswordModal" 
            :room-id="selectedRoom.id"
            :errors="joinForm.errors" 
            @close="showPasswordModal = false" 
            @submit="handlePasswordSubmit" />

        <CreateRoomModal 
            :show="showCreateModal" 
            @close="showCreateModal = false" />

        <Toast v-if="showToast" 
            :message="toastMessage" 
            :type="toastType" 
            @close="showToast = false" />
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
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