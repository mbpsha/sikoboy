<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const page = usePage();
const mitra = computed(() => page.props.value?.mitra);

// Tab state
const activeTab = ref('pengajuan');
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
                <div v-show="activeTab === 'pengajuan'">
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
                    <button class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#2f6f73] to-[#1e565a] text-white rounded-full shadow-md hover:shadow-lg transition-transform hover:scale-105">
                      <span class="text-lg">+</span>
                      Ajukan Baru
                    </button>
                  </div>
                </div>

                <div v-show="activeTab === 'riwayat'" class="py-16">
                  <div class="text-center text-sm text-gray-500 space-y-3">
                    <p class="text-2xl">📭</p>
                    <p>Belum ada riwayat kerjasama yang tercatat.</p>
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