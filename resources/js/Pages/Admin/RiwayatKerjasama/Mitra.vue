<script setup>
import { onBeforeUnmount, ref, watch, computed } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    DocumentTextIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/24/outline";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Swal from "sweetalert2";

const page = usePage();

const isActiveTab = (path) => {
    return window.location.pathname === path
}

const props = defineProps({
    data: Object,
    filters: Object,
    years: Array,
    mitras: Array,
    jenisKerjasamaOptions: Array,
    jenisDokumenOptions: Array,
    urusanOptions: Array,
});

const search = ref(props.filters?.search || "");
const tahun = ref(props.filters?.tahun || "");

const showModal = ref(false);
const fileInput = ref(null);
const showAdendumModal = ref(false);
const showAdendumDetailModal = ref(false);
const adendumFileInput = ref(null);
const selectedKerjasama = ref(null);
const selectedAdendumKerjasama = ref(null);
const openStatusDropdown = ref(null);
const openFilterColumn = ref(null);

// Computed untuk detect apakah ada filter aktif (cek dari props yang terupdate)
const hasActiveFilter = computed(() => {
    const searchVal = (props.filters?.search || "").trim();
    const tahunVal = (props.filters?.tahun || "");
    const hasFormFilter = searchVal !== "" || tahunVal !== "";

    // Check column filters
    const hasColumnFilterActive = Object.values(columnFilters.value).some(arr => arr.length > 0);

    const isActive = hasFormFilter || hasColumnFilterActive;
    return isActive;
});

let debounceTimer = null;

const form = ref({
    id_mitra: "",
    mitra: "",
    tahun: "",
    judul: "",
    jangka: "",
    mulai: "",
    selesai: "",
    jenis_kerjasama: "",
    jenis_dokumen: "",
    tipe_pengajuan: "mitra",
    nomor_suratM: '',
    nomor_suratP: '',
    urusan: '',
    pembiayaan: '',
    file: null,
});

const mitraIdSearch = ref("");

const filteredMitraOptions = computed(() => {
    const query = String(mitraIdSearch.value || "").trim();
    const mitras = props.mitras || [];

    if (!query) return mitras;

    return mitras.filter((mitra) => String(mitra.id_mitra).includes(query));
});

const selectedMitra = computed(() => {
    if (!form.value.id_mitra) return null;
    return (props.mitras || []).find(
        (mitra) => String(mitra.id_mitra) === String(form.value.id_mitra),
    ) || null;
});

const adendumForm = ref({
    judul_adendum: "",
    keterangan_adendum: "",
    file: null,
});

const errors = ref({});
const adendumErrors = ref({});
const isSubmitting = ref(false);

// AUTO CALCULATE JANGKA WAKTU (dari tanggal mulai & selesai)
const calculateJangka = () => {
  if (form.value.mulai && form.value.selesai) {
    const startDate = new Date(form.value.mulai);
    const endDate = new Date(form.value.selesai);

    // Ensure valid dates
    if (isNaN(startDate) || isNaN(endDate) || startDate >= endDate) {
      form.value.jangka = '';
      return;
    }

    // Calculate years, months, days accurately
    let years = endDate.getFullYear() - startDate.getFullYear();
    let months = endDate.getMonth() - startDate.getMonth();
    let days = endDate.getDate() - startDate.getDate();

    // Adjust for negative days
    if (days < 0) {
      months--;
      const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
      days += prevMonth.getDate();
    }

    // Adjust for negative months
    if (months < 0) {
      years--;
      months += 12;
    }

    // Build jangka string - only show non-zero values
    const parts = [];
    if (years > 0) parts.push(`${years} tahun`);
    if (months > 0) parts.push(`${months} bulan`);
    if (days > 0) parts.push(`${days} hari`);

    form.value.jangka = parts.join(' ') || '';
  }
};

// WATCHER untuk auto-calculate jangka dari tanggal
watch(
  [() => form.value.mulai, () => form.value.selesai],
  () => {
    calculateJangka();
  }
);

