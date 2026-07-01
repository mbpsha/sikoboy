<template>
  <AdminLayout title="Notifikasi Kerjasama">

    <!-- ================= MAIN CONTENT ================= -->
    <main class="flex-1">

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
            :class="notif.status === 'expired'
              ? 'border-l-red-500'
              : (notif.status === 'cancelled'
                ? 'border-l-slate-500'
                : (notif.status === 'info' ? 'border-l-blue-400' : 'border-l-orange-400'))"
          >
            <div class="flex items-start gap-4">
              <!-- Badge Type -->
              <div class="flex-shrink-0">
                <span
                  class="px-3 py-1 text-xs font-bold rounded-full"
                  :class="notif.kind === 'pengajuan_kerjasama'
                    ? 'bg-emerald-100 text-emerald-700'
                    : notif.kind === 'revisi_mitra'
                      ? 'bg-amber-100 text-amber-700'
                      : notif.kind === 'dibatalkan'
                        ? 'bg-slate-200 text-slate-700'
                        : 'bg-sky-100 text-sky-700'"
                >
                  {{ getKindLabel(notif.kind) }}
                </span>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                  <h3 class="font-semibold" :class="notif.status === 'expired'
                    ? 'text-red-600'
                    : (notif.status === 'cancelled'
                      ? 'text-slate-700'
                      : (notif.status === 'info' ? 'text-blue-600' : 'text-orange-600'))">
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
                      : (notif.status === 'cancelled'
                        ? 'bg-slate-200 text-slate-700'
                        : (notif.status === 'info' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'))"
                    >
                      {{ notif.status === 'expired'
                        ? 'Expired'
                        : (notif.status === 'cancelled'
                          ? 'Dibatalkan'
                          : (notif.status === 'info' ? 'Info' : 'Aktif')) }}
                    </span>
                  </div>
                </div>

              <!-- Countdown -->
              <div class="text-right flex-shrink-0 ml-auto">
                <div
                  class="w-20 h-20 rounded-lg flex flex-col items-center justify-center text-white font-bold"
                  :class="notif.status === 'expired'
                    ? 'bg-red-500'
                    : (notif.status === 'cancelled'
                      ? 'bg-slate-500'
                      : (notif.status === 'info' ? 'bg-blue-500' : 'bg-orange-500'))"
                >
                  <span class="text-2xl">{{ notif.status === 'cancelled' ? '✕' : (notif.daysLeft ?? '!') }}</span>
                  <span class="text-xs">{{ notif.status === 'cancelled' ? 'Batal' : (notif.daysLeft === null ? 'Baru' : 'Hari') }}</span>
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

  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

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
  { label: 'Dibatalkan', value: 'dibatalkan' },
  { label: 'Akan Berakhir', value: 'akan_berakhir' },
  { label: 'Sudah Berakhir', value: 'sudah_berakhir' },
]

const getKindLabel = (kind) => {
  switch (kind) {
    case 'pengajuan_kerjasama': return 'Pengajuan'
    case 'revisi_mitra': return 'Revisi Mitra'
    case 'dibatalkan': return 'Dibatalkan'
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
  if (type === 'dibatalkan') return notifications.value.filter(n => n.kind === 'dibatalkan').length
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
        case 'dibatalkan': return notif.kind === 'dibatalkan'
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