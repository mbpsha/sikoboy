<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import KerjasamaCard from '@/Components/Mitra/KerjasamaCard.vue';

const page = usePage();
const mitra = computed(() => page.props.value?.mitra);
const kerjasamaList = computed(() => page.props.value?.kerjasama || []);

// Data mock untuk preview tampilan
const mockKerjasama = [
  {
    id_kerjasama: 1,
    nama_kerjasama: 'Digitalisasi Sistem Administrasi Kependudukan',
    judul_dokumen: 'Digitalisasi Sistem Administrasi Kependudukan',
    status: 'pengajuan',
    kategori: { nama_kategori: 'Kerjasama dengan Pihak Ketiga' },
    urusan: 'Urusan Lainnya',
    ususan: 'Urusan Lainnya',
    tgl_mulai: '2027-06-05',
    tgl_selesai: '2032-06-05',
    created_at: '2026-04-18'
  }
];

// Gunakan mock data jika tidak ada data dari backend
const displayKerjasama = computed(() => {
  const realData = kerjasamaList.value && Array.isArray(kerjasamaList.value) && kerjasamaList.value.length > 0 
    ? kerjasamaList.value 
    : null;
  
  // Selalu tampilkan mock data jika tidak ada data real
  return realData || mockKerjasama;
});

// Tab state
const activeTab = ref('riwayat');

