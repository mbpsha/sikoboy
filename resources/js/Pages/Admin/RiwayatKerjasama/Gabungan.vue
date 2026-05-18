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
});

const search = ref(props.filters?.search || "");
const tahun = ref(props.filters?.tahun || "");

const showModal = ref(false);
const fileInput = ref(null);
const showAdendumModal = ref(false);
const adendumFileInput = ref(null);
const selectedKerjasama = ref(null);
const openStatusDropdown = ref(null);
const openFilterColumn = ref(null);

let debounceTimer = null;

const applyFilters = () => {
    console.log("✅ APPLY FILTERS - search:", search.value, "tahun:", tahun.value);
    router.get(
        route("admin.riwayat-kerjasama.gabungan"),
        {
            search: search.value,
            tahun: tahun.value,
        },
        { preserveState: true },
    );
};

const resetAllFilters = () => {
    search.value = "";
    tahun.value = "";
    applyFilters();
};

const filter = () => {
    console.log("🔍 GABUNGAN FILTER CALLED - search:", search.value, "tahun:", tahun.value);
    router.get(
        route("admin.riwayat-kerjasama.gabungan"),
        {
            search: search.value,
            tahun: tahun.value,
        },
        {
            preserveState: false,
            preserveScroll: false
        },
    );
};

// Watch search with debounce
watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
});

