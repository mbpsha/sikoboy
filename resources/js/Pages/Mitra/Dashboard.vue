<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BuildingOfficeIcon, UserIcon, PhoneIcon, MapPinIcon } from '@heroicons/vue/24/outline';

defineProps({
    mitra: Object,
    stats: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Mitra" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-8 mb-6 shadow-lg">
                    <h1 class="text-2xl font-bold text-white">Selamat datang, {{ mitra.pic }}!</h1>
                    <p class="text-blue-100 mt-1">{{ mitra.nama_perusahaan }}</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <BuildingOfficeIcon class="h-6 w-6 text-gray-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Member Since
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.member_since }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Status Verifikasi
                                        </dt>
                                        <dd class="text-lg font-semibold"
                                            :class="stats.verification_status === 'verified' ? 'text-green-600' : 'text-yellow-600'">
                                            {{ stats.verification_status === 'verified' ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Last Login
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.last_login || 'First time' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Profil Perusahaan
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Detail informasi perusahaan Anda
                            </p>
                        </div>
                        <Link
                            :href="route('mitra.profile.edit')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Edit Profil
                        </Link>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <BuildingOfficeIcon class="h-5 w-5 mr-2" />
                                    Nama Perusahaan
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ mitra.nama_perusahaan }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <UserIcon class="h-5 w-5 mr-2" />
                                    Person in Charge (PIC)
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ mitra.pic }}
                                    <span v-if="mitra.jabatan_pic" class="text-gray-500">
                                        - {{ mitra.jabatan_pic }}
                                    </span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <PhoneIcon class="h-5 w-5 mr-2" />
                                    Kontak
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div>HP: {{ mitra.no_handphone }}</div>
                                    <div v-if="mitra.no_telepon">Telp: {{ mitra.no_telepon }}</div>
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 flex items-center">
                                    <MapPinIcon class="h-5 w-5 mr-2" />
                                    Alamat
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div>{{ mitra.alamat }}</div>
                                    <div v-if="mitra.kecamatan" class="text-gray-600">
                                        {{ mitra.kecamatan }}, {{ mitra.kabupaten_kota }}, {{ mitra.provinsi }}
                                        <span v-if="mitra.kode_pos">{{ mitra.kode_pos }}</span>
                                    </div>
                                </dd>
                            </div>
                            <div v-if="mitra.npwp" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    NPWP
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ mitra.npwp }}
                                </dd>
                            </div>
                            <div v-if="mitra.bidang_usaha" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    Bidang Usaha
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ mitra.bidang_usaha }}
                                </dd>
                            </div>
                            <div v-if="mitra.website" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">
                                    Website
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <a :href="mitra.website" target="_blank" class="text-blue-600 hover:text-blue-500">
                                        {{ mitra.website }}
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
