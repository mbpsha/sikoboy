<script setup>
import { onBeforeUnmount, ref, watch } from "vue";
import { router, Link } from "@inertiajs/vue3";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    DocumentTextIcon,
} from "@heroicons/vue/24/outline";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    data: Object,
    filters: Object,
    years: Array,
});

const search = ref(props.filters?.search || "");
const tahun = ref(props.filters?.tahun || "");

const showModal = ref(false);
const fileInput = ref(null);

const filter = () => {
    router.get(
        route("admin.riwayat-kerjasama.pemerintah"),
        {
            search: search.value,
            tahun: tahun.value,
        },
        { preserveState: true },
    );
};

let debounceTimer = null;

watch([search, tahun], () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        filter();
    }, 400);
});

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});

const goToPage = (page) => {
    if (!page || page === props.data?.current_page) return;

    router.get(
        route("admin.riwayat-kerjasama.pemerintah"),
        {
            search: search.value,
            tahun: tahun.value,
            page,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const form = ref({
    mitra: "",
    tahun: "",
    judul: "",
    jangka: "",
    mulai: "",
    selesai: "",
    file: null,
});

const errors = ref({});

// VALIDASI
const validate = () => {
    errors.value = {};

    if (!form.value.mitra) errors.value.mitra = "Mitra wajib diisi";
    if (!form.value.tahun) errors.value.tahun = "Tahun wajib diisi";
    if (!form.value.judul) errors.value.judul = "Judul wajib diisi";
    if (!form.value.jangka) errors.value.jangka = "Jangka waktu wajib diisi";
    if (!form.value.mulai) errors.value.mulai = "Tanggal mulai wajib diisi";
    if (!form.value.selesai)
        errors.value.selesai = "Tanggal selesai wajib diisi";
    if (!form.value.file) errors.value.file = "File wajib diupload";

    return Object.keys(errors.value).length === 0;
};

// HANDLE FILE
const handleFile = (e) => {
    form.value.file = e.target.files[0];
};

// DRAG DROP
const handleDrop = (e) => {
    const file = e.dataTransfer.files[0];
    if (file) form.value.file = file;
};

// SUBMIT
const submit = () => {
    if (!validate()) return;

    const formData = new FormData();
    const tahun = String(form.value.tahun || new Date().getFullYear());
    const judulSlug = String(form.value.judul || "KERJASAMA")
        .toUpperCase()
        .replace(/[^A-Z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "")
        .slice(0, 24);

    formData.append("mitra", form.value.mitra);
    formData.append("tahun", tahun);
    formData.append("judul", form.value.judul);
    formData.append("jangka", form.value.jangka);
    formData.append(
        "nomor_surat",
        `RIW-P/${tahun}/${judulSlug || "KERJASAMA"}`,
    );
    formData.append("urusan", "Kerjasama Daerah");
    formData.append("daerah", "Boyolali");
    formData.append("jenis_kerjasama", "Pemerintah");
    formData.append("jenis_dokumen", "PDF");
    formData.append("nama_pihak_luar", form.value.mitra);
    formData.append("tanggal_mulai", form.value.mulai);
    formData.append("tanggal_berakhir", form.value.selesai);

    if (form.value.file) {
        formData.append("file", form.value.file);
    }

    router.post(route("admin.riwayat-kerjasama.pemerintah.store"), formData, {
        preserveScroll: true,
        onSuccess: closeModal,
    });
};

// CLOSE MODAL
const closeModal = () => {
    showModal.value = false;
    form.value = {
        mitra: "",
        tahun: "",
        judul: "",
        jangka: "",
        mulai: "",
        selesai: "",
        file: null,
    };
    errors.value = {};
};
</script>

<template>
    <AdminLayout title="Riwayat Kerjasama - Boyolali">
        <div class="p-6">
            <!-- SEARCH + FILTER CARD -->
            <div
                class="bg-[#fffffff5] p-4 rounded-2xl shadow-sm border-gray-300 flex gap-3 items-center overflow-x-auto"
            >
                <!-- SEARCH -->
                <div
                    class="flex items-center gap-2 bg-white rounded-xl px-4 py-2 w-full border"
                >
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                    <input
                        v-model="search"
                        placeholder="Cari Berdasarkan Mitra atau Nama Kerjasama..."
                        class="w-full outline-none text-sm"
                    />
                </div>

                <!-- FILTER ICON -->
                <div class="bg-white rounded-xl p-1">
                    <FunnelIcon class="w-5 h-5 text-gray-500" />
                </div>

                <!-- DROPDOWN -->
                <select
                    v-model="tahun"
                    class="bg-white border rounded-xl px-4 py-2 text-sm"
                >
                    <option value="">Semua Tahun</option>
                    <option v-for="y in years" :key="y" :value="y">
                        {{ y }}
                    </option>
                </select>
            </div>

            <!-- TAB + BUTTON -->
            <div class="flex justify-between items-center mt-6">
                <!-- TAB -->
                <div
                    class="bg-white border-gray-300 rounded-xl p-1 flex gap-1 shadow-sm"
                >
                    <Link
                        :href="route('admin.riwayat-kerjasama.pemerintah')"
                        class="px-4 py-2 rounded-lg text-sm bg-teal-600 text-white"
                    >
                        Pemrakarsa Boyolali
                    </Link>

                    <Link
                        :href="route('admin.riwayat-kerjasama.mitra')"
                        class="px-4 py-2 rounded-lg text-sm text-gray-600"
                    >
                        Pemrakarsa Mitra
                    </Link>
                </div>

                <!-- BUTTON -->
                <button
                    @click="showModal = true"
                    class="bg-teal-600 text-white px-5 py-2 rounded-xl shadow hover:bg-teal-700"
                >
                    + Tambah Kerjasama
                </button>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded-2xl shadow mt-4 overflow-x-auto">
                <table class="min-w-max text-sm min-w-[900px]">
                    <thead
                        class="bg-teal-700 text-white border-b border-gray-200"
                    >
                        <tr>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                No
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                Tahun
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                Mitra
                            </th>
                            <th
                                class="px-4 py-3 text-left border-r border-gray-200"
                            >
                                Judul
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                Mulai
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                Berakhir
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                Jangka
                            </th>
                            <th
                                class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                            >
                                File
                            </th>
                            <th class="px-4 py-3 text-left whitespace-nowrap">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="item in data?.data || []"
                            :key="item.no"
                            class="border-b border-gray-200 align-middle"
                        >
                            <td
                                class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                            >
                                {{ item.no }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                            >
                                {{ item.tahun }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                            >
                                {{ item.mitra }}
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                {{ item.judul }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                            >
                                {{ item.mulai }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                            >
                                {{ item.berakhir }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap min-w-[120px] border-r border-gray-200"
                            >
                                {{ item.jangka_waktu }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap min-w-[90px] border-r border-gray-200"
                            >
                                <div
                                    v-if="item.file_url"
                                    class="flex items-center gap-1 text-teal-700"
                                >
                                    <a
                                        :href="item.file_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1 hover:underline text-xs"
                                    >
                                        <DocumentTextIcon class="w-4 h-4" />
                                        lihat
                                    </a>
                                </div>
                                <span v-else class="text-gray-400 text-xs"
                                    >-</span
                                >
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap min-w-[140px]"
                            >
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs leading-none"
                                    :class="{
                                        'bg-green-100 text-green-700':
                                            item.status === 'Aktif',
                                        'bg-red-100 text-red-600':
                                            item.status === 'Berakhir',
                                        'bg-yellow-100 text-yellow-700':
                                            item.status === 'Segera Berakhir',
                                    }"
                                >
                                    {{ item.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="(data?.last_page || 1) > 1"
                class="mt-4 flex items-center justify-end gap-2"
            >
                <button
                    class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                    :disabled="!data?.prev_page_url"
                    @click="goToPage(data.current_page - 1)"
                >
                    Sebelumnya
                </button>

                <button
                    v-for="page in data.last_page"
                    :key="page"
                    class="px-3 py-2 text-sm rounded-lg border"
                    :class="
                        page === data.current_page
                            ? 'bg-teal-600 text-white border-teal-600'
                            : 'bg-white text-gray-700'
                    "
                    @click="goToPage(page)"
                >
                    {{ page }}
                </button>

                <button
                    class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                    :disabled="!data?.next_page_url"
                    @click="goToPage(data.current_page + 1)"
                >
                    Berikutnya
                </button>
            </div>
        </div>
        <!-- MODAL TAMBAH KERJASAMA -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-2xl p-6 w-[720px] shadow-lg relative">
                <!-- CLOSE -->
                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>

                <h2 class="text-lg font-semibold mb-6">Form Input Kerjasama</h2>

                <!-- FORM -->
                <div class="space-y-4">
                    <!-- MITRA + TAHUN -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">
                                Mitra <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.mitra"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Masukkan nama mitra"
                            />
                            <p
                                v-if="errors.mitra"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.mitra }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium">
                                Tahun <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.tahun"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Tahun"
                            />
                            <p
                                v-if="errors.tahun"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.tahun }}
                            </p>
                        </div>
                    </div>

                    <!-- JUDUL -->
                    <div>
                        <label class="text-sm font-medium">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.judul"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                            placeholder="Masukkan judul kerjasama"
                        />
                        <p
                            v-if="errors.judul"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.judul }}
                        </p>
                    </div>

                    <!-- JANGKA -->
                    <div>
                        <label class="text-sm font-medium">
                            Jangka Waktu <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.jangka"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                            placeholder="Masa kerjasama"
                        />
                        <p
                            v-if="errors.jangka"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.jangka }}
                        </p>
                    </div>

                    <!-- TANGGAL -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">
                                Tanggal mulai
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.mulai"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                            />
                            <p
                                v-if="errors.mulai"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.mulai }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium">
                                Tanggal selesai
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.selesai"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                            />
                            <p
                                v-if="errors.selesai"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.selesai }}
                            </p>
                        </div>
                    </div>

                    <!-- UPLOAD -->
                    <div>
                        <label class="text-sm font-medium">
                            Dokumen Kerjasama PDF
                            <span class="text-red-500">*</span>
                        </label>

                        <div
                            class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer hover:border-teal-500 transition"
                            @dragover.prevent
                            @drop.prevent="handleDrop"
                            @click="fileInput?.click()"
                        >
                            <p class="text-gray-500">
                                Drag & Drop Dokumen (PDF)
                            </p>

                            <p class="text-sm text-gray-400 mt-1">
                                atau klik untuk memilih file
                            </p>

                            <p
                                v-if="form.file"
                                class="mt-2 text-teal-600 text-sm"
                            >
                                {{ form.file.name }}
                            </p>

                            <input
                                type="file"
                                ref="fileInput"
                                class="hidden"
                                @change="handleFile"
                            />
                        </div>

                        <p v-if="errors.file" class="text-red-500 text-xs mt-1">
                            {{ errors.file }}
                        </p>
                    </div>
                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded-lg"
                    >
                        Batal
                    </button>

                    <button
                        @click="submit"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg"
                    >
                        Simpan Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