const applyFilters = () => {
    console.log("✅ MITRA APPLY FILTERS - search:", search.value, "tahun:", tahun.value);
    // Auto-detect: jika ada filter, show all; jika tidak ada filter, paginasi normal
    const hasFilter = search.value.trim() !== "" || tahun.value !== "";
    const perPage = hasFilter ? 10000 : 10;

    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
            page: 1,
            per_page: perPage,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const resetAllFilters = () => {
    search.value = "";
    tahun.value = "";
    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: "",
            tahun: "",
            page: 1,
            per_page: 10, // Kembali ke paginasi normal
        },
        { preserveState: true },
    );
};

// Watch search dengan debounce
watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Watch tahun (tidak perlu debounce, langsung apply)
watch(tahun, () => {
    applyFilters();
});

// =========================
// COLUMN FILTERS
// =========================
const columnFilters = ref({
    tahun: [],
    tipe: [],
    mitra: [],
    jenis_kerjasama: [],
    status: [],
});

const toggleColumnFilter = (filterKey, value) => {
    if (columnFilters.value[filterKey].includes(value)) {
        columnFilters.value[filterKey] = columnFilters.value[filterKey].filter(v => v !== value);
    } else {
        columnFilters.value[filterKey] = [...columnFilters.value[filterKey], value];
    }

    const hasActive = Object.values(columnFilters.value).some(arr => arr.length > 0);
    const perPage = hasActive ? 10000 : 10;

    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
            page: 1,
            per_page: perPage,
        },
        { preserveState: true }
    );
};

// Clear column filter
const clearColumnFilter = (filterKey) => {
    console.log("🧹 Clear column filter:", filterKey);
    columnFilters.value[filterKey] = [];

    // Load with pagination reset
    const hasActive = Object.values(columnFilters.value).some(arr => arr.length > 0);
    const perPage = hasActive ? 10000 : 10;
    console.log("🚀 After clear - hasActive:", hasActive, "perPage:", perPage);

    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
            page: 1,
            per_page: perPage,
        },
        { preserveState: false }
    );
};

const uniqueTahun = computed(() => {
    const values = (props.data?.data || []).map(item => String(item.tahun));
    return [...new Set(values)].sort().reverse();
});

const uniqueTipe = computed(() => {
    const values = (props.data?.data || []).map(item => item.tipe);
    return [...new Set(values)].filter(Boolean).sort();
});

const uniqueMitra = computed(() => {
    const values = (props.data?.data || []).map(item => item.mitra);
    return [...new Set(values)].filter(Boolean).sort();
});

const uniqueJenisKerjasama = computed(() => {
    const values = (props.data?.data || []).map(item => item.jenis_kerjasama);
    return [...new Set(values)].filter(Boolean).sort();
});

const uniqueStatus = computed(() => {
    const values = (props.data?.data || []).map(item => item.status);
    return [...new Set(values)].filter(Boolean).sort();
});

const filteredTableData = computed(() => {
    let data = [...(props.data?.data || [])];

    // COLUMN FILTERS
    if (columnFilters.value.tahun.length > 0) {
        data = data.filter(item =>
            columnFilters.value.tahun.includes(String(item.tahun))
        );
    }

    if (columnFilters.value.tipe.length > 0) {
        data = data.filter(item =>
            columnFilters.value.tipe.includes(item.tipe)
        );
    }

    if (columnFilters.value.mitra.length > 0) {
        data = data.filter(item =>
            columnFilters.value.mitra.includes(item.mitra)
        );
    }

    if (columnFilters.value.jenis_kerjasama.length > 0) {
        data = data.filter(item =>
            columnFilters.value.jenis_kerjasama.includes(item.jenis_kerjasama)
        );
    }

    if (columnFilters.value.status.length > 0) {
        data = data.filter(item =>
            columnFilters.value.status.includes(item.status)
        );
    }

    return data;
});

// Normalize status text and return badge classes
const statusBadgeClasses = (status) => {
    const s = String(status ?? '').trim().toLowerCase();
    if (!s) return 'bg-gray-100 text-gray-600';
    if (s === 'aktif' || s === 'active') return 'bg-green-100 text-green-700';
    if (s === 'berakhir' || s === 'expired' || s === 'selesai') return 'bg-red-100 text-red-600';
    if (s.includes('segera') || s.includes('soon') || s.includes('akan')) return 'bg-yellow-100 text-yellow-700';
    return 'bg-gray-100 text-gray-600';
};

