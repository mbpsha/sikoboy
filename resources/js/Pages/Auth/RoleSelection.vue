<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { BuildingOfficeIcon, UserGroupIcon } from '@heroicons/vue/24/outline';
</script>

<template>
    <GuestLayout>
        <Head title="Pilih Peran" />

        <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        SIKOBOY
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Silakan pilih peran Anda
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    <Link
                        :href="route('login', 'mitra')"
                        class="group relative w-full flex justify-center items-center py-4 px-4 border-2 border-gray-300 rounded-lg text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200"
                    >
                        <BuildingOfficeIcon class="h-8 w-8 text-blue-600 mr-4" />
                        <div class="flex-1">
                            <p class="text-base font-medium text-gray-900">Mitra / Partner</p>
                            <p class="text-sm text-gray-500">Untuk perusahaan mitra</p>
                        </div>
                    </Link>

                    <Link
                        :href="route('login', 'admin')"
                        class="group relative w-full flex justify-center items-center py-4 px-4 border-2 border-gray-300 rounded-lg text-left hover:border-green-500 hover:bg-green-50 transition-all duration-200"
                    >
                        <UserGroupIcon class="h-8 w-8 text-green-600 mr-4" />
                        <div class="flex-1">
                            <p class="text-base font-medium text-gray-900">Admin Setda</p>
                            <p class="text-sm text-gray-500">Untuk pegawai Setda Boyolali</p>
                        </div>
                    </Link>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-500">
                            Daftar sebagai mitra
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
