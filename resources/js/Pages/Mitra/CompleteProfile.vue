<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    nama_perusahaan: '',
    npwp: '',
    pic: '',
    jabatan_pic: '',
    no_handphone: '',
    no_telepon: '',
    alamat: '',
    provinsi: '',
    kabupaten_kota: '',
    kecamatan: '',
    kode_pos: '',
    bidang_usaha: '',
    website: '',
});

const submit = () => {
    form.post(route('mitra.profile.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Lengkapi Profil" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Lengkapi Profil Anda</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Silakan lengkapi informasi perusahaan Anda untuk melanjutkan
                    </p>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="px-4 py-5 sm:p-6 space-y-6">
                        <!-- Company Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Perusahaan</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700">
                                        Nama Perusahaan *
                                    </label>
                                    <input
                                        id="nama_perusahaan"
                                        v-model="form.nama_perusahaan"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-300': form.errors.nama_perusahaan }"
                                    />
                                    <div v-if="form.errors.nama_perusahaan" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.nama_perusahaan }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="npwp" class="block text-sm font-medium text-gray-700">NPWP</label>
                                        <input
                                            id="npwp"
                                            v-model="form.npwp"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>

                                    <div>
                                        <label for="bidang_usaha" class="block text-sm font-medium text-gray-700">
                                            Bidang Usaha
                                        </label>
                                        <input
                                            id="bidang_usaha"
                                            v-model="form.bidang_usaha"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                    <input
                                        id="website"
                                        v-model="form.website"
                                        type="url"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Person in Charge (PIC)</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="pic" class="block text-sm font-medium text-gray-700">
                                            Nama PIC *
                                        </label>
                                        <input
                                            id="pic"
                                            v-model="form.pic"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.pic }"
                                        />
                                        <div v-if="form.errors.pic" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.pic }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="jabatan_pic" class="block text-sm font-medium text-gray-700">
                                            Jabatan PIC
                                        </label>
                                        <input
                                            id="jabatan_pic"
                                            v-model="form.jabatan_pic"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="no_handphone" class="block text-sm font-medium text-gray-700">
                                            No. Handphone *
                                        </label>
                                        <input
                                            id="no_handphone"
                                            v-model="form.no_handphone"
                                            type="tel"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.no_handphone }"
                                        />
                                        <div v-if="form.errors.no_handphone" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.no_handphone }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="no_telepon" class="block text-sm font-medium text-gray-700">
                                            No. Telepon
                                        </label>
                                        <input
                                            id="no_telepon"
                                            v-model="form.no_telepon"
                                            type="tel"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Alamat</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="alamat" class="block text-sm font-medium text-gray-700">
                                        Alamat Lengkap *
                                    </label>
                                    <textarea
                                        id="alamat"
                                        v-model="form.alamat"
                                        rows="3"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-300': form.errors.alamat }"
                                    />
                                    <div v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.alamat }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="provinsi" class="block text-sm font-medium text-gray-700">
                                            Provinsi *
                                        </label>
                                        <input
                                            id="provinsi"
                                            v-model="form.provinsi"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.provinsi }"
                                        />
                                        <div v-if="form.errors.provinsi" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.provinsi }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="kabupaten_kota" class="block text-sm font-medium text-gray-700">
                                            Kabupaten/Kota *
                                        </label>
                                        <input
                                            id="kabupaten_kota"
                                            v-model="form.kabupaten_kota"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.kabupaten_kota }"
                                        />
                                        <div v-if="form.errors.kabupaten_kota" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.kabupaten_kota }}
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="kecamatan" class="block text-sm font-medium text-gray-700">
                                            Kecamatan
                                        </label>
                                        <input
                                            id="kecamatan"
                                            v-model="form.kecamatan"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>

                                    <div>
                                        <label for="kode_pos" class="block text-sm font-medium text-gray-700">
                                            Kode Pos
                                        </label>
                                        <input
                                            id="kode_pos"
                                            v-model="form.kode_pos"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan & Lanjutkan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
