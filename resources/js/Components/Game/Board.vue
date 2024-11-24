<script setup lang="ts">
import { computed } from '@vue/runtime-core';

interface Square {
    x: number;
    y: number;
    value: null | 'X' | 'O';
}

const props = defineProps<{
    moves: Array<{ x: number, y: number, user_id: number }>;
    disabled: boolean;
    currentPlayer: number;
    isGameFinished: boolean;
}>();

const emit = defineEmits<{
    (e: 'move', x: number, y: number): void;
}>();

const board = computed(() => {
    const grid: Square[][] = Array(15).fill(null).map((_, i) =>
        Array(15).fill(null).map((_, j) => ({
            x: j,
            y: i,
            value: null
        }))
    );

    props.moves.forEach((move: { x: number, y: number, user_id: number }) => {
        grid[move.y][move.x].value = move.user_id === props.currentPlayer ? 'X' : 'O';
    });

    return grid;
});

const handleClick = (x: number, y: number) => {
    // Don't allow moves if board is disabled or cell is already occupied
    if (props.disabled || board.value[y][x].value !== null) {
        return;
    }

    // Emit move event to parent component
    emit('move', x, y);
};
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="bg-amber-100 p-4 rounded-lg shadow-lg">
            <div v-for="(row, i) in board" :key="i" class="flex">
                <div v-for="(cell, j) in row" :key="`${i}-${j}`" @click="handleClick(cell.x, cell.y)" :class="{
                    'cursor-not-allowed': props.disabled || cell.value !== null,
                    'cursor-pointer': !props.disabled && cell.value === null,
                    'hover:bg-amber-200': !props.disabled && cell.value === null,
                    // Base size for mobile (sm)
                    'w-5 h-5': true,
                    // Medium screens (md)
                    'md:w-8 md:h-8': true,
                    // Large screens (lg)
                    'lg:w-9 lg:h-9': true,
                    'border': true,
                    'border-amber-700': true,
                    'flex': true,
                    'items-center': true,
                    'justify-center': true
                }">
                    <div v-if="cell.value === 'X'" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 rounded-full bg-black"
                        :title="`Player ${props.currentPlayer}'s move`">
                    </div>
                    <div v-if="cell.value === 'O'" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 rounded-full bg-white border-2 border-black"
                        :title="'Opponent\'s move'">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>