// Format tanggal
const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Get status badge color
const getStatusBadge = (status) => {
  const statusMap = {
    'pengajuan': { bg: 'bg-yellow-100', text: 'text-yellow-700', label: 'Pending' },
    'disetujui': { bg: 'bg-green-100', text: 'text-green-700', label: 'Disetujui' },
    'ditolak': { bg: 'bg-red-100', text: 'text-red-700', label: 'Ditolak' },
    'berlangsung': { bg: 'bg-blue-100', text: 'text-blue-700', label: 'Berlangsung' },
    'selesai': { bg: 'bg-gray-100', text: 'text-gray-700', label: 'Selesai' },
  };
  return statusMap[status] || { bg: 'bg-gray-100', text: 'text-gray-700', label: status };
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">

    <Header />

    <main class="flex-1 pt-32 pb-10 px-8">
      <div class="max-w-7xl mx-auto">

        <div class="mb-8">
          <h1 class="text-3xl font-bold text-[#17464E]">Profil Mitra</h1>
          <p class="text-sm text-[#2f5e66] mt-1">
            Kelola informasi dan pantau status pengajuan kerjasama Anda
          </p>
        </div>

        <div class="grid grid-cols-12 gap-6">

          <div class="col-span-3 space-y-5">
            <div class="bg-[#E7F0F1] rounded-2xl p-6 shadow-md">
              <div class="flex items-start gap-4">
                <div class="bg-[#2f6f73] p-3 rounded-xl shadow-sm flex items-center justify-center w-14 h-14 shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="white">
                    <path d="M7 19h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2V9H7v2Zm4 0h2V9h-2v2Zm4 0h2V9h-2v2ZM3 21V3h18v18H3Zm2-2h14V5H5v14Z"/>
                  </svg>
                </div>
                
                <div class="flex flex-col gap-1">
                  <h3 class="text-xl font-bold text-[#17464E] leading-tight mb-1">
                    {{ mitra?.nama_perusahaan || 'Hamaz Sejahtera Group' }}
                  </h3>
                  <span class="inline-block text-xs bg-[#86efac] text-[#166534] px-4 py-1 rounded-full font-medium w-fit">
                    Aktif
                  </span>
                </div>
              </div>

              <div class="mt-5 text-[#40676f]">
                <p class="text-sm font-medium opacity-70">Alamat</p>
                <p class="text-sm mt-1 leading-relaxed font-semibold">
                  {{ mitra?.alamat || 'Jl. Jend. Urip Sumoharjo No. 116, Kecamatan Jebres, Jawa Tengah 57128' }}
                </p>
              </div>
            </div>

            <div class="bg-[#E7F0F1] rounded-2xl p-5 shadow-md">
              <h3 class="text-sm font-semibold text-[#17464E] mb-3">Statistik</h3>
              <div class="space-y-2 text-sm text-[#17464E]">
                <div class="flex justify-between"><span>Total Pengajuan</span><span>0</span></div>
                <div class="flex justify-between"><span>Disetujui</span><span>0</span></div>
                <div class="flex justify-between"><span>Dalam Proses</span><span>0</span></div>
                <div class="flex justify-between"><span>Pending</span><span>0</span></div>
              </div>
            </div>
          </div>

          <div class="col-span-9">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
              
              <div class="flex items-end bg-[#2f6f73]"> 
                <button
                  @click="activeTab = 'pengajuan'"
                  :class="[
                    'flex-1 text-center py-4 text-sm font-semibold transition-all duration-300 relative z-10',
                    activeTab === 'pengajuan'
                      ? 'bg-white text-[#17464E] rounded-tr-[30px]'
                      : 'text-white/80 hover:text-white'
                  ]"
                >
                  Pengajuan Kerjasama
                </button>

                <button
                  @click="activeTab = 'riwayat'"
                  :class="[
                    'flex-1 text-center py-4 text-sm font-semibold transition-all duration-300 relative z-10',
                    activeTab === 'riwayat'
                      ? 'bg-white text-[#17464E] rounded-tl-[30px]'
                      : 'text-white/80 hover:text-white'
                  ]"
                >
                  Riwayat Kerjasama
                </button>
              </div>

              <div class="p-8">
                <div v-if="activeTab === 'pengajuan'">
                  <div class="grid grid-cols-3 gap-5 mb-10">
                    <div class="bg-[#DCEBED] p-5 rounded-2xl shadow-md hover:shadow-lg transition">
                      <div class="flex items-center gap-3 mb-2">
                        <div class="bg-white p-2 rounded-md shadow-sm text-lg">📄</div>
                        <h4 class="text-sm font-semibold text-[#17464E]">Proses Pengajuan</h4>
                      </div>
                      <p class="text-xs text-[#40676f] leading-relaxed">
                        Isi formulir pengajuan kerjasama dan unggah dokumen pendukung yang diperlukan.
                      </p>
                    </div>

                    <div class="bg-[#DCEBED] p-5 rounded-2xl shadow-md hover:shadow-lg transition">
                      <div class="flex items-center gap-3 mb-2">
                        <div class="bg-white p-2 rounded-md shadow-sm text-lg">✔️</div>
                        <h4 class="text-sm font-semibold text-[#17464E]">Proses Verifikasi</h4>
                      </div>
                      <p class="text-xs text-[#40676f] leading-relaxed">
                        Tim kami akan segera melakukan verifikasi terhadap dokumen yang Anda ajukan.
                      </p>
                    </div>

                    <div class="bg-[#DCEBED] p-5 rounded-2xl shadow-md hover:shadow-lg transition">
                      <div class="flex items-center gap-3 mb-2">
                        <div class="bg-white p-2 rounded-md shadow-sm text-lg">📊</div>
                        <h4 class="text-sm font-semibold text-[#17464E]">Status Pengajuan</h4>
                      </div>
                      <p class="text-xs text-[#40676f] leading-relaxed">
                        Pantau status pengajuan kerjasama Anda secara real-time melalui dashboard ini.
                      </p>
                    </div>
                  </div>

                  <div class="flex justify-center">
                    <Link
                      :href="route('mitra.pengajuan.step1')"
                      class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#2f6f73] to-[#1e565a] text-white rounded-full shadow-md hover:shadow-lg transition-transform hover:scale-105"
                    >
                      <span class="text-lg">+</span>
                      Ajukan Baru
                    </Link>
                  </div>
                </div>

                <div v-if="activeTab === 'riwayat'" class="py-6">
                  <div class="space-y-5">
                    <!-- Kartu Kerjasama 1 -->
                    <div class="bg-gradient-to-br from-[#D4E9ED] to-[#E7F5F7] rounded-2xl p-6 shadow-md">
                      
                      <!-- Header dengan judul dan status -->
                      <div class="flex items-start justify-between mb-6 pb-4 border-b-2 border-[#B8D4DA]">
                        <div class="flex-1">
                          <h2 class="text-2xl font-bold text-[#17464E] mb-2">
                            Digitalisasi Sistem Administrasi Kependudukan
                          </h2>
                          <p class="text-sm text-[#40676f]">
                            Kelola dan pantau progress kerjasama Anda
                          </p>
                        </div>
                        <span class="inline-block text-xs px-4 py-2 rounded-full font-semibold bg-yellow-100 text-yellow-700">
                          Pending
                        </span>
                      </div>

                      <!-- Detail Informasi -->
                      <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                          <p class="text-xs font-medium text-[#40676f] opacity-70 mb-1">Jenis Kerjasama</p>
                          <p class="text-sm font-bold text-[#17464E]">
                            Kerjasama dengan Pihak Ketiga
                          </p>
                        </div>
                        <div>
                          <p class="text-xs font-medium text-[#40676f] opacity-70 mb-1">Urusan</p>
                          <p class="text-sm font-bold text-[#17464E]">
                            Urusan Lainnya
                          </p>
                        </div>
                        <div>
                          <p class="text-xs font-medium text-[#40676f] opacity-70 mb-1">Tanggal Diajukan</p>
                          <p class="text-sm font-bold text-[#17464E]">
                            18 April 2026
                          </p>
                        </div>
                        <div>
                          <p class="text-xs font-medium text-[#40676f] opacity-70 mb-1">Periode</p>
                          <p class="text-sm font-bold text-[#17464E]">
                            5 Tahun
                          </p>
                        </div>
                      </div>

                      <!-- Lihat Progres Kerjasama -->
                      <div class="mb-4 flex items-center gap-2 text-[#2f6f73] hover:text-[#1e565a] cursor-pointer transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-semibold">Lihat Progres Kerjasama</span>
                      </div>

                      <!-- Warning Message -->
                      <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-6 rounded">
                        <p class="text-xs text-yellow-700 font-semibold">
                          💡 *Pantau status dokumen kerjasama Anda secara rutin dan berkala
                        </p>
                      </div>

                      <!-- Action Button -->
                      <div class="flex justify-center">
                        <Link
                          :href="route('mitra.pengajuan.step1')"
                          class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#2f6f73] to-[#1e565a] text-white rounded-full shadow-md hover:shadow-lg transition-transform hover:scale-105 font-semibold"
                        >
                          <span class="text-lg">+</span>
                          Ajukan Baru
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
/* Radius Lengkungan Tab */
.rounded-tr-\[30px\] {
  border-top-right-radius: 30px !important;
}
.rounded-tl-\[30px\] {
  border-top-left-radius: 30px !important;
}

button {
  outline: none;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #c1ced0;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #8FA4A7;
}
</style>