<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import KerjasamaProgressModal from '@/Components/Mitra/KerjasamaProgressModal.vue';
import DetailNotif from '@/Components/Mitra/Profile/DetailNotif.vue'; 

const page = usePage();

const notifications = computed(() => page.props?.notifications || []);

// 🔔 State untuk notifikasi yang sudah ditutup (disimpan di localStorage)
const closedNotifications = ref([]);

// 🔔 Load closed notifications dari localStorage saat component mount
onMounted(() => {
  try {
    const stored = localStorage.getItem('closed_notifications');
    if (stored) {
      closedNotifications.value = JSON.parse(stored);
    }
  } catch (e) {
    console.error('Error loading closed notifications:', e);
  }
});

// 🔔 Function tutup notifikasi
const closeNotification = (notifId) => {
  if (!closedNotifications.value.includes(notifId)) {
    closedNotifications.value.push(notifId);
    // Simpan ke localStorage
    localStorage.setItem('closed_notifications', JSON.stringify(closedNotifications.value));
  }
};

// 🔔 Filter notifikasi yang belum ditutup
const visibleNotifications = computed(() => {
  return notifications.value.filter(notif => !closedNotifications.value.includes(notif.id));
});

// 🔔 Modal state untuk Detail Notif
const isDetailNotifOpen = ref(false);
const selectedNotification = ref(null);

// 🔔 Function buka detail notifikasi (dipakai oleh banner alert)
const openDetailNotif = (notification) => {
  selectedNotification.value = notification;
  isDetailNotifOpen.value = true;
};

// Safely access props dengan fallback (untuk data mitra/stats)
const mitra = computed(() => {
  try {
    return page.props?.mitra || page.props?.value?.mitra || null;
  } catch (e) {
    console.error('Error accessing mitra props:', e);
    return null;
  }
});

const stats = computed(() => {
  try {
    return page.props?.stats || page.props?.value?.stats || { total_pengajuan: 0, disetujui: 0, dalam_proses: 0, pending: 0 };
  } catch (e) {
    return { total_pengajuan: 0, disetujui: 0, dalam_proses: 0, pending: 0 };
  }
});

const kerjasamaList = computed(() => {
  try {
    return page.props?.kerjasama_list || page.props?.value?.kerjasama_list || [];
  } catch (e) {
    return [];
  }
});

// Modal state
const isProgressModalOpen = ref(false);
const showLogoutConfirm = ref(false);
const selectedKerjasama = ref(null);

// Tab state
const activeTab = ref('riwayat');

// Logout function
const handleLogout = () => {
  // ✅ Clear closed notifications saat logout agar muncul lagi saat login berikutnya
  localStorage.removeItem('closed_notifications');
  
  router.post(route('logout'), {}, {
    onSuccess: () => {
      // Redirect akan otomatis dilakukan oleh Laravel setelah logout
    },
    onError: (errors) => {
      console.error('Logout error:', errors);
    }
  });
  showLogoutConfirm.value = false;
};

const openLogoutConfirm = () => {
  showLogoutConfirm.value = true;
};

const cancelLogout = () => {
  showLogoutConfirm.value = false;
};

const openProgressModal = (kerjasama) => {
  selectedKerjasama.value = kerjasama;
  isProgressModalOpen.value = true;
};

// Status badge styling
const getStatusClass = (status) => {
  switch(status?.toLowerCase?.()) {
    case 'disetujui':
      return 'bg-green-300 text-green-700';
    case 'dalam_proses':
      return 'bg-yellow-300 text-yellow-700';
    case 'ditolak':
      return 'bg-red-300 text-red-700';
    case 'pending':
    default:
      return 'bg-blue-300 text-blue-700';
  }
};

