<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    role: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    role: props.role,
});

const submit = () => {
    form.post(route('login.attempt'), {
        onFinish: () => form.reset('password'),
    });
};

const roleLabel = props.role === 'admin' ? 'Admin' : 'Mitra';
const roleColor = props.role === 'admin' ? 'green' : 'blue';
</script>

<template>
    <GuestLayout>
        <Head :title="`Login ${roleLabel}`" />

        <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Login {{ roleLabel }}
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Masuk ke akun {{ roleLabel }} Anda
                    </p>
                </div>

                <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <div class="mt-1">
                                <input
                                    id="email"
                                    v-model="form.email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.email }"
                                />
                            </div>
                            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <div class="mt-1">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.password }"
                                />
                            </div>
                            <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div v-if="form.errors.role" class="text-sm text-red-600">
                            {{ form.errors.role }}
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    id="remember"
                                    v-model="form.remember"
                                    name="remember"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="remember" class="ml-2 block text-sm text-gray-900">
                                    Ingat saya
                                </label>
                            </div>

                            <div class="text-sm">
                                <Link
                                    :href="route('password.request')"
                                    class="font-medium text-blue-600 hover:text-blue-500"
                                >
                                    Lupa password?
                                </Link>
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                :class="`w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${roleColor}-600 hover:bg-${roleColor}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-${roleColor}-500 disabled:opacity-50`"
                            >
                                <span v-if="form.processing">Loading...</span>
                                <span v-else>Masuk</span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300" />
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">
                                    <Link
                                        :href="route('login.select')"
                                        class="font-medium text-gray-600 hover:text-gray-500"
                                    >
                                        ← Kembali ke pilihan peran
                                    </Link>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="role === 'mitra'" class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Belum punya akun?
                            <Link
                                :href="route('register')"
                                class="font-medium text-blue-600 hover:text-blue-500"
                            >
                                Daftar sekarang
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
