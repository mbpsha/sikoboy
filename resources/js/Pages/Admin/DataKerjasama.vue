<template>
  <AdminLayout title="Data Kerjasama">
    <div class="max-w-7xl mx-auto">
      <!-- Header / Search -->
      <div class="mt-6 bg-white p-6 rounded-xl shadow-md">
        <div class="flex items-center gap-4">
          <input v-model="local.search" @keyup.enter="applyFilters" placeholder="Cari Berdasarkan Mitra atau Nama Kerjasama..." class="flex-1 rounded-full px-4 py-3 border" />
          <select v-model="local.tahun" class="rounded-full px-3 py-2">
            <option value="">Semua Tahun</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
          <select v-model="local.status" class="rounded-full px-3 py-2">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="segera berakhir">Segera Berakhir</option>
            <option value="berakhir">Berakhir</option>
          </select>
          <button @click="applyFilters" class="bg-teal-700 text-white px-4 py-2 rounded-full">Filter</button>
        </div>
      </div>

      <!-- Table card -->
      <div class="mt-6 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 overflow-x-auto">
          <table class="min-w-full table-auto text-sm table-lines">
            <thead>
              <tr class="bg-teal-700 text-white">
                <th class="py-3 px-4">No</th>
                <th class="py-3 px-4">Tahun</th>
                <th class="py-3 px-4">Mitra</th>
                <th class="py-3 px-4">Judul</th>
                <th class="py-3 px-4">Jenis Kerjasama</th>
                <th class="py-3 px-4">Jenis Dokumen</th>
                <th class="py-3 px-4">Urusan</th>
                <th class="py-3 px-4">Mulai</th>
                <th class="py-3 px-4">Berakhir</th>
                <th class="py-3 px-4">Jangka Waktu</th>
                <th class="py-3 px-4">File</th>
                <th class="py-3 px-4">Proses</th>
                <th class="py-3 px-4">Status Persetujuan</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(k, idx) in kerjasama.data" :key="k.id_kerjasama" class="border-b">
                <td class="py-4 px-4">{{ indexOffset + idx + 1 }}</td>
                <td class="py-4 px-4">{{ k.tahun }}</td>
                <td class="py-4 px-4 max-w-xs truncate">{{ k.mitra }}</td>
                <td class="py-4 px-4 max-w-2xl">{{ k.judul }}</td>
                <td class="py-4 px-4">{{ k.jenis_kerjasama }}</td>
                <td class="py-4 px-4">{{ k.jenis_dokumen }}</td>
                <td class="py-4 px-4">{{ k.urusan }}</td>
                <td class="py-4 px-4">{{ k.tanggal_mulai ? k.tanggal_mulai : '-' }}</td>
                <td class="py-4 px-4">{{ k.tanggal_berakhir ? k.tanggal_berakhir : '-' }}</td>
                <td class="py-4 px-4">{{ k.jangka_waktu ?? '-' }}</td>
                <td class="py-4 px-4">
                  <Link :href="route('admin.data-kerjasama.index') + '#/dokumen/' + k.id_kerjasama" class="text-teal-700">lihat</Link>
                </td>
                <td class="py-4 px-4">
                  <button class="bg-emerald-600 text-white px-3 py-1 rounded-md">Update Proses</button>
                </td>
                <td class="py-4 px-4">
                  <span v-if="k.status_persetujuan === 'disetujui'" class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs">Diterima</span>
                  <span v-else-if="k.status_persetujuan === 'ditolak'" class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs">Ditolak</span>
                  <span v-else class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs">{{ k.status_persetujuan ?? 'Proses' }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-6 flex items-center justify-between">
          <div class="text-sm text-gray-600">Tampilkan {{ kerjasama.per_page }} / Halaman</div>
          <div class="flex items-center gap-2">
            <button :disabled="!kerjasama.prev_page_url" @click.prevent="goTo(kerjasama.prev_page_url)" class="px-3 py-1 bg-white rounded-md">Prev</button>
            <button :disabled="!kerjasama.next_page_url" @click.prevent="goTo(kerjasama.next_page_url)" class="px-3 py-1 bg-white rounded-md">Next</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  kerjasama: Object,
  filters: Object,
})

const kerjasama = computed(() => props.kerjasama ?? {
  data: [],
  per_page: 15,
  prev_page_url: null,
  next_page_url: null,
  current_page: 1,
})

const filters = computed(() => props.filters ?? {})

const indexOffset = computed(() => (
  kerjasama.value.current_page
    ? ((kerjasama.value.current_page - 1) * kerjasama.value.per_page)
    : 0
))

// Local filter state for the UI
const local = ref({
  search: filters.value.search || '',
  tahun: filters.value.tahun || '',
  status: filters.value.status || '',
})

const years = computed(() => {
  const now = new Date().getFullYear()
  return Array.from({ length: 6 }).map((_, i) => now - i)
})

function applyFilters() {
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.tahun) params.tahun = local.value.tahun
  if (local.value.status) params.status = local.value.status
  router.visit(route('admin.data-kerjasama.index'), { method: 'get', data: params })
}

function goTo(url) {
  if (!url) return
  router.visit(url, { preserveState: false })
}
</script>

<style scoped>
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Table header separators to match design */
.table-lines thead th { border-right: 1px solid rgba(255,255,255,0.18); }
.table-lines thead th:last-child { border-right: none; }
.table-lines tbody td { border-bottom: 1px solid rgba(15,23,42,0.06); }
</style>