const goToPage = (page) => {
    if (!page || page === props.data?.current_page) return;

    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
            page,
            per_page: 10,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const visiblePages = computed(() => {
    const lastPage = Number(props.data?.last_page || 1);
    const currentPage = Number(props.data?.current_page || 1);

    if (lastPage <= 3) {
        return Array.from({ length: lastPage }, (_, index) => index + 1);
    }

    let startPage = Math.max(1, currentPage - 1);
    let endPage = Math.min(lastPage, currentPage + 1);

    if (startPage === 1) endPage = 3;
    if (endPage === lastPage) startPage = lastPage - 2;

    return Array.from(
        { length: endPage - startPage + 1 },
        (_, index) => startPage + index,
    );
});
const hasLeftEllipsis = computed(() => visiblePages.value.length > 0 && visiblePages.value[0] > 1);
const hasRightEllipsis = computed(() => {
    if (!visiblePages.value.length) return false;
    return visiblePages.value[visiblePages.value.length - 1] < Number(props.data?.last_page || 1);
});

// VALIDASI
const validate = () => {
    errors.value = {};

    if (!form.value.mitra) errors.value.mitra = "Nama mitra wajib diisi";
    if (!form.value.tahun) errors.value.tahun = "Tahun wajib diisi";
    if (!form.value.judul) errors.value.judul = "Judul wajib diisi";
    if (!form.value.nomor_suratM) errors.value.nomor_suratM = "Nomor surat mitra wajib diisi";
    if (!form.value.nomor_suratP) errors.value.nomor_suratP = "Nomor surat pemerintah wajib diisi";
    if (!form.value.urusan) errors.value.urusan = "Urusan wajib diisi";
    if (!form.value.mulai) errors.value.mulai = "Tanggal mulai wajib diisi";
    if (!form.value.selesai)
        errors.value.selesai = "Tanggal selesai wajib diisi";
    if (!form.value.jenis_kerjasama) errors.value.jenis_kerjasama = "Jenis kerjasama wajib diisi";
    if (!form.value.jenis_dokumen) errors.value.jenis_dokumen = "Jenis dokumen wajib diisi";
    if (!form.value.tipe_pengajuan) errors.value.tipe_pengajuan = "Tipe pengajuan wajib diisi";
    if (!form.value.pembiayaan) errors.value.pembiayaan = "Pembiayaan wajib diisi";
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
    if (!validate()) {
        Swal.fire({
            icon: "error",
            title: "Validasi Gagal",
            html:
                '<div style="text-align: left">' +
                Object.values(errors.value)
                    .map((err) => `• ${err}`)
                    .join("<br>") +
                "</div>",
            confirmButtonText: "OK",
            confirmButtonColor: "#0d9488",
        });

        return;
    }

    isSubmitting.value = true;

    const formData = new FormData();

    formData.append("mitra", form.value.mitra);
    formData.append("tahun", form.value.tahun);
    formData.append("judul", form.value.judul);
    formData.append("jangka", form.value.jangka);
    formData.append("nomor_suratM", form.value.nomor_suratM);
    formData.append("nomor_suratP", form.value.nomor_suratP);
    formData.append("urusan", form.value.urusan);
    formData.append("pembiayaan", form.value.pembiayaan);
    formData.append("daerah", "Boyolali");
    formData.append("jenis_kerjasama", form.value.jenis_kerjasama);
    formData.append("jenis_dokumen", form.value.jenis_dokumen);
    formData.append("nama_pihak_luar", form.value.mitra);
    formData.append("tanggal_mulai", form.value.mulai);
    formData.append("tanggal_berakhir", form.value.selesai);

    if (form.value.file) {
        formData.append("file", form.value.file);
    }

    router.post(
        route("admin.riwayat-kerjasama.mitra.store"),
        formData,
        {
            preserveScroll: true,

            onSuccess: () => {
                isSubmitting.value = false;

                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: "Data kerjasama mitra berhasil disimpan",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#0d9488",
                }).then(() => {
                    closeModal();

                    router.visit(
                        route("admin.riwayat-kerjasama.mitra"),
                        {
                            preserveState: false,
                        }
                    );
                });
            },

            onError: (err) => {
                isSubmitting.value = false;

                console.error("Error:", err);

                let errorHtml =
                    '<div style="text-align: left; font-size: 0.9rem;">';

                if (typeof err === "object") {
                    Object.entries(err).forEach(([key, value]) => {
                        const errorMsg = Array.isArray(value)
                            ? value[0]
                            : value;

                        errorHtml += `
                            <strong>${key}:</strong> ${errorMsg}<br>
                        `;
                    });
                } else {
                    errorHtml += String(err);
                }

                errorHtml += "</div>";

                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan Data",
                    html: errorHtml,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#0d9488",
                });

                errors.value = err;
            },
        }
    );
};

