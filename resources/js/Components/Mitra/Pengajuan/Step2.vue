<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  step1Data: Object,
  kategoris: Array,
  jenisDokumen: Array,
  urusanOptions: Array,
});

const DEFAULT_URUSAN_OPTIONS = [
  'SEMUA URUSAN',
  'PENDIDIKAN',
  'KESEHATAN',
  'PEKERJAAN UMUM DAN PENATAAN RUANG',
  'PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN',
  'KETENTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT',
  'SOSIAL',
  'TENAGA KERJA',
  'PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK',
  'PANGAN',
  'PERTANAHAN',
];

const urusanOptions = props.urusanOptions?.length ? props.urusanOptions : DEFAULT_URUSAN_OPTIONS;

// Batas maksimal ukuran file dokumen yang dapat diupload
const MAX_FILE_SIZE_MB = 10;
const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

const form = useForm({
  jenis_kerjasama: '',
  jenis_dokumen: '',
  judul: '',
  nama_pihak_luar: props.step1Data?.nama_perusahaan ?? '',
  nomor_suratM: '',
  pembiayaan: '',
  urusan: '',
  tanggal_mulai: '',
  tanggal_selesai: '',
  dokumen_file: null,
});

const fileInput = ref(null);
const fileName = ref('');
const fileError = ref('');

// Validasi tipe & ukuran file, mengembalikan pesan error (string) atau null jika valid
const validateFile = (file) => {
  if (file.type !== 'application/pdf') {
    return 'Hanya file berformat PDF yang diperbolehkan.';
  }
  if (file.size > MAX_FILE_SIZE_BYTES) {
    return `Ukuran file terlalu besar (${(file.size / (1024 * 1024)).toFixed(2)} MB). Maksimal ${MAX_FILE_SIZE_MB} MB.`;
  }
  return null;
};

const handleFileSelect = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  const errorMsg = validateFile(file);
  if (errorMsg) {
    fileError.value = errorMsg;
    form.dokumen_file = null;
    fileName.value = '';
    event.target.value = ''; // reset input agar bisa pilih ulang file yang sama
    return;
  }

  fileError.value = '';
  form.dokumen_file = file;
  fileName.value = file.name;
};

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleDrop = (e) => {
  const file = e.dataTransfer?.files?.[0];
  if (!file) return;

  const errorMsg = validateFile(file);
  if (errorMsg) {
    fileError.value = errorMsg;
    return;
  }

  fileError.value = '';
  form.dokumen_file = file;
  fileName.value = file.name;
};

const submit = () => {
  // Cegah submit jika masih ada error file yang belum diperbaiki
  if (fileError.value) {
    Swal.fire({ icon: 'warning', title: 'Periksa kembali file', text: fileError.value });
    return;
  }

  form.post(route('mitra.pengajuan.store'), {
    preserveScroll: false,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Pengajuan Terkirim',
        text: 'Pengajuan kerjasama berhasil dikirim ke admin.',
        confirmButtonText: 'Oke',
        allowOutsideClick: false,
        allowEscapeKey: false,
      }).then((result) => {
        if (result.isConfirmed) {
          router.visit(route('portal-mitra'))
        }
      })
    },
    onError: () => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan. Periksa input.' })
    }
  });
};
</script>

