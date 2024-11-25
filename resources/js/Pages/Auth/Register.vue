<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        
        <div class="animate-fade-in">
            <h1 class="mb-6 text-center text-2xl font-bold text-gray-900 dark:text-gray-100">
                Create your account
            </h1>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="transform transition-all duration-200 hover:scale-[1.01]">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="space-y-4">
                    <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span v-if="form.processing">Creating account...</span>
                        <span v-else>Create Account</span>
                    </PrimaryButton>

                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Already have an account? 
                        <Link :href="route('login')"
                              class="font-medium text-indigo-600 transition-colors duration-200 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Log in
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