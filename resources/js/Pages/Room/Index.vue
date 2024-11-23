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
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Game Rooms
                </h2>
                <PrimaryButton @click="showCreateModal = true">
                    Create Room
                </PrimaryButton>
            </div>
        </template>
        <div class="mb-4">
            <select v-model="statusFilter" class="rounded-md border-gray-300">
                <option value="active">Active Rooms</option>
                <option value="finished">Finished Rooms</option>
                <option value="all">All Rooms</option>
            </select>
        </div>
        <!-- Room List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="room in filteredRooms" :key="room.id"
                class="p-4 border border-black rounded-lg dark:border-gray-700 dark:text-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold">{{ room.name }}</h3>
                        <p class="text-sm text-gray-500">
                            Created by: {{ room.creator.name }}
                        </p>
                        <p class="text-sm">
                            Players: {{ room.players.length }}/2
                        </p>
                    </div>
                    <span :class="{
                        'bg-yellow-100 text-yellow-800': room.status === 'waiting',
                        'bg-green-100 text-green-800': room.status === 'playing',
                        'bg-gray-100 text-gray-800': room.status === 'finished'
                    }" class="px-2 py-1 rounded-full text-xs">
                        {{ room.status }}
                    </span>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="joinRoom(room)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        :disabled="room.players.length >= 2 || room.status !== 'waiting'">
                        {{ room.password ? 'Join (Password Required)' : 'Join' }}
                    </button>
                </div>
            </div>
        </div>

        <PasswordModal v-if="selectedRoom" :show="showPasswordModal" :room-id="selectedRoom.id"
            :errors="joinForm.errors" @close="showPasswordModal = false" @submit="handlePasswordSubmit" />


        <!-- Create Room Modal -->
        <CreateRoomModal :show="showCreateModal" @close="showCreateModal = false" />
        <Toast v-if="showToast" :message="toastMessage" :type="toastType" @close="showToast = false" />
    </AuthenticatedLayout>
</template>