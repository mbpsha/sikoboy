<script setup>
import { computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    mitra: {
        type: Object,
        default: null,
    },
    mode: {
        type: String,
        default: 'edit',
    },
});

const isCompleteMode = computed(() => props.mode === 'complete');

const form = useForm({
    nama_perusahaan: props.mitra?.nama_perusahaan ?? '',
    no_handphone: props.mitra?.no_handphone ?? '',
    alamat: props.mitra?.alamat ?? '',
    pic: props.mitra?.pic ?? '',
});

const submit = () => {
    if (isCompleteMode.value) {
        form.post('/mitra/profile/complete');
        return;
    }

    form.put('/mitra/profile');
};

const goDashboard = () => {
    router.visit('/mitra/dashboard');
};
</script>

<template>
    <section class="relative min-h-screen overflow-hidden bg-[#e7edf1] pt-30 pb-14">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 left-[-120px] h-80 w-80 rounded-full bg-[#1a6873]/10 blur-3xl"></div>
            <div class="absolute -bottom-20 right-[-120px] h-72 w-72 rounded-full bg-[#0b4f5e]/12 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-6xl px-4 sm:px-6">
            <div class="mb-4">
                <h1 class="text-4xl font-bold text-[#0b5563]">Profil Mitra</h1>
                <p class="mt-2 text-sm text-[#355b63]">
                    Kelola informasi dan pantau status pengajuan kerjasama Anda
                </p>
            </div>

            <div class="rounded-t-2xl bg-[#0f5966] px-8 py-7 text-center text-white shadow-sm">
                <h2 class="text-3xl font-semibold tracking-tight">Form Pengajuan Kerjasama</h2>
                <p class="mx-auto mt-2 max-w-xl text-sm text-white/85">
                    Mitra eksternal dapat mengajukan kerjasama kepada pemerintah kabupaten Boyolali melalui sistem ini.
                </p>
            </div>

            <div class="rounded-b-2xl bg-white/75 px-6 py-7 shadow-sm sm:px-10">
                <div class="mx-auto mb-7 flex w-full max-w-xs items-center justify-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full text-sm font-bold"
                        :class="isCompleteMode ? 'bg-[#0f5966] text-white' : 'bg-[#0f5966] text-white'">1</span>
                    <span class="h-px w-28 bg-[#93b2b8]"></span>
                    <span class="grid h-9 w-9 place-items-center rounded-full text-sm font-bold"
                        :class="isCompleteMode ? 'bg-[#8ab0b8] text-white' : 'bg-[#0f5966] text-white'">2</span>
                </div>

                <div class="mx-auto max-w-5xl rounded-2xl border border-[#d6e1e4] bg-white px-5 py-8 shadow-[0_10px_40px_rgba(9,62,73,0.07)] sm:px-8">
                    <h3 class="text-3xl font-semibold text-[#1b2b30]">
                        Data Kelengkapan Mitra
                    </h3>

                    <form class="mt-7 grid grid-cols-1 gap-x-10 gap-y-5 md:grid-cols-2" @submit.prevent="submit">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#1f2f34]">
                                Nama Mitra <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.nama_perusahaan"
                                type="text"
                                placeholder="Nama Mitra"
                                class="w-full rounded-lg border border-[#d3dde0] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#0f5966] focus:ring-2 focus:ring-[#0f5966]/20"
                            />
                            <p v-if="form.errors.nama_perusahaan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_perusahaan }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#1f2f34]">
                                Nomor Hp <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.no_handphone"
                                type="text"
                                placeholder="Nomor Hp"
                                class="w-full rounded-lg border border-[#d3dde0] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#0f5966] focus:ring-2 focus:ring-[#0f5966]/20"
                            />
                            <p v-if="form.errors.no_handphone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.no_handphone }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#1f2f34]">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.alamat"
                                type="text"
                                placeholder="Masukkan Alamat"
                                class="w-full rounded-lg border border-[#d3dde0] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#0f5966] focus:ring-2 focus:ring-[#0f5966]/20"
                            />
                            <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">
                                {{ form.errors.alamat }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#1f2f34]">
                                PIC <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.pic"
                                type="text"
                                placeholder="Nama PIC"
                                class="w-full rounded-lg border border-[#d3dde0] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#0f5966] focus:ring-2 focus:ring-[#0f5966]/20"
                            />
                            <p v-if="form.errors.pic" class="mt-1 text-sm text-red-600">
                                {{ form.errors.pic }}
                            </p>
                        </div>

                        <div class="md:col-span-2 mt-4 flex items-center justify-center gap-4">
                            <button
                                type="button"
                                @click="goDashboard"
                                class="min-w-32 rounded-lg bg-[#d8dde0] px-6 py-2.5 text-sm font-semibold text-[#5a6164] transition hover:bg-[#c7ced2]"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="min-w-44 rounded-lg bg-[#2f7c89] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#226975] disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
