<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="animate-fade-in">
            <h1 class="mb-6 text-center text-2xl font-bold text-gray-900 dark:text-gray-100">
                Welcome Back!
            </h1>

            <div v-if="status" 
                 class="mb-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-600 dark:bg-green-900/50 dark:text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
                        autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                        autocomplete="current-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                    </label>

                    <Link v-if="canResetPassword" 
                          :href="route('password.request')"
                          class="text-sm text-indigo-600 transition-colors duration-200 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Forgot password?
                    </Link>
                </div>

                <div class="space-y-4">
                    <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span v-if="form.processing">Logging in...</span>
                        <span v-else>Log in</span>
                    </PrimaryButton>

                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account? 
                        <Link :href="route('register')"
                              class="font-medium text-indigo-600 transition-colors duration-200 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Sign up
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </GuestLayout>
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
