<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from '@vue/runtime-core';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { defineProps } from '@vue/runtime-core';

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
const showJoinModal = ref(false);
const selectedRoom = ref<Room | null>(null);

const joinForm = useForm({
    password: '',
});

const form = useForm({
    name: '',
    description: '',
    password: '',
    has_password: false
});

const createRoom = () => {
    form.post(route('rooms.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

const joinRoom = (room: Room) => {
    selectedRoom.value = room;
    if (room.password) {
        showJoinModal.value = true;
    } else {
        joinForm.post(route('rooms.join', room.id), {
            onSuccess: () => {
                showJoinModal.value = false;
                joinForm.reset();
            }
        });
    }
};

defineProps<{
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

        <!-- Room List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="room in rooms" :key="room.id" class="p-4 border border-black rounded-lg dark:border-gray-700 dark:text-gray-50">
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

        <!-- Join Room Modal -->
        <Modal :show="showJoinModal" @close="showJoinModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Join Room
                </h2>
                <form @submit.prevent="joinRoom(selectedRoom)" class="mt-6">
                    <div>
                        <InputLabel for="password" value="Room Password" />
                        <TextInput id="password" v-model="joinForm.password" type="password" class="mt-1 block w-full"
                            required />
                        <InputError :message="joinForm.errors.password" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showJoinModal = false" class="mr-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="joinForm.processing">
                            Join
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Create Room Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Create New Room
                </h2>

                <form @submit.prevent="createRoom" class="mt-6">
                    <div>
                        <InputLabel for="name" value="Room Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <InputLabel for="description" value="Description (Optional)" />
                        <TextInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" />
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="has_password" v-model="form.has_password"
                                class="rounded border-gray-300 dark:border-gray-700" />
                            <label for="has_password" class="ml-2">Password protect this room</label>
                        </div>

                        <div v-if="form.has_password" class="mt-4">
                            <InputLabel for="password" value="Room Password" />
                            <TextInput id="password" v-model="form.password" type="password"
                                class="mt-1 block w-full" />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <PrimaryButton type="submit" :disabled="form.processing">
                            Create Room
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>