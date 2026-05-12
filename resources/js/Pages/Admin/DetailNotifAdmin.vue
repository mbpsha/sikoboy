<template>
  <div class="min-h-screen bg-gray-50 flex">
    
    <!-- ================= SIDEBAR ADMIN ================= -->
    <aside
      style="background-color: #0c505c"
      class="text-white w-64 h-screen fixed left-0 top-0 z-40 flex flex-col justify-between overflow-hidden"
    >
      <!-- TOP -->
      <div>
        <!-- LOGO -->
        <div class="px-6 pt-6 pb-4">
          <div class="text-3xl font-extrabold tracking-widest">SIKOBOY</div>
          <p class="text-xs text-teal-100 mt-1">Admin Dashboard</p>
          <div class="mt-4 border-t border-teal-700"></div>
        </div>

        <!-- MENU -->
        <nav class="mt-2 space-y-2 px-3">
          <!-- Beranda -->
          <Link :href="route('admin.dashboard')" :class="navClass('/admin/dashboard')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z" />
            </svg>
            <span class="text-sm">Beranda</span>
          </Link>

          <!-- Pengguna -->
          <Link :href="route('admin.pengguna.index')" :class="navClass('/admin/pengguna')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <circle cx="9" cy="8" r="3" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-3 3-5 5-5s5 2 5 5" />
            </svg>
            <span class="text-sm">Pengguna</span>
          </Link>

          <!-- Ajuan Kerjasama -->
          <Link :href="route('admin.data-kerjasama.index')" :class="navClass('/admin/data-kerjasama')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5" />
            </svg>
            <span class="text-sm">Ajuan Kerjasama</span>
          </Link>

          <!-- Riwayat Kerjasama (Dropdown) -->
          <div>
            <button type="button" @click="showRiwayatMenu = !showRiwayatMenu" :class="navClass('/admin/riwayat-kerjasama')">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
              </svg>
              <span class="text-sm flex-1 text-left">Riwayat Kerjasama</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="showRiwayatMenu ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
              </svg>
            </button>

            <div v-if="showRiwayatMenu" class="mt-2 ml-5 space-y-1 border-l border-teal-700/50 pl-3">
              <Link :href="route('admin.riwayat-kerjasama.gabungan')" :class="subNavClass('/admin/riwayat-kerjasama/gabungan')">
                <span class="text-sm">Semua Kerjasama</span>
              </Link>
              <Link :href="route('admin.riwayat-kerjasama.pemerintah')" :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')">
                <span class="text-sm">Pemrakarsa Boyolali</span>
              </Link>
              <Link :href="route('admin.riwayat-kerjasama.mitra')" :class="subNavClass('/admin/riwayat-kerjasama/mitra')">
                <span class="text-sm">Pemrakarsa Mitra</span>
              </Link>
            </div>
          </div>

          <!-- Manajemen Potensi -->
          <Link :href="route('admin.manajemen-potensi.index')" :class="navClass('/admin/manajemen-potensi')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              <circle cx="9" cy="6" r="2" />
              <circle cx="15" cy="12" r="2" />
              <circle cx="11" cy="18" r="2" />
            </svg>
            <span class="text-sm">Manajemen Potensi</span>
          </Link>

          <!-- Manajemen Dokumen -->
          <Link :href="route('admin.manajemen-dokumen.index')" :class="navClass('/admin/manajemen-dokumen')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6M9 17h6" />
            </svg>
            <span class="text-sm">Manajemen Dokumen</span>
          </Link>

          <!-- Manajemen Peraturan -->
          <Link :href="route('admin.manajemen-peraturan.index')" :class="navClass('/admin/manajemen-peraturan')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5" />
            </svg>
            <span class="text-sm">Manajemen Peraturan</span>
          </Link>
        </nav>
      </div>

      <!-- BOTTOM (LOGOUT) -->
      <div class="p-4">
        <button @click="showConfirm = true" class="w-full bg-red-600 hover:bg-red-700 transition p-3 rounded-lg font-semibold">
          Logout
        </button>
      </div>

      <!-- MODAL KONFIRMASI -->
      <div v-if="showConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-80 text-center">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Yakin ingin logout?</h2>
          <div class="flex justify-center gap-4">
            <button @click="showConfirm = false" class="px-4 py-2 bg-gray-600 rounded-lg">Batal</button>
            <button @click="logout" class="px-4 py-2 bg-red-600 text-white rounded-lg">Logout</button>
          </div>
        </div>
      </div>
    </aside>
    <!-- ================= END SIDEBAR ================= -->


    <!-- ================= MAIN CONTENT ================= -->
    <main class="flex-1 ml-64">
      
      <!-- Header -->
      <header class="bg-white shadow-sm px-6 py-4 sticky top-0 z-30">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Dashboard / Notifikasi / Detail</p>
            <h1 class="text-2xl font-semibold text-teal-700 mt-1">
              Detail Notifikasi Kerjasama
            </h1>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6 space-y-6">
        
        <!-- Alert Banner (Dinamis berdasarkan status) -->
        <div 
          class="rounded-lg p-4 flex items-start gap-3"
          :class="kerjasama?.status === 'expired' ? 'bg-red-50 border border-red-200' : 'bg-orange-50 border border-orange-200'"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-6 w-6 flex-shrink-0"
            :class="kerjasama?.status === 'expired' ? 'text-red-600' : 'text-orange-600'"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path 
              v-if="kerjasama?.status === 'expired'"
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" 
            />
            <path 
              v-else
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />
          </svg>
          <div class="flex-1">
            <h3 
              class="font-semibold"
              :class="kerjasama?.status === 'expired' ? 'text-red-800' : 'text-orange-800'"
            >
              {{ kerjasama?.status === 'expired' ? 'Kerjasama Telah Berakhir' : 'Kerjasama Akan Segera Berakhir' }}
            </h3>
            <p 
              class="text-sm mt-1"
              :class="kerjasama?.status === 'expired' ? 'text-red-700' : 'text-orange-700'"
            >
              {{ kerjasama?.status === 'expired' 
                ? `Kerjasama ini telah berakhir pada ${formatDate(kerjasama.tanggalBerakhir)}. Segera lakukan evaluasi atau perpanjangan jika diperlukan.`
                : `Kerjasama ini akan berakhir dalam ${kerjasama.daysLeft} hari lagi pada ${formatDate(kerjasama.tanggalBerakhir)}. Persiapkan proses perpanjangan atau evaluasi.`
              }}
            </p>
          </div>
          <div 
            class="text-3xl font-bold"
            :class="kerjasama?.status === 'expired' ? 'text-red-600' : 'text-orange-600'"
          >
            {{ kerjasama?.status === 'expired' ? '✓' : kerjasama?.daysLeft }}
            <span class="text-sm font-normal block text-center">{{ kerjasama?.status === 'expired' ? 'Selesai' : 'Hari' }}</span>
          </div>
        </div>

        <!-- Main Info Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-start gap-3 mb-6">
            <span 
              class="px-3 py-1 text-sm font-bold rounded-full"
              :class="kerjasama?.type === 'MITRA' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
            >
              {{ kerjasama?.type }}
            </span>
            <span 
              class="px-3 py-1 text-sm font-semibold rounded-full"
              :class="kerjasama?.status === 'aktif' || kerjasama?.status === 'warning' || kerjasama?.status === 'urgent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            >
              {{ kerjasama?.status === 'expired' ? 'Expired' : 'Aktif' }}
            </span>
          </div>

          <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ kerjasama?.judul }}</h2>
          
          <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
              <p class="text-sm text-gray-500 mb-1">Nomor Kerjasama</p>
              <p class="font-semibold text-gray-800">{{ kerjasama?.nomor }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 mb-1">Tanggal Mulai</p>
              <p class="font-semibold text-gray-800">{{ formatDate(kerjasama?.tanggalMulai) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 mb-1">Tanggal Berakhir</p>
              <p class="font-semibold text-gray-800">{{ formatDate(kerjasama?.tanggalBerakhir) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 mb-1">Durasi</p>
              <p class="font-semibold text-gray-800">{{ kerjasama?.durasi }}</p>
            </div>
          </div>

          <div class="pt-6 border-t border-gray-200">
            
          </div>
        </div>

        <!-- Parties Involved -->
        <div class="grid grid-cols-2 gap-6">
          <!-- Pihak 1 (SETDA) -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              Pihak Pertama (SETDA)
            </h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-500">Nama Instansi</p>
                <p class="font-semibold text-gray-800">{{ kerjasama?.pihak1?.nama_instansi }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="text-gray-700">{{ kerjasama?.pihak1?.alamat }}</p>
              </div>
              <div>
                
              </div>
              <div>
               
              </div>
            </div>
          </div>

          <!-- Pihak 2 (Mitra) -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Pihak Kedua ({{ kerjasama?.type }})
            </h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-500">Nama Instansi/Perusahaan</p>
                <p class="font-semibold text-gray-800">{{ kerjasama?.pihak2?.nama_instansi }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="text-gray-700">{{ kerjasama?.pihak2?.alamat }}</p>
              </div>
              <div>
                
              </div>
              <div>
                
              </div>
            </div>
          </div>
        </div>

        <!-- Tutup Button -->
        <div class="flex justify-end pt-4 border-t border-gray-200">
          <button 
            @click="goBack"
            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
          >
            Tutup
          </button>
        </div>

      </div>
    </main>
    <!-- ================= END MAIN CONTENT ================= -->
    
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

// --- Sidebar Logic ---
const page = usePage()
const showConfirm = ref(false)
const showRiwayatMenu = ref(page.url?.startsWith('/admin/riwayat-kerjasama') ?? false)

watch(() => page.url, (url) => {
  if (url?.startsWith('/admin/riwayat-kerjasama')) {
    showRiwayatMenu.value = true
  }
})

const navClass = (url) => {
  const base = "flex items-center gap-3 px-2 py-2 rounded-full transition w-full"
  if (page.url && page.url.startsWith(url)) {
    return base + " bg-teal-100 text-teal-900 font-semibold"
  }
  return base + " text-white/90 hover:bg-teal-700/30 hover:text-white"
}

const subNavClass = (url) => {
  const base = "flex items-center gap-3 px-3 py-2 rounded-lg transition w-full text-left"
  if (page.url && page.url.startsWith(url)) {
    return base + " bg-white text-teal-900 font-semibold"
  }
  return base + " text-white/80 hover:bg-teal-700/25 hover:text-white"
}

const logout = () => {
  router.post(route('logout'), {}, { onSuccess: () => (window.location.href = '/') })
}

// --- Detail Page Logic ---
const props = defineProps({
  id: {
    type: [String, Number],
    required: true
  }
})

const goBack = () => {
  router.visit('/admin/notifikasi')
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

// 🎭 DUMMY DATA - Sama seperti di NotifAdmin.vue agar sinkron
const allNotifications = [
  {
    id: 1,
    type: 'MITRA',
    judul: 'Kerjasama akan berakhir dalam 90 hari lagi',
    nomor: '012/SP-KS/PT-ABC/V/2026',
    status: 'warning',
    tanggalMulai: '2026-01-02',
    tanggalBerakhir: '2027-01-02',
    daysLeft: 90,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
      
    },
    pihak2: {
      nama_instansi: 'PT BPR Bank Boyolali (Perseroda)',
      alamat: 'Jl. Letjen S. Parman No. 12, Boyolali',
      
    }
  },
  {
    id: 2,
    type: 'MITRA',
    judul: 'Kerjasama akan berakhir dalam 30 hari lagi',
    nomor: '013/SP-KS/CV-SR/VII/2026',
    status: 'urgent',
    tanggalMulai: '2025-12-15',
    tanggalBerakhir: '2026-12-15',
    daysLeft: 30,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
      
    },
    pihak2: {
      nama_instansi: 'CV Sumber Rejeki',
      alamat: 'Jl. Diponegoro No. 45, Boyolali',
      
    }
  },
  {
    id: 3,
    type: 'MITRA',
    judul: 'Kerjasama telah berakhir',
    nomor: '010/SP-KS/PT-SA/III/2026',
    status: 'expired',
    tanggalMulai: '2025-05-01',
    tanggalBerakhir: '2026-05-01',
    daysLeft: 0,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
      
    },
    pihak2: {
      nama_instansi: 'PT Sejahtera Abadi',
      alamat: 'Jl. Sudirman No. 88, Boyolali',
      
    }
  },
  {
    id: 4,
    type: 'SETDA',
    judul: 'Arsip dokumen akan berakhir dalam 60 hari',
    nomor: '015/SP-KS/SETDA/VI/2026',
    status: 'warning',
    tanggalMulai: '2026-02-15',
    tanggalBerakhir: '2027-02-15',
    daysLeft: 60,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
      
    },
    pihak2: {
      nama_instansi: 'PT Maju Jaya',
      alamat: 'Jl. Ahmad Yani No. 23, Boyolali',
      
    }
  },
  {
    id: 5,
    type: 'SETDA',
    judul: 'Arsip dokumen akan berakhir dalam 90 hari',
    nomor: '016/SP-KS/SETDA/VIII/2026',
    status: 'warning',
    tanggalMulai: '2026-03-10',
    tanggalBerakhir: '2027-03-10',
    daysLeft: 90,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
      
    },
    pihak2: {
      nama_instansi: 'CV Berkah Jaya',
      alamat: 'Jl. Gatot Subroto No. 12, Boyolali',
      
    }
  },
  {
    id: 6,
    type: 'SETDA',
    judul: 'Arsip dokumen telah berakhir',
    nomor: '011/SP-KS/SETDA/II/2026',
    status: 'expired',
    tanggalMulai: '2025-04-20',
    tanggalBerakhir: '2026-04-20',
    daysLeft: 0,
    durasi: '1 Tahun',
    
    pihak1: {
      nama_instansi: 'Sekretariat Daerah Kabupaten Boyolali',
      alamat: 'Jl. Pandanaran No. 1, Boyolali, Jawa Tengah',
    
    },
    pihak2: {
      nama_instansi: 'PT Nusantara',
      alamat: 'Jl. Kartini No. 56, Boyolali',
      
      
    }
  }
]

// 🔍 Cari data berdasarkan ID dari route
const kerjasama = computed(() => {
  return allNotifications.find(n => n.id === parseInt(props.id)) || allNotifications[0]
})
</script>