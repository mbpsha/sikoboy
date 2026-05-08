<script setup>
import { onBeforeUnmount, ref, watch } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    DocumentTextIcon,
} from "@heroicons/vue/24/outline";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const page = usePage();

const isActiveTab = (path) => {
    return window.location.pathname === path
}

const props = defineProps({
    data: Object,
    filters: Object,
    years: Array,
});

const search = ref(props.filters?.search || "");
const tahun = ref(props.filters?.tahun || "");

const showModal = ref(false);
const fileInput = ref(null);
const showAdendumModal = ref(false);
const adendumFileInput = ref(null);
const selectedKerjasama = ref(null);

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

watch(search, (value) => {
    router.get(
        route('admin.riwayat-kerjasama.pemerintah'),
        {
            search: value,
            tahun: tahun.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
})

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
    jenis_kerjasama: "KSDD",
    tipe_pengajuan: "pemerintah",
    file: null,
});

const adendumForm = ref({
    judul_adendum: "",
    keterangan_adendum: "",
    file: null,
});

const errors = ref({});
const adendumErrors = ref({});

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
    if (!form.value.jenis_kerjasama) errors.value.jenis_kerjasama = "Jenis kerjasama wajib diisi";
    if (!form.value.tipe_pengajuan) errors.value.tipe_pengajuan = "Tipe pengajuan wajib diisi";
    if (!form.value.file) errors.value.file = "File wajib diupload";

    return Object.keys(errors.value).length === 0;
};

// VALIDASI ADENDUM
const validateAdendum = () => {
    adendumErrors.value = {};

    if (!adendumForm.value.judul_adendum)
        adendumErrors.value.judul_adendum = "Judul adendum wajib diisi";
    if (!adendumForm.value.file)
        adendumErrors.value.file = "File adendum wajib diupload";

    return Object.keys(adendumErrors.value).length === 0;
};

// HANDLE FILE
const handleFile = (e) => {
    form.value.file = e.target.files[0];
};

const handleAdendumFile = (e) => {
    adendumForm.value.file = e.target.files[0];
};

// DRAG DROP
const handleDrop = (e) => {
    const file = e.dataTransfer.files[0];
    if (file) form.value.file = file;
};

const handleAdendumDrop = (e) => {
    const file = e.dataTransfer.files[0];
    if (file) adendumForm.value.file = file;
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
    formData.append("jenis_kerjasama", form.value.jenis_kerjasama);
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
        onError: (err) => {
            console.log(err);
            errors.value = err;
        },
    });
};