<template>
  <div class="min-h-screen bg-[#8AB4BB] flex flex-col font-sans relative">
    <Header />

    <main class="flex-1 flex flex-col">

      <div class="max-w-5xl mx-auto w-full px-4 sm:px-6 md:px-10 pt-28 sm:pt-32 pb-8">
      </div>

      <div class="bg-[#17464E] rounded-t-[30px] sm:rounded-t-[40px] pt-12 sm:pt-16 pb-20 sm:pb-32 text-center shadow-inner">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
          <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Form Pengajuan Kerjasama</h2>
          <p class="text-xs sm:text-[15px] text-gray-200 leading-relaxed max-w-2xl mx-auto px-2">
            Mitra eksternal dapat mengajukan kerjasama kepada pemerintah<br class="hidden md:block"/>
            kabupaten boyolali melalui sistem ini
          </p>
        </div>
      </div>

      <div class="flex-1 bg-[#8AB4BB] relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

          <div class="relative -mt-20 sm:-mt-24 mb-16 sm:mb-20">

            <div class="relative flex items-center justify-center max-w-xs mx-auto mb-8 sm:mb-10">
              <div class="absolute left-0 right-0 h-0.5 bg-gray-400/50 z-0"></div>
              <div class="flex justify-between w-full relative z-10">
                <div class="flex items-center justify-center w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-[#17464E] text-white font-bold shadow-md border-4 border-[#8AB4BB] text-sm sm:text-base">1</div>
                <div class="flex items-center justify-center w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-[#17464E] text-white font-bold shadow-md border-4 border-[#8AB4BB] text-sm sm:text-base">2</div>
              </div>
            </div>

            <div class="bg-white rounded-[20px] sm:rounded-3xl p-5 sm:p-8 md:p-14 shadow-[0_15px_50px_rgba(0,0,0,0.15)]">
              <h3 class="text-lg sm:text-xl font-bold text-[#17464E] mb-6 sm:mb-10">Form Input Kerjasama</h3>

              <form @submit.prevent="submit" class="space-y-6 sm:space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 md:gap-x-10 md:gap-y-8">
                  <!-- Jenis Kerjasama -->
                  <div class="space-y-2">
                    <label for="jenis_kerjasama" class="block text-xs sm:text-sm font-bold text-[#17464E]">Jenis Kerjasama <span class="text-red-500">*</span></label>
                    <select
                      v-model="form.jenis_kerjasama"
                      id="jenis_kerjasama"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih jenis kerjasama</option>
                      <option v-for="kat in kategoris" :key="kat.id_kategori" :value="kat.nama_kategori">
                        {{ kat.nama_kategori }}
                      </option>
                    </select>
                    <p v-if="form.errors.jenis_kerjasama" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_kerjasama }}</p>
                  </div>

                  <!-- Jenis Dokumen -->
                  <div class="space-y-2">
                    <label for="jenis_dokumen" class="block text-xs sm:text-sm font-bold text-[#17464E]">Jenis Dokumen <span class="text-red-500">*</span></label>
                    <select
                      v-model="form.jenis_dokumen"
                      id="jenis_dokumen"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih jenis dokumen</option>
                      <option v-for="jenis in jenisDokumen" :key="jenis" :value="jenis">
                        {{ jenis }}
                      </option>
                    </select>
                    <p v-if="form.errors.jenis_dokumen" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_dokumen }}</p>
                  </div>

                  <!-- Judul Dokumen Perjanjian -->
                  <div class="space-y-2">
                    <label for="judul_dokumen" class="block text-xs sm:text-sm font-bold text-[#17464E]">Judul Dokumen Perjanjian <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.judul"
                      id="judul_dokumen"
                      type="text"
                      placeholder="Masukkan judul dokumen perjanjian"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.judul" class="text-red-500 text-xs mt-1">{{ form.errors.judul }}</p>
                  </div>

                  <!-- Mitra Kerjasama -->
                  <div class="space-y-2">
                    <label for="mitra_kerjasama" class="block text-xs sm:text-sm font-bold text-[#17464E]">Mitra Kerjasama <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.nama_pihak_luar"
                      id="mitra_kerjasama"
                      type="text"
                      placeholder="Nama Mitra kerjasama ke- 1"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.nama_pihak_luar" class="text-red-500 text-xs mt-1">{{ form.errors.nama_pihak_luar }}</p>
                  </div>

                  <!-- Nomor Dokumen dari Mitra -->
                  <div class="space-y-2">
                    <label for="nomor_dokumen" class="block text-xs sm:text-sm font-bold text-[#17464E]">Nomor Dokumen dari Mitra <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.nomor_suratM"
                      id="nomor_dokumen"
                      type="text"
                      placeholder="Inputkan nomor surat mitra anda"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.nomor_suratM" class="text-red-500 text-xs mt-1">{{ form.errors.nomor_suratM }}</p>
                  </div>

                  <!-- Pembiayaan -->
                  <div class="space-y-2">
                    <label for="pembayaan" class="block text-xs sm:text-sm font-bold text-[#17464E]">Pembiayaan <span class="text-orange-500">(*wajib dipilih)</span></label>
                    <select
                      v-model="form.pembiayaan"
                      id="pembiayaan"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih pembiayaan</option>
                      <option value="APBN">APBN</option>
                      <option value="APBD">APBD</option>
                      <option value="PIHAK KETIGA">PIHAK KETIGA</option>
                      <option value="PARA PIHAK">PARA PIHAK</option>
                      <option value="SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN">SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN</option>
                    </select>
                    <p v-if="form.errors.pembiayaan" class="text-red-500 text-xs mt-1">{{ form.errors.pembiayaan }}</p>
                  </div>

                  <!-- Ususan -->
                  <div class="space-y-2">
                    <label for="ususan" class="block text-xs sm:text-sm font-bold text-[#17464E]">Urusan<span class="text-red-500">*</span></label>
                    <select
                      v-model="form.urusan"
                      id="ususan"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih urusan</option>
                      <option v-for="urusan in urusanOptions" :key="urusan" :value="urusan">
                        {{ urusan }}
                      </option>
                    </select>
                    <p v-if="form.errors.urusan" class="text-red-500 text-xs mt-1">{{ form.errors.urusan }}</p>
                  </div>

                  <!-- Tanggal Mulai -->
                  <div class="space-y-2">
                    <label for="tanggal_mulai" class="block text-xs sm:text-sm font-bold text-[#17464E]">Tanggal Mulai / Penetapan <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.tanggal_mulai"
                      id="tanggal_mulai"
                      type="date"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.tanggal_mulai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_mulai }}</p>
                  </div>

                  <!-- Tanggal Selesai -->
                  <div class="space-y-2">
                    <label for="tanggal_selesai" class="block text-xs sm:text-sm font-bold text-[#17464E]">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.tanggal_selesai"
                      id="tanggal_selesai"
                      type="date"
                      class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.tanggal_selesai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_selesai }}</p>
                  </div>
                </div>

                <!-- File Upload -->
                <div class="space-y-2">
                  <label for="dokumen_file" class="block text-xs sm:text-sm font-bold text-[#17464E]">
                    Dokumen Kerjasama PDF <span class="text-red-500">*</span>
                  </label>
                  <input
                    ref="fileInput"
                    id="dokumen_file"
                    type="file"
                    accept=".pdf"
                    class="hidden"
                    @change="handleFileSelect"
                  />
                  <div
                    @click="triggerFileInput"
                    @dragover.prevent
                    @drop.prevent="handleDrop"
                    :class="[
                      'border-2 border-dashed rounded-lg sm:rounded-xl p-6 sm:p-10 text-center transition cursor-pointer',
                      fileError ? 'border-red-400 hover:border-red-500' : 'border-gray-300 hover:border-[#17464E]'
                    ]"
                  >
                    <div class="flex flex-col items-center">
                      <svg class="w-10 sm:w-14 h-10 sm:h-14 text-[#17464E] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                      </svg>
                      <p class="font-semibold text-[#17464E] mb-1 text-xs sm:text-base">Drag & Drop Dokumen Kerjasama (PDF)</p>
                      <p class="text-xs text-gray-600 mb-4">atau klik untuk memilih file &middot; Maks. {{ MAX_FILE_SIZE_MB }} MB, format PDF</p>
                      <button
                        type="button"
                        class="px-4 sm:px-6 py-2 bg-[#17464E] text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-[#0f3238] transition"
                      >
                        Pilih File
                      </button>
                      <p v-if="fileName && !fileError" class="text-xs sm:text-sm text-green-700 mt-4">✓ {{ fileName }}</p>
                    </div>
                  </div>
                  <p v-if="fileError" class="text-red-500 text-xs mt-1">{{ fileError }}</p>
                  <p v-if="form.errors.dokumen_file" class="text-red-500 text-xs mt-1">{{ form.errors.dokumen_file }}</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center pt-6 sm:pt-8">
                  <Link :href="route('portal-mitra')" class="px-8 sm:px-14 py-2 sm:py-3 bg-[#D1D5DB] text-[#4B5563] rounded-lg sm:rounded-xl font-bold hover:bg-gray-300 transition-all text-sm sm:text-base">Batal</Link>
                  <button type="submit" class="px-8 sm:px-14 py-2 sm:py-3 bg-[#336D71] text-white rounded-lg sm:rounded-xl font-bold hover:bg-[#28575a] shadow-lg transition-all text-sm sm:text-base">Simpan Pengajuan</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>