const getStatusLabel = (status) => {
  switch(status?.toLowerCase?.()) {
    case 'disetujui':
      return 'Disetujui';
    case 'dalam_proses':
      return 'Dalam Proses';
    case 'ditolak':
      return 'Ditolak';
    case 'pending':
      return 'Pending';
    default:
      return 'Pending';
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-[#8AB4BB] to-[#6B9BA5] flex flex-col">

  <!-- Header (lonceng notifikasi tetap ada, tapi tidak dipake di halaman ini) -->
  <Header />

    <main class="flex-1 pt-32 pb-10 px-8">
      <div class="max-w-7xl mx-auto">

        <div class="mb-8">
          <h1 class="text-3xl font-bold text-[#17464E]">Profil Mitra</h1>
          <p class="text-sm text-[#2f5e66] mt-1">
            Kelola informasi dan pantau status pengajuan kerjasama Anda
          </p>
        </div>

        <!-- 🔔 NOTIFICATION ALERT BANNER (DUMMY - DENGAN TOMBOL SILANG) -->
        <div v-if="visibleNotifications && visibleNotifications.length > 0" class="mb-6 space-y-3">
          <div 
            v-for="(notif, index) in visibleNotifications" 
            :key="notif.id"
            class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 rounded-r-xl p-5 shadow-md hover:shadow-lg transition-shadow relative"
          >
            <!-- 🔴 TOMBOL SILANG (Close Button) -->
            <button 
              @click="closeNotification(notif.id)"
              class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-yellow-200 hover:bg-yellow-300 text-yellow-700 transition-colors"
              title="Tutup notifikasi ini"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <div class="flex gap-4 pr-8">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <h4 class="text-sm font-bold text-yellow-800 mb-1">
                  {{ notif.title }}
                </h4>
                <p class="text-sm text-yellow-700 leading-relaxed">
                  {{ notif.message }}
                </p>
                <div class="mt-3 flex gap-3 items-center">
                  <button 
                    @click="openDetailNotif(notif)"
                    class="text-xs font-semibold text-yellow-800 hover:text-yellow-900 underline cursor-pointer bg-transparent border-none p-0"
                  >
                    Lihat Kerjasama
                  </button>
                  <span class="text-xs text-yellow-600">
                    • {{ notif.days_left === null || notif.days_left === undefined ? 'Baru' : `${notif.days_left} hari lagi` }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 🔔 END NOTIFICATION ALERT BANNER -->

        <div class="grid grid-cols-12 gap-6">

          <div class="col-span-3 space-y-5">
            <div class="bg-[#E7F0F1] rounded-2xl p-6 shadow-md">
              <div class="flex items-start gap-4">
                <div class="bg-[#2f6f73] p-3 rounded-xl shadow-sm flex items-center justify-center w-14 h-14 shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="white">
                    <path d="M7 19h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2V9H7v2Zm4 0h2V9h-2v2Zm4 0h2V9h-2v2ZM3 21V3h18v18H3Zm2-2h14V5H5v14Z"/>
                  </svg>
                </div>
                
                <div class="flex flex-col gap-2">
                  <h3 class="text-xl font-bold text-[#17464E] leading-tight mb-1">
                    <Link :href="route('mitra.profile.index')" class="hover:underline">
                      {{ mitra?.nama_perusahaan || 'Hamaz Sejahtera Group' }}
                    </Link>
                  </h3>
                  <span class="inline-block text-xs bg-[#86efac] text-[#166534] px-4 py-1 rounded-full font-medium w-fit">
                    Aktif
                  </span>
                  <Link :href="route('mitra.profile.edit')" class="mt-2 inline-block text-xs bg-blue-500 text-white px-3 py-1 rounded-full font-medium hover:bg-blue-400 self-start">
                    Edit Profil
                  </Link>
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
                <div class="flex justify-between"><span>Total Pengajuan</span><span class="font-bold">{{ stats.total_pengajuan }}</span></div>
                <div class="flex justify-between"><span>Disetujui</span><span class="font-bold">{{ stats.disetujui }}</span></div>
                <div class="flex justify-between"><span>Dalam Proses</span><span class="font-bold">{{ stats.dalam_proses }}</span></div>
                <div class="flex justify-between"><span>Pending</span><span class="font-bold">{{ stats.pending }}</span></div>
              </div>
            </div>

            <!-- Logout Button Card -->
            <div class="bg-[#E7F0F1] rounded-2xl p-5 shadow-md">
              <button 
                @click="openLogoutConfirm"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-all shadow-md hover:shadow-lg"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
              </button>
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
                    <Link
                      :href="route('mitra.pengajuan.step1')"
                      class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#2f6f73] to-[#1e565a] text-white rounded-full shadow-md hover:shadow-lg transition-transform hover:scale-105"
                    >
                      <span class="text-lg">+</span>
                      Ajukan Baru
                    </Link>
                  </div>
                </div>

                <div v-show="activeTab === 'riwayat'" class="py-6">
                  <div class="space-y-5">
                    <!-- Pesan jika tidak ada kerjasama -->
                    <div v-if="kerjasamaList.length === 0" class="text-center py-12">
                      <p class="text-[#40676f] text-sm mb-3">Anda belum memiliki kerjasama yang selesai diajukan</p>
                      <Link
                        :href="route('mitra.pengajuan.step1')"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-[#2f6f73] text-white rounded-full text-sm font-semibold hover:bg-[#1e565a] transition"
                      >
                        <span>+</span>
                        Ajukan Baru
                      </Link>
                    </div>

                    <!-- Kartu Kerjasama -->
                    <div v-for="kerjasama in kerjasamaList" :key="kerjasama.id_kerjasama" class="bg-[#D4E9ED] rounded-3xl p-8 shadow-md">
                      
                      <!-- Header dengan judul dan status -->
                      <div class="flex items-start justify-between mb-6">
                        <h2 class="text-2xl font-bold text-[#17464E] flex-1">
                          {{ kerjasama.judul }}
                        </h2>
                        <span :class="['inline-block text-xs px-4 py-1 rounded-full font-semibold ml-4 whitespace-nowrap', getStatusClass(kerjasama.status)]">
                          {{ getStatusLabel(kerjasama.status) }}
                        </span>
                      </div>

                      <!-- Detail Informasi -->
                      <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                          <span class="text-sm text-[#40676f] font-medium w-40">Jenis Kerjasama</span>
                          <span class="text-sm text-[#40676f] font-medium mx-4">:</span>
                          <span class="text-sm font-bold text-[#17464E]">{{ kerjasama.kategori }}</span>
                        </div>
                        
                        <div class="flex items-center">
                          <span class="text-sm text-[#40676f] font-medium w-40">Urusan</span>
                          <span class="text-sm text-[#40676f] font-medium mx-4">:</span>
                          <span class="text-sm font-bold text-[#17464E]">{{ kerjasama.urusan }}</span>
                        </div>

                        <div class="flex items-center">
                          <span class="text-sm text-[#40676f] font-medium w-40">Tanggal Diajukan</span>
                          <span class="text-sm text-[#40676f] font-medium mx-4">:</span>
                          <span class="text-sm font-bold text-[#17464E]">{{ kerjasama.tanggal_daftar }}</span>
                        </div>

                        <div class="flex items-center">
                          <span class="text-sm text-[#40676f] font-medium w-40">Periode</span>
                          <span class="text-sm text-[#40676f] font-medium mx-4">:</span>
                          <span class="text-sm font-bold text-[#17464E]">{{ kerjasama.periode }}</span>
                        </div>
                      </div>

                      <!-- Lihat Progres Kerjasama Button -->
                      <div class="flex justify-center mb-6">
                        <button @click="openProgressModal(kerjasama)" class="w-full py-2 text-center text-sm font-semibold text-[#2f6f73] hover:text-[#1e565a] rounded-lg transition flex items-center justify-center gap-2 bg-white/60 hover:bg-white">
                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                          </svg>
                          Lihat Progres Kerjasama
                        </button>
                      </div>

                      <!-- Warning Message -->
                      <div class="text-center">
                        <p class="text-xs text-red-600 font-semibold">
                          *Pantau status dokumen kerjasama Anda secara rutin dan berkala
                        </p>
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

    <!-- Logout Confirmation Modal -->
    <div v-if="showLogoutConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h3>
          <p class="text-sm text-gray-600 mb-6">
            Apakah Anda yakin ingin keluar dari akun Anda?
          </p>
          <div class="flex gap-3 justify-center">
            <button 
              @click="cancelLogout"
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
            >
              Batal
            </button>
            <button 
              @click="handleLogout"
              class="px-6 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition"
            >
              Ya, Keluar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress Modal -->
    <KerjasamaProgressModal 
      :isOpen="isProgressModalOpen"
      :kerjasamaNama="selectedKerjasama?.judul || ''"
      @close="isProgressModalOpen = false"
    />

    <!-- 🔔 Detail Notifikasi Modal (dipakai oleh alert banner) -->
    <DetailNotif 
      :is-open="isDetailNotifOpen"
      :notification="selectedNotification"
      @close="isDetailNotifOpen = false"
    />

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
