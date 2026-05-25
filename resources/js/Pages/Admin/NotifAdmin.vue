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
      
      <!-- Header Page -->
      <header class="bg-white shadow-sm px-6 py-4 sticky top-0 z-30">
        <p class="text-sm text-gray-500">Dashboard / Notifikasi</p>
        <h1 class="text-2xl font-semibold text-teal-700 mt-1">
          Notifikasi Kerjasama
        </h1>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Search -->
        <div class="bg-white rounded-lg shadow-sm px-4 py-3 mb-4">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari notifikasi berdasarkan judul, nomor, atau isi pesan..."
              class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 pl-10 text-sm text-gray-700 placeholder:text-gray-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
            />
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.4a7.2 7.2 0 11-14.4 0 7.2 7.2 0 0114.4 0z" />
            </svg>
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm px-4 py-3 mb-6">
          <div class="flex gap-2 overflow-x-auto">
            <button
              v-for="tab in tabs"
              :key="tab.value"
              @click="activeTab = tab.value"
              class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors flex items-center gap-1"
              :class="activeTab === tab.value 
                ? 'bg-teal-600 text-white' 
                : 'text-gray-600 hover:bg-gray-100'"
            >
              {{ tab.label }}
              <span v-if="getCount(tab.value)" class="px-2 py-0.5 text-xs bg-white/20 rounded-full">
                {{ getCount(tab.value) }}
              </span>
            </button>
          </div>
        </div>

        <!-- Notification List -->
        <div class="space-y-4">
          <div
            v-for="notif in filteredNotifications"
            :key="notif.id"
            class="bg-white rounded-lg shadow-sm p-5 border-l-4 transition-all hover:shadow-md"
            :class="notif.status === 'expired' ? 'border-l-red-500' : (notif.status === 'info' ? 'border-l-blue-400' : 'border-l-orange-400')"
          >
            <div class="flex gap-4">
              <!-- Badge Type -->
              <div class="flex-shrink-0">
                <span 
                  class="px-3 py-1 text-xs font-bold rounded-full"
                  :class="notif.kind === 'pengajuan_kerjasama' 
                    ? 'bg-emerald-100 text-emerald-700' 
                    : notif.kind === 'revisi_mitra'
                      ? 'bg-amber-100 text-amber-700'
                      : 'bg-sky-100 text-sky-700'"
                >
                  {{ getKindLabel(notif.kind) }}
                </span>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                  <h3 class="font-semibold" :class="notif.status === 'expired' ? 'text-red-600' : (notif.status === 'info' ? 'text-blue-600' : 'text-orange-600')">
                    {{ notif.title }}
                  </h3>
                <p class="text-gray-600 text-sm mt-1">{{ notif.description }}</p>
                
                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                    <span class="text-gray-500" v-if="notif.nomor">
                      <strong class="text-gray-700">Nomor:</strong> {{ notif.nomor || 'DUMMY-001' }}
                    </span>
                    <span class="text-gray-500" v-if="notif.tanggalBerakhir">
                      <strong class="text-gray-700">Berakhir:</strong> {{ formatDate(notif.tanggalBerakhir) }}
                    </span>
                    <span 
                      class="px-2 py-0.5 rounded text-xs font-semibold"
                      :class="notif.status === 'expired'
                      ? 'bg-red-100 text-red-700' 
                      : (notif.status === 'info' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700')"
                    >
                      {{ notif.status === 'expired' ? 'Expired' : (notif.status === 'info' ? 'Info' : 'Aktif') }}
                    </span>
                  </div>
                </div>

              <!-- Countdown -->
              <div class="text-right flex-shrink-0">
                <div 
                  class="w-20 h-20 rounded-lg flex flex-col items-center justify-center text-white font-bold"
                  :class="notif.status === 'expired' ? 'bg-red-500' : (notif.status === 'info' ? 'bg-blue-500' : 'bg-orange-500')"
                >
                  <span class="text-2xl">{{ notif.daysLeft ?? '!' }}</span>
                  <span class="text-xs">{{ notif.daysLeft === null ? 'Baru' : 'Hari' }}</span>
                </div>
                <button 
                  @click="viewDetail(notif.id)"
                  class="mt-2 text-teal-600 text-sm font-semibold hover:underline flex items-center gap-1 mx-auto"
                >
                  Detail
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredNotifications.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">Tidak ada notifikasi untuk filter ini.</p>
          </div>
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

// --- Notification Page Logic ---
const props = defineProps({
  notifications: {
    type: Array,
    default: () => [],
  },
})

const activeTab = ref('semua')
const searchQuery = ref('')

const notifications = computed(() => props.notifications ?? [])

const tabs = [
  { label: 'Semua', value: 'semua' },
  { label: 'Pengajuan Kerjasama', value: 'pengajuan_kerjasama' },
  { label: 'Revisi Mitra', value: 'revisi_mitra' },
  { label: 'Akan Berakhir', value: 'akan_berakhir' },
  { label: 'Sudah Berakhir', value: 'sudah_berakhir' },
]

const getKindLabel = (kind) => {
  switch (kind) {
    case 'pengajuan_kerjasama': return 'Pengajuan'
    case 'revisi_mitra': return 'Revisi Mitra'
    case 'status_admin': return 'Pengingat'
    default: return 'Notifikasi'
  }
}

const matchesSearch = (notif, query) => {
  if (!query) return true

  const haystack = [
    notif.title,
    notif.description,
    notif.message,
    notif.nomor,
    notif.nomor_kerjasama,
    notif.kerjasama_judul,
    notif.proses_judul,
    getKindLabel(notif.kind),
  ]
    .filter(Boolean)
    .join(' ')
    .toLowerCase()

  return haystack.includes(query.toLowerCase())
}

const getCount = (type) => {
  if (type === 'semua') return notifications.value.length
  if (type === 'pengajuan_kerjasama') return notifications.value.filter(n => n.kind === 'pengajuan_kerjasama').length
  if (type === 'revisi_mitra') return notifications.value.filter(n => n.kind === 'revisi_mitra').length
  if (type === 'akan_berakhir') return notifications.value.filter(n => n.status_group === 'akan_berakhir').length
  if (type === 'sudah_berakhir') return notifications.value.filter(n => n.status_group === 'sudah_berakhir').length
  return 0
}

const filteredNotifications = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  return notifications.value.filter((notif) => {
    const tabMatch = (() => {
      switch (activeTab.value) {
        case 'pengajuan_kerjasama': return notif.kind === 'pengajuan_kerjasama'
        case 'revisi_mitra': return notif.kind === 'revisi_mitra'
        case 'akan_berakhir': return notif.status_group === 'akan_berakhir'
        case 'sudah_berakhir': return notif.status_group === 'sudah_berakhir'
        default: return true
      }
    })()

    return tabMatch && matchesSearch(notif, query)
  })
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

// ✅ UPDATE: Navigasi ke DetailNotifAdmin.vue
const viewDetail = (id) => {
  router.visit(`/admin/notifikasi/${id}`)
  // Atau pakai named route: router.visit(route('admin.notifications.show', { id }))
}
</script>
