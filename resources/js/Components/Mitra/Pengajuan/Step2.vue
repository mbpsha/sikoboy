<script setup>
import { ref } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const page = usePage();
const props = defineProps({
  step1Data: Object,
  kategoris: Array,
});

const form = useForm({
  jenis_kerjasama: '',
  jenis_dokumen: '',
  judul_dokumen: '',
  mitra_kerjasama: '',
  nomor_dokumen: '',
  pembayaan: '',
  ususan: '',
  tanggal_mulai: '',
  tanggal_selesai: '',
  dokumen_file: null,
});

const fileInput = ref(null);
const fileName = ref('');

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.dokumen_file = file;
    fileName.value = file.name;
  }
};

const triggerFileInput = () => {
  fileInput.value.click();
};

const submit = () => {
  form.post(route('mitra.pengajuan.store'), {
    onError: () => {
      // Form errors handled by inertia
    },
  });
};
</script>

<template>
  <div class="min-h-screen bg-[#8AB4BB] flex flex-col font-sans relative">
    <Header />

    <main class="flex-1 flex flex-col">
      
      <div class="max-w-5xl mx-auto w-full px-10 pt-32 pb-8">
        <h1 class="text-4xl font-bold text-[#17464E]">Profil Mitra</h1>
        <p class="text-sm text-[#17464E]/80 mt-2">Kelola informasi dan pantau status pengajuan kerjasama Anda</p>
      </div>

      <div class="bg-[#17464E] rounded-t-[40px] pt-16 pb-32 text-center shadow-inner">
        <div class="max-w-5xl mx-auto px-6">
          <h2 class="text-3xl font-bold text-white mb-3">Form Pengajuan Kerjasama</h2>
          <p class="text-[15px] text-gray-200 leading-relaxed max-w-2xl mx-auto">
            Mitra eksternal dapat mengajukan kerjasama kepada pemerintah<br class="hidden md:block"/>
            kabupaten boyolali melalui sistem ini
          </p>
        </div>
      </div>

      <div class="flex-1 bg-[#8AB4BB] relative">
        <div class="max-w-5xl mx-auto px-4">
          
          <div class="relative -mt-24 mb-20">
            
            <div class="relative flex items-center justify-center max-w-xs mx-auto mb-10">
              <div class="absolute left-0 right-0 h-[2px] bg-gray-400/50 z-0"></div>
              <div class="flex justify-between w-full relative z-10">
                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-[#17464E] text-white font-bold shadow-md border-4 border-[#8AB4BB]">1</div>
                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-[#17464E] text-white font-bold shadow-md border-4 border-[#8AB4BB]">2</div>
              </div>
            </div>

            <div class="bg-white rounded-[24px] p-8 md:p-14 shadow-[0_15px_50px_rgba(0,0,0,0.15)]">
              <h3 class="text-xl font-bold text-[#17464E] mb-10">Form Input Kerjasama</h3>

              <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                  <!-- Jenis Kerjasama -->
                  <div class="space-y-2">
                    <label for="jenis_kerjasama" class="block text-sm font-bold text-[#17464E]">Jenis Kerjasama <span class="text-red-500">*</span></label>
                    <select
                      v-model="form.jenis_kerjasama"
                      id="jenis_kerjasama"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih jenis kerjasama</option>
                      <option v-for="kat in kategoris" :key="kat.id_kategori" :value="kat.id_kategori">
                        {{ kat.nama_kategori }}
                      </option>
                    </select>
                    <p v-if="form.errors.jenis_kerjasama" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_kerjasama }}</p>
                  </div>

                  <!-- Jenis Dokumen -->
                  <div class="space-y-2">
                    <label for="jenis_dokumen" class="block text-sm font-bold text-[#17464E]">Jenis Dokumen <span class="text-red-500">*</span></label>
                    <select
                      v-model="form.jenis_dokumen"
                      id="jenis_dokumen"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih jenis dokumen</option>
                      <option value="KSB">KSB</option>
                      <option value="Nota Kesepakatan">Nota Kesepakatan</option>
                      <option value="Perjanjian Teknis">Perjanjian Teknis</option>
                      <option value="PKS">PKS</option>
                      <option value="Rencana Kerja">Rencana Kerja</option>
                      <option value="MOU">MOU</option>
                      <option value="RKT">RKT</option>
                      <option value="LOI">LOI</option>
                    </select>
                    <p v-if="form.errors.jenis_dokumen" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_dokumen }}</p>
                  </div>

                  <!-- Judul Dokumen Perjanjian -->
                  <div class="space-y-2">
                    <label for="judul_dokumen" class="block text-sm font-bold text-[#17464E]">Judul Dokumen Perjanjian <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.judul_dokumen"
                      id="judul_dokumen"
                      type="text"
                      placeholder="Masukkan judul dokumen perjanjian"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.judul_dokumen" class="text-red-500 text-xs mt-1">{{ form.errors.judul_dokumen }}</p>
                  </div>

                  <!-- Mitra Kerjasama -->
                  <div class="space-y-2">
                    <label for="mitra_kerjasama" class="block text-sm font-bold text-[#17464E]">Mitra Kerjasama <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.mitra_kerjasama"
                      id="mitra_kerjasama"
                      type="text"
                      placeholder="Mitra kerjasama ke- 1"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.mitra_kerjasama" class="text-red-500 text-xs mt-1">{{ form.errors.mitra_kerjasama }}</p>
                  </div>

                  <!-- Nomor Dokumen dari Mitra -->
                  <div class="space-y-2">
                    <label for="nomor_dokumen" class="block text-sm font-bold text-[#17464E]">Nomor Dokumen dari Mitra <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.nomor_dokumen"
                      id="nomor_dokumen"
                      type="text"
                      placeholder="Inputkan nomor surat mitra anda"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.nomor_dokumen" class="text-red-500 text-xs mt-1">{{ form.errors.nomor_dokumen }}</p>
                  </div>

                  <!-- Pembiayaan -->
                  <div class="space-y-2">
                    <label for="pembayaan" class="block text-sm font-bold text-[#17464E]">Pembiayaan <span class="text-orange-500">(*wajib dipilih)</span></label>
                    <select
                      v-model="form.pembayaan"
                      id="pembayaan"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih pembiayaan</option>
                      <option value="APBN">APBN</option>
                      <option value="APBD">APBD</option>
                      <option value="PIHAK KETIGA">PIHAK KETIGA</option>
                      <option value="PARA PIHAK">PARA PIHAK</option>
                      <option value="SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN">SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN</option>
                    </select>
                    <p v-if="form.errors.pembayaan" class="text-red-500 text-xs mt-1">{{ form.errors.pembayaan }}</p>
                  </div>

                  <!-- Ususan -->
                  <div class="space-y-2">
                    <label for="ususan" class="block text-sm font-bold text-[#17464E]">Ususan</label>
                    <select
                      v-model="form.ususan"
                      id="ususan"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    >
                      <option value="">Pilih urusan</option>
                      <option value="SEMUA URUSAN">SEMUA URUSAN</option>
                      <option value="PENDIDIKAN">PENDIDIKAN</option>
                      <option value="KESEHATAN">KESEHATAN</option>
                      <option value="PEKERJAAN UMUM DAN PENATAAN RUANG">PEKERJAAN UMUM DAN PENATAAN RUANG</option>
                      <option value="PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN">PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN</option>
                      <option value="KETANTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT">KETANTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT</option>
                      <option value="SOSIAL">SOSIAL</option>
                      <option value="TENAGA KERJA">TENAGA KERJA</option>
                      <option value="PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK">PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK</option>
                      <option value="PANGAN">PANGAN</option>
                      <option value="PERTANAHAN">PERTANAHAN</option>
                    </select>
                    <p v-if="form.errors.ususan" class="text-red-500 text-xs mt-1">{{ form.errors.ususan }}</p>
                  </div>

                  <!-- Tanggal Mulai -->
                  <div class="space-y-2">
                    <label for="tanggal_mulai" class="block text-sm font-bold text-[#17464E]">Tanggal Mulai / Penetapan <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.tanggal_mulai"
                      id="tanggal_mulai"
                      type="date"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.tanggal_mulai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_mulai }}</p>
                  </div>

                  <!-- Tanggal Selesai -->
                  <div class="space-y-2">
                    <label for="tanggal_selesai" class="block text-sm font-bold text-[#17464E]">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input
                      v-model="form.tanggal_selesai"
                      id="tanggal_selesai"
                      type="date"
                      class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all"
                    />
                    <p v-if="form.errors.tanggal_selesai" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_selesai }}</p>
                  </div>
                </div>

                <!-- File Upload -->
                <div class="space-y-2">
                  <label for="dokumen_file" class="block text-sm font-bold text-[#17464E]">
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
                    @drop.prevent="(e) => {
                      const file = e.dataTransfer.files[0];
                      if (file && file.type === 'application/pdf') {
                        form.dokumen_file = file;
                        fileName = file.name;
                      }
                    }"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-[#17464E] transition cursor-pointer"
                  >
                    <div class="flex flex-col items-center">
                      <svg class="w-14 h-14 text-[#17464E] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                      </svg>
                      <p class="font-semibold text-[#17464E] mb-1">Drag & Drop Dokumen Kerjasama (PDF)</p>
                      <p class="text-xs text-gray-600 mb-4">atau klik untuk memilih file</p>
                      <button
                        type="button"
                        class="px-6 py-2 bg-[#17464E] text-white rounded-lg text-sm font-semibold hover:bg-[#0f3238] transition"
                      >
                        Pilih File
                      </button>
                      <p v-if="fileName" class="text-sm text-gray-600 mt-4">✓ {{ fileName }}</p>
                    </div>
                  </div>
                  <p v-if="form.errors.dokumen_file" class="text-red-500 text-xs mt-1">{{ form.errors.dokumen_file }}</p>
                </div>

                <div class="flex gap-4 justify-center pt-8">
                  <Link :href="route('portal-mitra')" class="px-14 py-3 bg-[#D1D5DB] text-[#4B5563] rounded-xl font-bold hover:bg-gray-300 transition-all">Batal</Link>
                  <button type="submit" class="px-14 py-3 bg-[#336D71] text-white rounded-xl font-bold hover:bg-[#28575a] shadow-lg transition-all">Simpan Pengajuan</button>
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