const goToPage = (page) => {
    if (!page || page === props.data?.current_page) return;

    router.get(
        route("admin.riwayat-kerjasama.gabungan"),
        {
            search: search.value,
            tahun: tahun.value,
            page,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const form = ref({
    id_mitra: "",
    mitra: "",
    tahun: "",
    judul_adendum: "",
    jangka: "",
    mulai: "",
    selesai: "",
    jenis_kerjasama: "KSDD",
    jenis_dokumen: "KSB",
    tipe_pengajuan: "mitra",
    nomor_suratM: '',
    nomor_suratP: '',
    urusan: '',
    pembiayaan: '',
    file: null,
});

const jenisKerjasamaOptions = [
    { value: "KSDD", label: "Kerjasama Daerah Antar Daerah (KSDD)" },
    { value: "KSDPK", label: "Kerjasama Dengan Pihak Ketiga (KSDPK)" },
    { value: "NK/RK", label: "Sinergi Dengan Pemerintah Pusat/Lembaga (NK/RK)" },
    { value: "PERTEK", label: "Perjanjian Teknis (PERTEK)" },
    { value: "KSDPL", label: "Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)" },
    { value: "KSDLL", label: "Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)" },
];

const pembiayaanOptions = [
    "APBN",
    "APBD",
    "PIHAK KETIGA",
    "PARA PIHAK",
    "SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN",
];

const adendumForm = ref({
    mitra: "",
    tahun: "",
    judul_adendum: "",
    nomor_suratM_baru: "",
    nomor_suratP_baru: "",
    nomor_suratM_lama: "",
    nomor_suratP_lama: "",
    urusan: "",
    jangka: "",
    mulai: "",
    selesai: "",
    jenis_kerjasama: "KSDD",
    pembiayaan: "",
    file: null,
});

const errors = ref({});
const adendumErrors = ref({});
const isSubmitting = ref(false);
const showMitraSuggestions = ref(false);
let hideMitraSuggestionsTimer = null;

const filteredMitraOptions = computed(() => {
    const query = String(form.value.mitra ?? "").trim().toLowerCase();

    if (!query) return [];

    return (props.mitras || [])
        .filter((mitraOption) => {
            const idMitra = String(mitraOption.id_mitra ?? "").toLowerCase();
            const namaPerusahaan = String(mitraOption.nama_perusahaan ?? "").toLowerCase();

            return idMitra.includes(query) || namaPerusahaan.includes(query);
        })
        .slice(0, 10);
});

const selectMitra = (mitraOption) => {
    form.value.id_mitra = String(mitraOption.id_mitra);
    form.value.mitra = mitraOption.nama_perusahaan || "";
    showMitraSuggestions.value = false;
};

const handleMitraFocus = () => {
    if (hideMitraSuggestionsTimer) {
        clearTimeout(hideMitraSuggestionsTimer);
        hideMitraSuggestionsTimer = null;
    }
    showMitraSuggestions.value = true;
};

const handleMitraBlur = () => {
    hideMitraSuggestionsTimer = setTimeout(() => {
        showMitraSuggestions.value = false;
    }, 120);
};

watch(
    () => form.value.mitra,
    (value) => {
        const keyword = String(value ?? "").trim().toLowerCase();

        if (!keyword) {
            form.value.id_mitra = "";
            return;
        }

        const exactMatch = (props.mitras || []).find(
            (mitraOption) =>
                String(mitraOption.nama_perusahaan ?? "").trim().toLowerCase() === keyword,
        );

        form.value.id_mitra = exactMatch ? String(exactMatch.id_mitra) : "";
    },
);

const parseJangkaToYears = (value) => {
    const match = String(value ?? "").match(/(\d+)/);
    if (!match) return null;

    const years = Number.parseInt(match[1], 10);

    return Number.isNaN(years) || years <= 0 ? null : years;
};

// AUTO CALCULATE TANGGAL SELESAI
const calculateEndDate = () => {
  if (form.value.mulai && form.value.jangka) {
    const startDate = new Date(form.value.mulai);
    const years = parseJangkaToYears(form.value.jangka);

    if (years !== null) {
      const endDate = new Date(startDate);
      endDate.setFullYear(endDate.getFullYear() + years);

      // Format ke YYYY-MM-DD
      const year = endDate.getFullYear();
      const month = String(endDate.getMonth() + 1).padStart(2, '0');
      const day = String(endDate.getDate()).padStart(2, '0');

      form.value.selesai = `${year}-${month}-${day}`;
    }
  }
};

// WATCHER untuk auto-calculate
watch(
  [() => form.value.mulai, () => form.value.jangka],
  () => {
    calculateEndDate();
  }
);

const calculateAdendumEndDate = () => {
    if (adendumForm.value.mulai && adendumForm.value.jangka) {
        const startDate = new Date(adendumForm.value.mulai);
        const years = parseJangkaToYears(adendumForm.value.jangka);

        if (years !== null) {
            const endDate = new Date(startDate);
            endDate.setFullYear(endDate.getFullYear() + years);

            const year = endDate.getFullYear();
            const month = String(endDate.getMonth() + 1).padStart(2, "0");
            const day = String(endDate.getDate()).padStart(2, "0");

            adendumForm.value.selesai = `${year}-${month}-${day}`;
        }
    }
};

watch(
    [() => adendumForm.value.mulai, () => adendumForm.value.jangka],
    () => {
        calculateAdendumEndDate();
    }
);

// VALIDASI
const validate = () => {
    errors.value = {};

    if (!form.value.mitra) errors.value.mitra = "Mitra wajib diisi";
    if (!form.value.tahun) errors.value.tahun = "Tahun wajib diisi";
    if (!form.value.judul) errors.value.judul = "Judul wajib diisi";
    if (!form.value.nomor_suratM) errors.value.nomor_suratM = "Nomor surat mitra wajib diisi";
    if (!form.value.nomor_suratP) errors.value.nomor_suratP = "Nomor surat pemerintah wajib diisi";
    if (!form.value.urusan) errors.value.urusan = "Urusan wajib diisi";
    if (!form.value.jangka) errors.value.jangka = "Jangka waktu wajib diisi";
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

    if (!adendumForm.value.mitra)
        adendumErrors.value.mitra = "Mitra wajib diisi";
    if (!adendumForm.value.tahun)
        adendumErrors.value.tahun = "Tahun wajib diisi";
    if (!adendumForm.value.judul_adendum)
        adendumErrors.value.judul_adendum = "Judul adendum wajib diisi";
    if (!adendumForm.value.nomor_suratM_baru)
        adendumErrors.value.nomor_suratM_baru = "Nomor surat mitra baru wajib diisi";
    if (!adendumForm.value.nomor_suratP_baru)
        adendumErrors.value.nomor_suratP_baru = "Nomor surat pemerintah baru wajib diisi";
    if (!adendumForm.value.urusan)
        adendumErrors.value.urusan = "Urusan wajib diisi";
    if (!adendumForm.value.jangka)
        adendumErrors.value.jangka = "Jangka waktu wajib diisi";
    if (!adendumForm.value.mulai)
        adendumErrors.value.mulai = "Tanggal mulai wajib diisi";
    if (!adendumForm.value.selesai)
        adendumErrors.value.selesai = "Tanggal berakhir wajib diisi";
    if (!adendumForm.value.jenis_kerjasama)
        adendumErrors.value.jenis_kerjasama = "Jenis kerjasama wajib diisi";
    if (!adendumForm.value.pembiayaan)
        adendumErrors.value.pembiayaan = "Pembiayaan wajib diisi";
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
    formData.append("tipe_pengajuan", form.value.tipe_pengajuan);
    formData.append("jenis_dokumen", form.value.jenis_dokumen);
    formData.append("nama_pihak_luar", form.value.mitra);
    formData.append("tanggal_mulai", form.value.mulai);
    formData.append("tanggal_berakhir", form.value.selesai);

    if (form.value.file) {
        formData.append("file", form.value.file);
    }

    router.post(
        route("admin.riwayat-kerjasama.gabungan.store"),
        formData,
        {
            preserveScroll: true,

            onSuccess: () => {
                isSubmitting.value = false;

                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: "Data kerjasama berhasil disimpan",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#0d9488",
                }).then(() => {
                    closeModal();

                    // Redirect to last page to see the newly created kerjasama
                    const lastPage = props.data?.last_page || 1;
                    router.visit(
                        route("admin.riwayat-kerjasama.gabungan", { page: lastPage }),
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

    const currentPage = props.data?.current_page || 1;
    const formData = new FormData();
    formData.append("id_kerjasama", selectedKerjasama.value.id_kerjasama);
    formData.append("mitra", adendumForm.value.mitra);
    formData.append("tahun", adendumForm.value.tahun);
    formData.append("judul_adendum", adendumForm.value.judul_adendum);
    formData.append("keterangan_adendum", JSON.stringify({
        nomor_surat_mitra_lama: adendumForm.value.nomor_suratM_lama,
        nomor_surat_mitra_baru: adendumForm.value.nomor_suratM_baru,
        nomor_surat_pemerintah_lama: adendumForm.value.nomor_suratP_lama,
        nomor_surat_pemerintah_baru: adendumForm.value.nomor_suratP_baru,
        urusan: adendumForm.value.urusan,
        jangka_waktu: adendumForm.value.jangka,
        tanggal_mulai: adendumForm.value.mulai,
        tanggal_berakhir: adendumForm.value.selesai,
        jenis_kerjasama: adendumForm.value.jenis_kerjasama,
        pembiayaan: adendumForm.value.pembiayaan,
    }));

    if (adendumForm.value.file) {
        formData.append("file", adendumForm.value.file);
    }

    router.post(route("admin.riwayat-kerjasama.adendum.store"), formData, {
        preserveScroll: true,
        onSuccess: () => {
            closeAdendumModal();
            // Refresh to same page after adding adendum
            router.visit(
                route("admin.riwayat-kerjasama.gabungan", { page: currentPage }),
                { preserveState: true }
            );
        },
    });
};

// CLOSE MODAL
const closeModal = () => {
    showModal.value = false;
    showMitraSuggestions.value = false;
    form.value = {
        id_mitra: "",
        mitra: "",
        tahun: "",
        judul_adendum: "",
        jangka: "",
        mulai: "",
        selesai: "",
        jenis_kerjasama: "KSDD",
        jenis_dokumen: "KSB",
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
        mitra: "",
        tahun: "",
        judul_adendum: "",
        nomor_suratM_baru: "",
        nomor_suratP_baru: "",
        nomor_suratM_lama: "",
        nomor_suratP_lama: "",
        urusan: "",
        jangka: "",
        mulai: "",
        selesai: "",
        jenis_kerjasama: "KSDD",
        pembiayaan: "",
        file: null,
    };
    adendumErrors.value = {};
};

// OPEN ADENDUM MODAL
const openAdendumModal = (item) => {
    selectedKerjasama.value = item;
    adendumForm.value = {
        judul_adendum: item?.judul || "",
        nomor_suratM_baru: "",
        nomor_suratP_baru: "",
        nomor_suratM_lama: item?.nomor_suratM || "",
        nomor_suratP_lama: item?.nomor_suratP || "",
        urusan: item?.urusan || "",
        jangka: item?.jangka_waktu || "",
        mulai: item?.tanggal_mulai || "",
        selesai: item?.tanggal_berakhir || "",
        jenis_kerjasama: item?.jenis_kerjasama || "KSDD",
        pembiayaan: item?.pembiayaan || "",
        file: null,
    };
    showAdendumModal.value = true;
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
    const currentPage = props.data?.current_page || 1;

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
                                route("admin.riwayat-kerjasama.gabungan", { page: currentPage }),
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

// Normalize status text and return badge classes
const statusBadgeClasses = (status) => {
    const s = String(status ?? '').trim().toLowerCase();
    if (!s) return 'bg-gray-100 text-gray-600';
    if (s === 'aktif' || s === 'active') return 'bg-green-100 text-green-700';
    if (s === 'berakhir' || s === 'expired' || s === 'selesai') return 'bg-red-100 text-red-600';
    if (s.includes('segera') || s.includes('soon') || s.includes('akan')) return 'bg-yellow-100 text-yellow-700';
    return 'bg-gray-100 text-gray-600';
};

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
    if (hideMitraSuggestionsTimer) clearTimeout(hideMitraSuggestionsTimer);
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
</script>

<template>
    <AdminLayout title="Riwayat Kerjasama - Semua Kerjasama">
        <div class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- SEARCH -->
                <div
                    class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100"
                >
                    <div class="flex gap-3 items-center overflow-x-auto mb-3">
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

                        <select
                            v-model="tahun"
                            @change="applyFilters"
                            class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition min-w-[180px]"
                        >
                            <option value="">Semua Tahun</option>
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}
                            </option>
                        </select>

                        <button @click="applyFilters" class="bg-teal-700 hover:bg-teal-800 text-white text-sm px-5 py-2.5 rounded-full font-medium transition">
                            Filter
                        </button>

                        <button v-if="search || tahun" @click="resetAllFilters" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm px-5 py-2.5 rounded-full font-medium transition">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- TAB + BUTTON -->
                <div class="flex justify-between items-center mt-6">
                    <div
                        class="bg-white border-gray-300 rounded-xl p-1 flex gap-1 shadow-sm"
                    >
                        <Link
                            :href="route('admin.riwayat-kerjasama.gabungan')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm transition',
                                isActiveTab('/admin/riwayat-kerjasama/gabungan')
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
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Tahun</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'tahun' ? null : 'tahun'" class="ml-2 text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
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
                                                        @change="(e) => {
                                                            if (e.target.checked) {
                                                                columnFilters.tahun.push(val)
                                                            } else {
                                                                columnFilters.tahun = columnFilters.tahun.filter(v => v !== val)
                                                            }
                                                        }"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="columnFilters.tahun = []"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Tipe</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'tipe' ? null : 'tipe'" class="ml-2 text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
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
                                                        @change="(e) => {
                                                            if (e.target.checked) {
                                                                columnFilters.tipe.push(val)
                                                            } else {
                                                                columnFilters.tipe = columnFilters.tipe.filter(v => v !== val)
                                                            }
                                                        }"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="columnFilters.tipe = []"
                                                class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Mitra</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'mitra' ? null : 'mitra'" class="ml-2 text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
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
                                                        @change="(e) => {
                                                            if (e.target.checked) {
                                                                columnFilters.mitra.push(val)
                                                            } else {
                                                                columnFilters.mitra = columnFilters.mitra.filter(v => v !== val)
                                                            }
                                                        }"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="columnFilters.mitra = []"
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
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200 relative cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Jenis Kerjasama</span>
                                            <button @click.stop="openFilterColumn = openFilterColumn === 'jenis_kerjasama' ? null : 'jenis_kerjasama'" class="ml-2 text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
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
                                                        @change="(e) => {
                                                            if (e.target.checked) {
                                                                columnFilters.jenis_kerjasama.push(val)
                                                            } else {
                                                                columnFilters.jenis_kerjasama = columnFilters.jenis_kerjasama.filter(v => v !== val)
                                                            }
                                                        }"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="columnFilters.jenis_kerjasama = []"
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
                                                        @change="(e) => {
                                                            if (e.target.checked) {
                                                                columnFilters.status.push(val)
                                                            } else {
                                                                columnFilters.status = columnFilters.status.filter(v => v !== val)
                                                            }
                                                        }"
                                                        class="cursor-pointer"
                                                    />
                                                    <span class="text-xs">{{ val }}</span>
                                                </label>
                                            </div>
                                            <button
                                                @click="columnFilters.status = []"
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
                                        {{ item.id_kerjasama }}
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
                                        {{ item.judul }}
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
                                        {{ item.mulai }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.berakhir }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.jangka_waktu }}
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
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition first:rounded-t-lg"
                                                    >
                                                        Aktif
                                                    </button>
                                                    <button
                                                        @click.stop="handleStatusUpdate(item.id_kerjasama, 'Segera Berakhir')"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition"
                                                    >
                                                        Segera Berakhir
                                                    </button>
                                                    <button
                                                        @click.stop="handleStatusUpdate(item.id_kerjasama, 'Berakhir')"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition last:rounded-b-lg"
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
                                ? 'bg-teal-600 text-white'
                                : 'bg-white'
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
                                @focus="handleMitraFocus"
                                @blur="handleMitraBlur"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                                placeholder="Ketik nama atau ID mitra"
                            />
                            <div
                                v-if="showMitraSuggestions && form.mitra && filteredMitraOptions.length > 0"
                                class="mt-1 max-h-44 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-sm"
                            >
                                <button
                                    v-for="mitraOption in filteredMitraOptions"
                                    :key="mitraOption.id_mitra"
                                    type="button"
                                    class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50"
                                    @mousedown.prevent="selectMitra(mitraOption)"
                                >
                                    {{ mitraOption.id_mitra }} - {{ mitraOption.nama_perusahaan }}
                                </button>
                            </div>
                            <p
                                v-if="showMitraSuggestions && form.mitra && filteredMitraOptions.length === 0"
                                class="text-xs text-gray-500 mt-1"
                            >
                                Data mitra tidak ditemukan.
                            </p>
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
                            v-if="errors.nomor_suratM"
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
                            v-if="errors.nomor_SuratP"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.nomor_suratP }}
                        </p>
                    </div>

                    <!-- Urusan -->
                    <div>
                        <label class="text-sm font-medium">
                            Urusan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="form.urusan"
                            rows="3"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                            placeholder="Masukkan urusan kerjasama"
                        ></textarea>
                        <p
                            v-if="errors.urusan"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ errors.urusan }}
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

                    <!-- PEMBIAYAAN -->
                    <div>
                        <label class="text-sm font-medium">
                            Pembiayaan <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.pembiayaan"
                            class="w-full border rounded-lg px-3 py-2 mt-1"
                        >
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
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div
                class="bg-white rounded-2xl p-6 w-[760px] max-h-[85vh] shadow-lg relative flex flex-col"
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

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">
                                    Nomor Surat Mitra baru <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="adendumForm.nomor_suratM_baru"
                                    type="text"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                    placeholder="Masukkan nomor surat mitra baru"
                                />
                                <p v-if="adendumErrors.nomor_suratM_baru" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.nomor_suratM_baru }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">
                                    Nomor Surat Pemerintah baru <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="adendumForm.nomor_suratP_baru"
                                    type="text"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                    placeholder="Masukkan nomor surat pemerintah baru"
                                />
                                <p v-if="adendumErrors.nomor_suratP_baru" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.nomor_suratP_baru }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">
                                    Nomor Surat Mitra lama
                                </label>
                                <input
                                    v-model="adendumForm.nomor_suratM_lama"
                                    type="text"
                                    class="w-full border rounded-lg px-3 py-2 mt-1 bg-gray-100"
                                    readonly
                                />
                            </div>
                            <div>
                                <label class="text-sm font-medium">
                                    Nomor Surat Pemerintah lama
                                </label>
                                <input
                                    v-model="adendumForm.nomor_suratP_lama"
                                    type="text"
                                    class="w-full border rounded-lg px-3 py-2 mt-1 bg-gray-100"
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">
                                    Urusan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="adendumForm.urusan"
                                    rows="3"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                    placeholder="Masukkan urusan kerjasama"
                                ></textarea>
                                <p v-if="adendumErrors.urusan" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.urusan }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">
                                    Jangka Waktu <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="adendumForm.jangka"
                                    type="text"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                    placeholder="Contoh: 2 Tahun"
                                />
                                <p v-if="adendumErrors.jangka" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.jangka }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">
                                    Jenis Kerjasama <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="adendumForm.jenis_kerjasama"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                >
                                    <option
                                        v-for="option in jenisKerjasamaOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <p v-if="adendumErrors.jenis_kerjasama" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.jenis_kerjasama }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    v-model="adendumForm.mulai"
                                    class="w-full border rounded-lg px-3 py-2 mt-1"
                                />
                                <p v-if="adendumErrors.mulai" class="text-red-500 text-xs mt-1">
                                    {{ adendumErrors.mulai }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">
                                Tanggal Berakhir <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="adendumForm.selesai"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                            />
                            <p v-if="adendumErrors.selesai" class="text-red-500 text-xs mt-1">
                                {{ adendumErrors.selesai }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium">
                                Pembiayaan <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="adendumForm.pembiayaan"
                                class="w-full border rounded-lg px-3 py-2 mt-1"
                            >
                                <option v-for="option in pembiayaanOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                            <p v-if="adendumErrors.pembiayaan" class="text-red-500 text-xs mt-1">
                                {{ adendumErrors.pembiayaan }}
                            </p>
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
