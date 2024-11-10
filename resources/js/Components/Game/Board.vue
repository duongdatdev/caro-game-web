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
    if (props.disabled || board.value[y][x].value !== null) return;
    emit('move', x, y);
};
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="bg-amber-100 p-4 rounded-lg shadow-lg ">
            <div v-for="(row, i) in board" :key="i" class="flex">
                <div v-for="(cell, j) in row" 
                     :key="`${i}-${j}`"
                     @click="handleClick(cell.x, cell.y)"
                     class="w-8 h-8 md:w-7 md:h-7 sm:w-4 sm:h-4 lg:w-9 lg:h-9 border border-amber-700 flex items-center justify-center cursor-pointer hover:bg-amber-200"
                >
                    <div v-if="cell.value === 'X'" class="w-6 h-6 rounded-full bg-black"></div>
                    <div v-if="cell.value === 'O'" class="w-6 h-6 rounded-full bg-white border-2 border-black"></div>
                </div>
            </div>
        </div>
    </div>
</template>