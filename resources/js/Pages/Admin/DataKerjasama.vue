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
                <td class="py-4 px-4">{{ k.tanggal_selesai ? k.tanggal_selesai : '-' }}</td>
                <td class="py-4 px-4">{{ k.jangka_waktu ?? '-' }}</td>
                <td class="py-4 px-4">
                  <Link :href="route('admin.data-kerjasama.index') + '#/dokumen/' + k.id_kerjasama" class="text-teal-700">lihat</Link>
                </td>
                <td class="py-4 px-4 align-top">
                  <div class="proses-dropdown relative">
                    <div class="proses-list max-w-[220px]">
                      <div v-for="(p, pi) in (k.proses || [])" :key="pi" class="proses-item mb-2">
                        <button @click.prevent="openProcessModal(k, p)" class="w-full text-left px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">{{ p.label || p.title || p.nama_proses }}</button>
                      </div>

                      <div v-if="!(k.proses || []).length" class="text-xs text-gray-400">Belum ada proses.</div>

                      <div class="mt-2">
                        <button @click.prevent="toggleAddForm(k.id_kerjasama)" class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white border text-teal-700 text-sm">
                          <span class="text-xl">+</span> Tambah Proses
                        </button>
                      </div>

                      <div v-if="showAddFormFor[k.id_kerjasama]" class="mt-3 p-3 bg-white border rounded-lg">
                        <input v-model="newProcessForm[k.id_kerjasama].title" placeholder="Contoh : Proses 1 - Revisi Dokumen" class="w-full border rounded px-3 py-2 mb-2 text-sm" />
                        <div class="flex gap-2">
                          <button @click.prevent="addProcess(k)" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded">Tambah</button>
                          <button @click.prevent="cancelAdd(k.id_kerjasama)" class="flex-1 bg-gray-200 px-4 py-2 rounded">Batal</button>
                        </div>
                      </div>
                    </div>
                  </div>
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

        <!-- Process modal (moved inside the single top-level template) -->
        <div v-if="showProcessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6">
            <h3 class="text-lg font-semibold mb-2">Update Proses Kerja sama</h3>
            <div class="text-sm text-gray-600 mb-4">{{ activeKerjasama?.judul }}</div>

            <div class="mb-3">
              <label class="text-sm font-medium">Status : <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs">{{ activeProcess?.title }}</span></label>
            </div>

            <div class="mb-3">
              <label class="text-sm font-medium">Penanggung Jawab</label>
              <input v-model="activeProcess.penanggung" placeholder="Contoh : Admin Bidang Tata Pemerintahan" class="w-full border rounded px-3 py-2" />
            </div>

            <div class="mb-3">
              <label class="text-sm font-medium">Catatan</label>
              <textarea v-model="activeProcess.catatan" class="w-full border rounded px-3 py-2 h-28"></textarea>
            </div>

            <div class="mb-3">
              <label class="text-sm font-medium">Upload Dokumen (PDF)</label>
              <input type="file" @change="onFileSelect" accept="application/pdf" class="w-full" />
            </div>

            <div class="flex justify-end gap-3 mt-4">
              <button @click="closeProcessModal" class="px-4 py-2 rounded bg-gray-200">Batal</button>
              <button @click.prevent="saveProcessUpdate" class="px-4 py-2 rounded bg-teal-600 text-white">Simpan</button>
            </div>
          </div>
        </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, reactive } from 'vue'

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

const showAddFormFor = reactive({})
const newProcessForm = reactive({})

function toggleAddForm(id_kerjasama) {
  showAddFormFor[id_kerjasama] = !showAddFormFor[id_kerjasama]
  if (!newProcessForm[id_kerjasama]) {
    newProcessForm[id_kerjasama] = { title: '' }
  }
}

function cancelAdd(id_kerjasama) {
  showAddFormFor[id_kerjasama] = false
  if (newProcessForm[id_kerjasama]) newProcessForm[id_kerjasama].title = ''
}

// Local modal state for viewing/updating a process
const showProcessModal = ref(false)
const activeProcess = ref(null)
const activeKerjasama = ref(null)

function openProcessModal(k, p) {
  activeKerjasama.value = k
  activeProcess.value = { ...p }
  showProcessModal.value = true
}

function closeProcessModal() {
  showProcessModal.value = false
  activeProcess.value = null
  activeKerjasama.value = null
}

async function addProcess(k) {
  const id = k.id_kerjasama
  const title = (newProcessForm[id].title || '').trim()
  if (!title) return

  // optimistic UI update: push to k.proses array
  k.proses = k.proses || []
  const newP = { id: Date.now(), title, label: title }
  k.proses.unshift(newP)

  // reset form
  newProcessForm[id].title = ''
  showAddFormFor[id] = false

  // attempt to persist to server if route exists
  try {
    const url = route('admin.kerjasama.proses.store', k.id_kerjasama)
    await router.post(url, { title })
    // server may return created process; we leave optimistic item as-is
  } catch (e) {
    // ignore errors — keep optimistic client-side state
  }
}

// file upload + modal save handlers
const fileToUpload = ref(null)
function onFileSelect(e){ fileToUpload.value = e.target.files?.[0] ?? null }

async function saveProcessUpdate(){
  const k = activeKerjasama.value
  const p = activeProcess.value
  if (!k || !p) return

  // update or insert process in k.proses
  k.proses = k.proses || []
  const existing = k.proses.findIndex(x => x.id === p.id || x.id === p.id_kerja)
  const item = { ...p }
  if (existing >= 0) {
    k.proses.splice(existing, 1, item)
  } else {
    k.proses.unshift(item)
  }

  // derive status_persetujuan from process title when applicable
  const title = (p.title || p.label || '').toString().toLowerCase()
  let statusVal = p.title || null
  if (title.includes('diterima') || title.includes('diterima')) {
    statusVal = 'disetujui'
  } else if (title.includes('ditolak')) {
    statusVal = 'ditolak'
  } else {
    // leave the title as a descriptive status so FE shows it
    statusVal = p.title || statusVal
  }

  k.status_persetujuan = statusVal

  // try to persist to server (best-effort)
  try {
    // prefer a named route if available: admin.kerjasama.proses.update
    const url = (() => {
      try { return route('admin.kerjasama.proses.update', [k.id_kerjasama, p.id]) } catch (e) { return null }
    })()

    if (url) {
      const data = { title: p.title, penanggung: p.penanggung ?? null, catatan: p.catatan ?? null }
      await router.put(url, data)
    }
  } catch (e) {
    // ignore server errors — UI already updated optimistically
  }

  // clear file and close modal
  fileToUpload.value = null
  closeProcessModal()
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