// SUBMIT ADENDUM
const submitAdendum = () => {
    if (!validateAdendum()) return;

    const formData = new FormData();
    formData.append("id_kerjasama", selectedKerjasama.value.id_kerjasama);
    formData.append("mitra", selectedKerjasama.value?.mitra || "");
    formData.append("tahun", selectedKerjasama.value?.tahun || "");
    formData.append("judul_adendum", adendumForm.value.judul_adendum);
    formData.append("keterangan_adendum", adendumForm.value.keterangan_adendum || "");

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
    mitraIdSearch.value = "";
    form.value = {
        id_mitra: "",
        mitra: "",
        tahun: "",
        judul: "",
        jangka: "",
        mulai: "",
        selesai: "",
        jenis_kerjasama: "",
        jenis_dokumen: "",
        tipe_pengajuan: "mitra",
        nomor_suratM: '',
        nomor_suratP: '',
        urusan: '',
        pembiayaan: '',
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

const closeAdendumDetailModal = () => {
    showAdendumDetailModal.value = false;
    selectedAdendumKerjasama.value = null;
};

// OPEN ADENDUM MODAL
const openAdendumModal = (item) => {
    selectedKerjasama.value = item;
    adendumForm.value.judul_adendum = item?.judul || "";
    showAdendumModal.value = true;
};

const openAdendumDetailModal = (item) => {
    selectedAdendumKerjasama.value = item;
    showAdendumDetailModal.value = true;
};

// CLOSE STATUS DROPDOWN
const closeStatusDropdown = () => {
    openStatusDropdown.value = null;
};

// TOGGLE STATUS DROPDOWN
const toggleStatusDropdown = (idKerjasama) => {
    if (openStatusDropdown.value === idKerjasama) {
        openStatusDropdown.value = null;
    } else {
        openStatusDropdown.value = idKerjasama;
    }
};

// UPDATE STATUS
const handleStatusUpdate = (idKerjasama, newStatus) => {
    Swal.fire({
        title: "Ubah Status",
        text: `Apakah Anda yakin ingin mengubah status menjadi "${newStatus}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0d9488",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, ubah status",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(
                route("admin.riwayat-kerjasama.update-status", idKerjasama),
                { status: newStatus },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        closeStatusDropdown();
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Status kerjasama berhasil diperbarui",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#0d9488",
                        }).then(() => {
                            router.visit(
                                route("admin.riwayat-kerjasama.mitra"),
                                { preserveState: true }
                            );
                        });
                    },
                    onError: (err) => {
                        console.error("Error updating status:", err);
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Gagal mengubah status kerjasama",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#0d9488",
                        });
                    },
                }
            );
        }
    });
};

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});
</script>

<template>
    <AdminLayout title="Riwayat Kerjasama - Mitra">
        <div class="p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- SEARCH -->
                <div
                    class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100"
                >
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                        <div
                            class="flex items-center gap-2 w-full min-w-0 rounded-full px-4 py-2.5 border border-gray-200 bg-gray-50 focus-within:border-teal-600 focus-within:ring-1 focus-within:ring-teal-600 transition lg:flex-1"
                        >
                            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                            <input
                                v-model="search"
                                placeholder="Cari berdasarkan tahun, nama mitra, atau judul kerjasama..."
                                class="w-full bg-transparent outline-none text-sm text-gray-700 placeholder-gray-400"
                            />
                        </div>

                        <select
                            v-model="tahun"
                            class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition min-w-[180px]"
                        >
                            <option value="">Semua Tahun</option>
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}
                            </option>
                        </select>

                            <button @click="applyFilters" class="w-full bg-teal-700 hover:bg-teal-800 text-white text-sm px-5 py-2.5 rounded-full font-medium transition sm:w-auto">
                                Filter
                            </button>

                        <button v-if="search || tahun" @click="resetAllFilters" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm px-5 py-2.5 rounded-full font-medium transition">
                            Reset
                        </button>

                        <button @click="exportSpreadsheet" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2.5 rounded-full font-medium transition whitespace-nowrap">
                            Ekspor Spreadsheet
                        </button>
                    </div>
                </div>

                <!-- TAB -->
                <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div
                        class="bg-white border-gray-300 rounded-xl p-1 flex flex-wrap gap-1 shadow-sm w-full lg:w-auto"
                    >
                        <Link
                            :href="route('admin.riwayat-kerjasama.gabungan')"
                            :class="[
                                'px-3 sm:px-4 py-2 rounded-lg text-sm transition whitespace-nowrap',
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
                                'px-3 sm:px-4 py-2 rounded-lg text-sm transition whitespace-nowrap',
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
                                'px-3 sm:px-4 py-2 rounded-lg text-sm transition whitespace-nowrap',
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
                        class="w-full sm:w-auto bg-teal-600 text-white px-5 py-2 rounded-xl shadow hover:bg-teal-700"
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
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative group cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Tahun</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'tahun' ? null : 'tahun'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- FILTER DROPDOWN TAHUN -->
                                        <div
                                            v-show="openFilterColumn === 'tahun'"
                                            class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200"
                                        >
                                            <div class="mb-2 max-h-40 overflow-y-auto">
                                                <label v-for="val in uniqueTahun" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                    <input
                                                        type="checkbox"
                                                        :checked="columnFilters.tahun.includes(val)"
                                                        @change="toggleColumnFilter('tahun', val)"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="clearColumnFilter('tahun')"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative group cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Tipe</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'tipe' ? null : 'tipe'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- FILTER DROPDOWN TIPE -->
                                        <div
                                            v-show="openFilterColumn === 'tipe'"
                                            class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200"
                                        >
                                            <div class="mb-2 max-h-40 overflow-y-auto">
                                                <label v-for="val in uniqueTipe" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                    <input
                                                        type="checkbox"
                                                        :checked="columnFilters.tipe.includes(val)"
                                                        @change="toggleColumnFilter('tipe', val)"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="clearColumnFilter('tipe')"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative group cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Mitra</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'mitra' ? null : 'mitra'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- FILTER DROPDOWN MITRA -->
                                        <div
                                            v-show="openFilterColumn === 'mitra'"
                                            class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200 max-w-xs"
                                        >
                                            <div class="mb-2 max-h-40 overflow-y-auto">
                                                <label v-for="val in uniqueMitra" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                    <input
                                                        type="checkbox"
                                                        :checked="columnFilters.mitra.includes(val)"
                                                        @change="toggleColumnFilter('mitra', val)"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="clearColumnFilter('mitra')"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Judul
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Nomor Surat Mitra
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Nomor Surat Pemerintah
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Urusan
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative group cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between gap-1">
                                            <span>Jenis Kerjasama</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'jenis_kerjasama' ? null : 'jenis_kerjasama'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- FILTER DROPDOWN JENIS KERJASAMA -->
                                        <div
                                            v-show="openFilterColumn === 'jenis_kerjasama'"
                                            class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200"
                                        >
                                            <div class="mb-2 max-h-40 overflow-y-auto">
                                                <label v-for="val in uniqueJenisKerjasama" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                    <input
                                                        type="checkbox"
                                                        :checked="columnFilters.jenis_kerjasama.includes(val)"
                                                        @change="toggleColumnFilter('jenis_kerjasama', val)"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="clearColumnFilter('jenis_kerjasama')"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Jenis Dokumen
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
                                        Sisa Waktu
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Pembiayaan
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
                                        class="px-4 py-3 text-left whitespace-nowrap relative cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Status</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'status' ? null : 'status'" class="ml-2 text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- FILTER DROPDOWN STATUS -->
                                        <div
                                            v-show="openFilterColumn === 'status'"
                                            class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200"
                                        >
                                            <div class="mb-2 max-h-40 overflow-y-auto">
                                                <label v-for="val in uniqueStatus" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                    <input
                                                        type="checkbox"
                                                        :checked="columnFilters.status.includes(val)"
                                                        @change="toggleColumnFilter('status', val)"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="clearColumnFilter('status')"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <tbody @click="closeStatusDropdown()">
                                <tr
                                    v-for="item in filteredTableData"
                                    :key="item.id_kerjasama"
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
                                                item.tipe === 'mitra'
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
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.nomor_suratM }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.nomor_suratP }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.urusan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700"
                                        >
                                            {{ item.jenis_kerjasama || '-' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.jenis_dokumen || '-' }}
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
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        <span v-if="item.days_remaining !== null" :class="{
                                            'text-green-600 font-semibold': item.days_remaining > 30,
                                            'text-orange-600 font-semibold': item.days_remaining > 0 && item.days_remaining <= 30,
                                            'text-red-600 font-semibold': item.days_remaining <= 0
                                        }">
                                            {{ item.days_remaining > 0 ? item.days_remaining + ' hari' : 'Berakhir' }}
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.pembiayaan }}
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
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm text-gray-600">
                                                {{ item.adendum_count ? `${item.adendum_count} adendum` : 'Belum ada adendum' }}
                                            </span>
                                            <button
                                                v-if="item.adendum_count"
                                                @click="openAdendumDetailModal(item)"
                                                class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 whitespace-nowrap"
                                            >
                                                Lihat
                                            </button>
                                            <button
                                                @click="openAdendumModal(item)"
                                                class="px-2 py-1 bg-teal-600 text-white rounded text-xs hover:bg-teal-700 whitespace-nowrap"
                                            >
                                                + Tambah
                                            </button>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap min-w-[140px] relative"
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs leading-none"
                                                :class="statusBadgeClasses(item.status)"
                                            >
                                                {{ item.status }}
                                            </span>
                                            <div class="relative">
                                                <button
                                                    @click.stop="toggleStatusDropdown(item.id_kerjasama)"
                                                    class="p-1 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition"
                                                >
                                                    <EllipsisVerticalIcon class="w-5 h-5" />
                                                </button>
                                                <!-- DROPDOWN STATUS MENU -->
                                                <div
                                                    v-if="openStatusDropdown === item.id_kerjasama"
                                                    class="absolute right-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 border border-gray-200 min-w-max"
                                                    @click.stop
                                                >
                                                    <button
                                                        @click.stop="handleStatusUpdate(item.id_kerjasama, 'Aktif')"
                                                        :disabled="item.status === 'Aktif'"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition first:rounded-t-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        Aktif
                                                    </button>
                                                    <button
                                                        @click.stop="handleStatusUpdate(item.id_kerjasama, 'Segera Berakhir')"
                                                        :disabled="item.status === 'Segera Berakhir'"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        Segera Berakhir
                                                    </button>
                                                    <button
                                                        @click.stop="handleStatusUpdate(item.id_kerjasama, 'Berakhir')"
                                                        :disabled="item.status === 'Berakhir'"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition last:rounded-b-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        Berakhir
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="(data?.last_page || 1) > 1 && !hasActiveFilter"
                    class="mt-4 flex items-center justify-end gap-2"
                >
                    <div class="hidden md:flex items-center justify-between">
                        <span class="text-xs text-gray-500 mr-6">Tampilkan 10 data / halaman</span>
                        <div class="flex items-center justify-end gap-2">
                            <button
                                class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                                :disabled="!data?.prev_page_url"
                                @click="goToPage(data.current_page - 1)"
                            >
                                Sebelumnya
                            </button>

                            <button
                                v-for="page in visiblePages"
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
                                Selanjutnya
                            </button>
                        </div>
                    </div>

                    <div class="flex md:hidden items-center justify-center gap-2">
                        <button
                            class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                            :disabled="!data?.prev_page_url"
                            @click="goToPage(data.current_page - 1)"
                        >
                            &lt;
                        </button>

                        <span v-if="hasLeftEllipsis" class="px-1 text-sm text-gray-600">...</span>

                        <button
                            v-for="page in visiblePages"
                            :key="`mobile-${page}`"
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

                        <span v-if="hasRightEllipsis" class="px-1 text-sm text-gray-600">...</span>

                        <button
                            class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                            :disabled="!data?.next_page_url"
                            @click="goToPage(data.current_page + 1)"
                        >
                            &gt;
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH KERJASAMA -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4 sm:px-6"
        >
            <div class="bg-white rounded-2xl p-6 w-full max-w-[750px] max-h-[85vh] shadow-lg relative flex flex-col">
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
                                v-model="mitraIdSearch"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Ketik ID mitra (contoh: 21)"
                            />
                            <select
                                v-model="form.id_mitra"
                                @change="form.mitra = selectedMitra?.nama_perusahaan || ''"
                                class="w-full border rounded-lg px-3 py-2 mt-1 bg-white"
                            >
                                <option value="">Pilih ID mitra</option>
                                <option
                                    v-for="mitraOption in filteredMitraOptions"
                                    :key="mitraOption.id_mitra"
                                    :value="String(mitraOption.id_mitra)"
                                >
                                    {{ mitraOption.id_mitra }} - {{ mitraOption.nama_perusahaan }}
                                </option>
                            </select>
                            <p
                                v-if="mitraIdSearch && filteredMitraOptions.length === 0"
                                class="text-xs text-gray-500 mt-1"
                            >
                                Data mitra tidak ditemukan untuk ID tersebut.
                            </p>
                            <p
                                v-if="errors.id_mitra"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.id_mitra }}
                            </p>
                            <input
                                v-model="form.mitra"
                                class="w-full border rounded-lg px-3 py-2 mt-2"
                                placeholder="Masukkan nama mitra"
                            />
                            <p
                                v-if="errors.mitra"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ errors.mitra }}
                            </p>
                            <div
                                v-if="selectedMitra"
                                class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-700 space-y-1"
                            >
                                <p><span class="font-semibold">Nama Perusahaan:</span> {{ selectedMitra.nama_perusahaan || '-' }}</p>
                                <p><span class="font-semibold">NPWP:</span> {{ selectedMitra.npwp || '-' }}</p>
                                <p><span class="font-semibold">PIC:</span> {{ selectedMitra.pic || '-' }}</p>
                                <p><span class="font-semibold">No. HP:</span> {{ selectedMitra.no_handphone || '-' }}</p>
                                <p><span class="font-semibold">Alamat:</span> {{ selectedMitra.alamat || '-' }}</p>
                            </div>
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

                    <!-- NOMOR SURAT MITRA -->
                    <div>
                        <label class="text-sm font-medium">
                            Nomor Surat Mitra <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.nomor_suratM"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                            placeholder="Masukkan nomor surat mitra"
                        />
                        <p
                            v-if="errors.nomor_SuratM"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.nomor_suratM }}
                        </p>
                    </div>

                    <!-- Nomor Surat Pemerintah -->
                    <div>
                        <label class="text-sm font-medium">
                            Nomor Surat Pemerintah <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.nomor_suratP"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                            placeholder="Masukkan nomor surat pemerintah"
                        />
                        <p
                            v-if="errors.nomor_suratP"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.nomor_suratP }}
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
                            <option value="">-- Pilih Jenis Kerjasama --</option>
                            <option
                                v-for="option in (jenisKerjasamaOptions || [])"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <p
                            v-if="errors.jenis_kerjasama"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.jenis_kerjasama }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium">
                            Jenis Dokumen <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.jenis_dokumen"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            <option
                                v-for="option in (jenisDokumenOptions || [])"
                                :key="option"
                                :value="option"
                            >
                                {{ option }}
                            </option>
                        </select>
                        <p
                            v-if="errors.jenis_dokumen"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.jenis_dokumen }}
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
                            <option value="mitra">Mitra</option>
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

                    <!-- JANGKA WAKTU DISPLAY (Auto-calculated from dates) -->
                    <div v-if="form.mulai && form.selesai" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <label class="text-sm font-medium text-blue-900 block mb-2">Jangka Waktu</label>
                        <p class="text-base font-semibold text-blue-900">{{ form.jangka }}</p>
                    </div>

                    <!-- PEMBIAYAAN -->
                    <div>
                        <label class="text-sm font-medium">
                            Pembiayaan <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.pembiayaan"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
                            <option value="">-- Pilih Pembiayaan --</option>
                            <option value="APBN">APBN</option>
                            <option value="APBD">APBD</option>
                            <option value="PIHAK KETIGA">PIHAK KETIGA</option>
                            <option value="PARA PIHAK">PARA PIHAK</option>
                            <option value="SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN">SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN</option>
                        </select>
                        <p
                            v-if="errors.pembiayaan"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.pembiayaan }}
                        </p>
                    </div>

                    <!-- Urusan -->
                    <div>
                        <label class="text-sm font-medium">
                            Urusan <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.urusan"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
                            <option value="">-- Pilih Urusan --</option>
                            <option
                                v-for="urusan in props.urusanOptions"
                                :key="urusan"
                                :value="urusan"
                            >
                                {{ urusan }}
                            </option>
                        </select>
                        <p
                            v-if="errors.urusan"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.urusan }}
                        </p>
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
                        :disabled="isSubmitting"
                        class="px-4 py-2 bg-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Batal
                    </button>

                    <button
                        @click="submit"
                        :disabled="isSubmitting"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <span v-if="isSubmitting" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        {{ isSubmitting ? 'Menyimpan...' : 'Simpan Pengajuan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL UPLOAD ADENDUM -->
        <div
            v-if="showAdendumModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4 sm:px-6 py-6"
        >
            <div
                class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-[600px] max-h-[90dvh] shadow-lg relative flex flex-col"
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
                    class="flex flex-col-reverse sm:flex-row gap-3 justify-end mt-4 pt-4 border-t border-gray-200"
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

        <div
            v-if="showAdendumDetailModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4 sm:px-6 py-6"
        >
            <div class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-[680px] max-h-[90dvh] shadow-lg relative flex flex-col">
                <button
                    @click="closeAdendumDetailModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>

                <h2 class="text-lg font-semibold mb-1">Data Adendum</h2>
                <p class="text-sm text-gray-500 mb-4">{{ selectedAdendumKerjasama?.judul }}</p>

                <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                    <div
                        v-for="adendum in (selectedAdendumKerjasama?.adendum_items || [])"
                        :key="adendum.id_adendum"
                        class="border border-gray-200 rounded-xl p-4 bg-gray-50"
                    >
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <h3 class="font-semibold text-sm text-gray-800">Adendum {{ adendum.urutan }}</h3>
                            <span v-if="adendum.created_at" class="text-xs text-gray-500">{{ adendum.created_at }}</span>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div><p class="text-gray-500 text-xs">Judul Adendum</p><p class="font-medium">{{ adendum.judul_adendum || '-' }}</p></div>
                            <div v-if="adendum.keterangan_adendum"><p class="text-gray-500 text-xs">Keterangan</p><p class="font-medium whitespace-pre-line">{{ adendum.keterangan_adendum }}</p></div>
                            <div v-if="adendum.file_url">
                                <a
                                    :href="adendum.file_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 text-teal-700 text-sm font-medium hover:underline"
                                >
                                    <DocumentTextIcon class="w-4 h-4" />
                                    {{ adendum.file_name || 'Lihat dokumen adendum' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
                    <button
                        @click="closeAdendumDetailModal"
                        class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
