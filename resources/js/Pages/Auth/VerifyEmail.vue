<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { EnvelopeIcon } from '@heroicons/vue/24/outline';

const resendVerification = () => {
    router.post(route('verification.send'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success message will be shown via flash
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email" />

        <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="text-center">
                    <EnvelopeIcon class="mx-auto h-12 w-12 text-blue-600" />
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Verifikasi Email Anda
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Kami telah mengirimkan link verifikasi ke email Anda.
                    </p>
                </div>

                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-700">
                            Silakan cek email Anda dan klik link verifikasi untuk mengaktifkan akun Anda.
                        </p>
                    </div>

                    <div class="mt-6">
                        <button
                            @click="resendVerification"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Kirim Ulang Email Verifikasi
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm font-medium text-gray-600 hover:text-gray-500"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
