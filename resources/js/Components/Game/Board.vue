<script setup lang="ts">
interface Square {
    x: number;
    y: number;
    value: null | 'X' | 'O';
}

const props = withDefaults(defineProps<{
    size?: number;
}>(), {
    size: 15
});

const board = Array(props.size).fill(null).map((_, i) => 
    Array(props.size).fill(null).map((_, j) => ({
        x: i,
        y: j,
        value: null
    }))
);
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="bg-amber-100 p-4 rounded-lg shadow-lg">
            <div v-for="(row, i) in board" :key="i" class="flex">
                <div v-for="(cell, j) in row" 
                     :key="`${i}-${j}`"
                     class="w-8 h-8 border border-amber-700 flex items-center justify-center cursor-pointer hover:bg-amber-200"
                >
                    <div v-if="cell.value === 'X'" class="w-6 h-6 rounded-full bg-black"></div>
                    <div v-if="cell.value === 'O'" class="w-6 h-6 rounded-full bg-white border-2 border-black"></div>
                </div>
            </div>
        </div>
    </div>
</template>