// SUBMIT ADENDUM
const submitAdendum = () => {
    if (!validateAdendum()) return;

    const formData = new FormData();
    formData.append("id_kerjasama", selectedKerjasama.value.id_kerjasama);
    formData.append("judul_adendum", adendumForm.value.judul_adendum);
    formData.append("keterangan_adendum", adendumForm.value.keterangan_adendum);

    if (adendumForm.value.file) {
        formData.append("file", adendumForm.value.file);
    }

    router.post(route("admin.riwayat-kerjasama.adendum.store"), formData, {
        preserveScroll: true,
        onSuccess: closeAdendumModal,
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
        jenis_kerjasama: "KSDD",
        tipe_pengajuan: "pemerintah",
        file: null,
    };
    errors.value = {};
    if (fileInput.value) fileInput.value.value = "";
};
// CLOSE ADENDUM MODAL
const closeAdendumModal = () => {
    showAdendumModal.value = false;
    selectedKerjasama.value = null;
    adendumForm.value = {
        judul_adendum: "",
        keterangan_adendum: "",
        file: null,
    };
    adendumErrors.value = {};
};

// OPEN ADENDUM MODAL
const openAdendumModal = (item) => {
    selectedKerjasama.value = item;
    showAdendumModal.value = true;
};
</script>

<template>
    <AdminLayout title="Riwayat Kerjasama - Boyolali">
        <div class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- SEARCH + FILTER CARD -->
                <div
                    class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex gap-3 items-center overflow-x-auto"
                >
                    <!-- SEARCH -->
                    <div
                        class="flex items-center gap-2 flex-1 min-w-[220px] rounded-full px-4 py-2.5 border border-gray-200 bg-gray-50 focus-within:border-teal-600 focus-within:ring-1 focus-within:ring-teal-600 transition"
                    >
                        <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                        <input
                            v-model="search"
                            placeholder="Cari berdasarkan tahun, nama mitra, atau judul kerjasama..."
                            class="w-full bg-transparent outline-none text-sm text-gray-700 placeholder-gray-400"
                        />
                    </div>

                    <!-- DROPDOWN -->
                    <select
                        v-model="tahun"
                        class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition min-w-[180px]"
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
                            :href="route('admin.riwayat-kerjasama.gabungan')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm transition',
                                isActiveTab('/admin/riwayat-kerjasama')
                                    ? 'bg-teal-700 text-white'
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            Semua Kerjasama
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.pemerintah')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm transition',
                                isActiveTab('/admin/riwayat-kerjasama/pemerintah')
                                    ? 'bg-teal-700 text-white'
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            Pemrakarsa Boyolali
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.mitra')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm transition',
                                isActiveTab('/admin/riwayat-kerjasama/mitra')
                                    ? 'bg-teal-700 text-white'
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
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
                <div class="mt-4 bg-white rounded-2xl shadow overflow-hidden">
                    <div class="p-6 overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
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
                                        Tipe
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
                                        Jenis Kerjasama
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
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Adendum
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap"
                                    >
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
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold"
                                            :class="
                                                item.tipe === 'Mitra'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : 'bg-green-100 text-green-800'
                                            "
                                        >
                                            {{ item.tipe === 'mitra' ? 'Mitra' : 'Pemerintah' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.mitra }}
                                    </td>
                                    <td
                                        class="px-4 py-3 border-r border-gray-200"
                                    >
                                        {{ item.judul || "-" }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700"
                                        >
                                            {{ item.jenis_kerjasama || '-' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.mulai || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.berakhir || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap min-w-[120px] border-r border-gray-200"
                                    >
                                        {{ item.jangka_waktu || "-" }}
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
                                                <DocumentTextIcon
                                                    class="w-4 h-4"
                                                />
                                                lihat
                                            </a>
                                        </div>
                                        <span
                                            v-else
                                            class="text-gray-400 text-xs"
                                            >-</span
                                        >
                                    </td>
                                    <td
                                        class="px-4 py-3 border-r border-gray-200"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span
                                                v-if="!item.has_adendum"
                                                class="text-sm text-gray-500"
                                            >
                                                Belum ada adendum
                                            </span>
                                            <span
                                                v-else
                                                class="text-sm text-green-600 font-semibold"
                                            >
                                                ✓ Ada adendum
                                            </span>
                                            <button
                                                @click="openAdendumModal(item)"
                                                class="px-2 py-1 bg-teal-600 text-white rounded text-xs hover:bg-teal-700 whitespace-nowrap"
                                            >
                                                + Upload
                                            </button>
                                        </div>
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
                                                    item.status ===
                                                    'Segera Berakhir',
                                            }"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
        </div>
        <!-- MODAL TAMBAH KERJASAMA -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-2xl p-6 w-[750px] max-h-[85vh] shadow-lg relative flex flex-col">
                <!-- CLOSE -->
                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>

                <h2 class="text-lg font-semibold mb-4">Form Input Kerjasama</h2>

                <!-- FORM - SCROLLABLE -->
                <div class="overflow-y-auto flex-1 pr-2">
                    <div class="space-y-3">
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

                    <!-- JENIS KERJASAMA -->
                    <div>
                        <label class="text-sm font-medium">
                            Jenis Kerjasama <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.jenis_kerjasama"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
                            <option value="KSDD">Kerjasama Daerah Antar Daerah (KSDD)</option>
                            <option value="KSDPK">Kerjasama Dengan Pihak Ketiga (KSDPK)</option>
                            <option value="NK/RK">Sinergi Dengan Pemerintah Pusat/Lembaga (NK/RK)</option>
                            <option value="PERTEK">Perjanjian Teknis (PERTEK)</option>
                            <option value="KSDPL">Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)</option>
                            <option value="KSDLL">Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)</option>
                        </select>
                        <p
                            v-if="errors.jenis_kerjasama"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.jenis_kerjasama }}
                        </p>
                    </div>

                    <!-- TIPE PENGAJUAN -->
                    <div>
                        <label class="text-sm font-medium">
                            Tipe Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.tipe_pengajuan"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
                            <option value="pemerintah">Pemerintah</option>
                        </select>
                        <p
                            v-if="errors.tipe_pengajuan"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.tipe_pengajuan }}
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

                <!-- BUTTONS -->
                <div class="flex gap-3 justify-end mt-4 pt-4 border-t border-gray-200">
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
        <!-- MODAL UPLOAD ADENDUM -->
        <div
            v-if="showAdendumModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div
                class="bg-white rounded-2xl p-6 w-[600px] max-h-[85vh] shadow-lg relative flex flex-col"
            >
                <!-- CLOSE -->
                <button
                    @click="closeAdendumModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>

                <h2 class="text-lg font-semibold mb-4">Upload Adendum</h2>

                <!-- FORM - SCROLLABLE -->
                <div class="overflow-y-auto flex-1 pr-2">
                    <div class="space-y-4">
                        <!-- JUDUL ADENDUM -->
                        <div>
                            <label class="text-sm font-medium">
                                Judul Adendum
                            </label>
                            <input
                                v-model="adendumForm.judul_adendum"
                                type="text"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Masukkan judul adendum"
                            />
                            <p
                                v-if="adendumErrors.judul_adendum"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ adendumErrors.judul_adendum }}
                            </p>
                        </div>

                        <!-- KETERANGAN ADENDUM -->
                        <div>
                            <label class="text-sm font-medium">
                                Keterangan (Opsional)
                            </label>
                            <textarea
                                v-model="adendumForm.keterangan_adendum"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Masukkan keterangan adendum"
                                rows="4"
                            ></textarea>
                        </div>

                        <!-- FILE UPLOAD -->
                        <div>
                            <label class="text-sm font-medium">
                                File Dokumen Adendum
                            </label>
                            <div
                                @drop.prevent="handleAdendumDrop"
                                @dragover.prevent
                                class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer mt-1 hover:bg-gray-50"
                            >
                                <input
                                    ref="adendumFileInput"
                                    type="file"
                                    accept=".pdf"
                                    @change="handleAdendumFile"
                                    class="hidden"
                                />
                                <button
                                    type="button"
                                    @click="adendumFileInput?.click()"
                                    class="text-teal-600 hover:text-teal-700 font-semibold"
                                >
                                    Pilih File atau Drag & Drop
                                </button>
                                <p class="text-xs text-gray-500 mt-2">
                                    Format: PDF
                                </p>
                            </div>
                            <p
                                v-if="adendumForm.file"
                                class="text-green-600 text-xs mt-2"
                            >
                                ✓ {{ adendumForm.file.name }}
                            </p>
                            <p
                                v-if="adendumErrors.file"
                                class="text-red-500 text-xs mt-2"
                            >
                                {{ adendumErrors.file }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div
                    class="flex gap-3 justify-end mt-4 pt-4 border-t border-gray-200"
                >
                    <button
                        @click="closeAdendumModal"
                        class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitAdendum"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700"
                    >
                        Upload
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
