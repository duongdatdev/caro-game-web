<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from '@vue/runtime-core';

const props = defineProps({
  show: { type: Boolean, required: true },
});

const emit = defineEmits(['close']);

const form = useForm({
  name: '',
  description: '',
  password: '',
  has_password: false,
});

const createRoom = () => {
  form.post(route('rooms.store'), {
    onSuccess: () => {
      emit('close');
      form.reset();
    },
  });
};
</script>

<template>
  <Modal :show="show" @close="emit('close')">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        Create New Room
      </h2>

      <form @submit.prevent="createRoom" class="mt-6">
        <div>
          <InputLabel for="name" value="Room Name" />
          <TextInput
            id="name"
            v-model="form.name"
            type="text"
            class="mt-1 block w-full"
            required
          />
          <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div class="mt-6">
          <InputLabel for="description" value="Description (Optional)" />
          <TextInput
            id="description"
            v-model="form.description"
            type="text"
            class="mt-1 block w-full"
          />
        </div>

        <div class="mt-6">
          <div class="flex items-center">
            <input
              type="checkbox"
              id="has_password"
              v-model="form.has_password"
              class="rounded border-gray-300 dark:border-gray-700"
            />
            <label for="has_password" class="ml-2">
              Password protect this room
            </label>
          </div>

          <div v-if="form.has_password" class="mt-4">
            <InputLabel for="password" value="Room Password" />
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full"
            />
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
</template>