<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout>
        <Head title="Welcome" />

        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                        SIKOBOY
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        Sistem Informasi Kerjasama Boyolali - Platform manajemen kerjasama dan kemitraan Pemerintah Kabupaten Boyolali
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            :href="route('login.select')"
                            class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        >
                            Login
                        </Link>
                        <Link
                            :href="route('register')"
                            class="text-sm font-semibold leading-6 text-gray-900"
                        >
                            Daftar sebagai Mitra <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
