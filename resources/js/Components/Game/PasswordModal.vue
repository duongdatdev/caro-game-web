<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits, watch, ref, computed } from '@vue/runtime-core';

const props = defineProps<{
  show: boolean;
  roomId?: number;
  errors?: {
    password?: string;
  };
}>();

const emit = defineEmits(['close', 'submit']);

const form = useForm({
  password: ''
});

const submit = () => {
  emit('submit', form.password);
  form.reset();
};
</script>

<template>
  <Modal :show="show" @close="emit('close')">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        Join Room
      </h2>
      <form @submit.prevent="submit" class="mt-6">
        <div>
          <InputLabel for="password" value="Room Password" />
          <TextInput 
            id="password" 
            v-model="form.password" 
            type="password" 
            class="mt-1 block w-full"
            required 
          />
          <InputError :message="errors?.password" class="mt-2" />
        </div>
        <div class="mt-6 flex justify-end gap-4">
          <SecondaryButton type="button" @click="emit('close')">
            Cancel
          </SecondaryButton>
          <PrimaryButton type="submit">
            Join Room
          </PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>
</template>