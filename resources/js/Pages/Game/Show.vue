<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Board from '@/Components/Game/Board.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from '@vue/runtime-core';
import axios from 'axios';
import { defineProps } from '@vue/runtime-core';
import { defineComponent } from '@vue/runtime-core';
import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

const props = defineProps<{
    room: {
        id: number;
        name: string;
        status: string;
        created_by: number;
        players: Array<{
            id: number;
            name: string;
            pivot: {
                is_ready: boolean;
            };
        }>;
        moves: Array<{
            x: number;
            y: number;
            user_id: number;
        }>;
    };
    currentPlayer: number;
}>();

const getPlayerName = (userId: number): string | undefined => {
    const player = props.room?.players.find((p: { id: number }) => p.id === userId);
    return player ? player.name : undefined;
};

const moves = ref(props.room.moves);
const isYourTurn = ref(false);
const gameStatus = ref(props.room.status);
const messages = ref<Array<{ id: number; user_id: number; message: string }>>([]);
const newMessage = ref('');
const players = ref(props.room.players);


// Toggle player ready status
const toggleReady = async () => {
    try {
        const response = await axios.post(`/game/${props.room.id}/ready`);
        const player = players.value.find(p => p.id === props.currentPlayer);
        if (player) {
            player.pivot.is_ready = !player.pivot.is_ready;
        }

        if (response.data.game_started) {
            gameStatus.value = 'playing';
            isYourTurn.value = props.room.created_by === props.currentPlayer;
        }
    } catch (error) {
        console.error('Failed to toggle ready state:', error);
    }
};

// Make a move
const makeMove = async (x: number, y: number) => {
    if (!isYourTurn.value || gameStatus.value !== 'playing') return;

    // Add move immediately to local state
    const newMove = {
        x: x,
        y: y,
        user_id: props.currentPlayer,
        order: moves.value.length + 1
    };
    moves.value.push(newMove);
    isYourTurn.value = false;


    try {
        const response = await axios.post(`/game/${props.room.id}/move`, { x, y });
        if (response.data.status === 'win') {
            gameStatus.value = 'finished';
        }
    } catch (error) {
        // If request fails, rollback the move
        moves.value = moves.value.filter(move => 
            move.x !== x || move.y !== y
        );
        isYourTurn.value = true;
        console.error('Failed to make move:', error);
    }
};

// Send chat message
const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    try {
        await axios.post(`/game/${props.room.id}/message`, {
            message: newMessage.value
        });
        newMessage.value = '';
    } catch (error) {
        console.error('Failed to send message:', error);
    }
};

onMounted(() => {
    // Subscribe to real-time events using private channel
    window.Echo.private(`room.${props.room.id}`)
        .listen('.player.joined', (e: any) => {
            // Add new player to players list
            if (players.value.some((p: { id: number }) => p.id === e.player.id)) return;
            players.value.push(e.player);
            console.log('New player joined:', e.player.name);
        })
        .listen('.move.made', (e: any) => {
            // Add new move to moves list
            const newMove = {
                x: e.move.x,
                y: e.move.y,
                user_id: e.move.user_id,
                order: e.move.order
            };
            moves.value.push(newMove);
            isYourTurn.value = e.move.user_id !== props.currentPlayer;
        })
        .listen('.player.ready', (e: any) => {
            console.log('Ready event received:', e); // Add logging

            // Update player ready status
            const playerIndex = players.value.findIndex(p => p.id === e.playerId); // Update to match event data
            if (playerIndex !== -1) {
                players.value[playerIndex].pivot.is_ready = e.isReady; // Update to match event data
            }

            // Update game status if all players are ready
            if (e.roomStatus === 'playing') {
                console.log(e.roomStatus);
                gameStatus.value = 'playing';
                console.log('Game started');
                isYourTurn.value = props.room.created_by === props.currentPlayer;
            }
        })
        .listenForWhisper('typing', (e: any) => {
            console.log(e.name);
        });

    // Error handling
    window.Echo.connector.pusher.connection.bind('error', (err: any) => {
        console.error('Channel error:', err);
    });
});

onUnmounted(() => {
    // Clean up Echo subscription
    window.Echo?.private(`room.${props.room.id}`).stopListening('.move.made');
    window.Echo?.private(`room.${props.room.id}`).stopListening('.player.ready');
    window.Echo?.private(`room.${props.room.id}`).stopListening('.message.sent');
    window.Echo?.private(`room.${props.room.id}`).stopListening('.game.finished');
    window.Echo?.leave(`room.${props.room.id}`);
});
</script>
<template>

    <Head title="Playing Game" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-50">
                    {{ room.name }}
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ isYourTurn ? 'Your Turn' : "Opponent's Turn" }}
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Game Info -->
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold mb-4 dark:text-gray-50">Players</h3>
                            <div class="space-y-2">
                                <div v-for="player in room.players" :key="player.id"
                                    :class="{ 'text-green-600': player.pivot.is_ready }">
                                    {{ player.name }}
                                    <span v-if="player.pivot.is_ready">(Ready)</span>
                                </div>
                            </div>

                            <button v-if="gameStatus === 'waiting'" @click="toggleReady()"
                                class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Toggle Ready
                            </button>
                        </div>
                    </div>

                    <!-- Game Board -->
                    <div class="w-full lg:w-2/4">
                        <Board :moves="moves" :disabled="!isYourTurn || gameStatus !== 'playing'"
                            :current-player="currentPlayer" @move="makeMove" />
                    </div>

                    <!-- Chat -->
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 h-full flex flex-col">
                            <h3 class="text-lg font-semibold mb-4">Chat</h3>
                            <div class="flex-1 overflow-y-auto mb-4 space-y-2">
                                <div v-for="message in messages" :key="message.id" class="text-sm">
                                    <span class="font-medium">
                                        {{ getPlayerName(messages.user_id) }}:
                                    </span>
                                    {{ messages.message }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <input v-model="newMessage" type="text"
                                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                    placeholder="Type a message..." @keyup.enter="sendMessage">
                                <button @click="sendMessage"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>