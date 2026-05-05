<template>
  <AdminLayout title="Data Kerjasama">
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

      <!-- Search & Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-3">
          <input
            v-model="local.search"
            @keyup.enter="applyFilters"
            placeholder="Cari berdasarkan mitra atau nama kerjasama..."
            class="flex-1 min-w-[220px] rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition"
          />
          <select v-model="local.tahun" class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600">
            <option value="">Semua Tahun</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
          <select v-model="local.status" class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="segera berakhir">Segera Berakhir</option>
            <option value="berakhir">Berakhir</option>
          </select>
          <button @click="applyFilters" class="bg-teal-700 hover:bg-teal-800 text-white text-sm px-5 py-2.5 rounded-full font-medium transition">
            Filter
          </button>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-teal-700 text-white text-xs uppercase tracking-wide">
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">No</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Tahun</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Mitra</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Judul</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Jenis Kerjasama</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Jenis Dokumen</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Urusan</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Mulai</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Berakhir</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Jangka Waktu</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">File</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Proses</th>
                <th class="py-3 px-4 text-left font-medium">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(k, idx) in kerjasama.data" :key="k.id_kerjasama" class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-500 text-xs">{{ indexOffset + idx + 1 }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tahun }}</td>
                <td class="py-3 px-4 max-w-[130px] truncate font-medium">{{ k.mitra }}</td>
                <td class="py-3 px-4 max-w-[220px] leading-snug">{{ k.judul }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.jenis_kerjasama }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.jenis_dokumen }}</td>
                <td class="py-3 px-4 text-gray-600">{{ k.urusan }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tanggal_mulai || '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tanggal_selesai || '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.jangka_waktu ?? '—' }}</td>
                <td class="py-3 px-4">
                  <Link :href="route('admin.data-kerjasama.index') + '#/dokumen/' + k.id_kerjasama"
                    class="text-teal-700 hover:text-teal-900 font-medium text-xs underline-offset-2 hover:underline">
                    Lihat
                  </Link>
                </td>

                <!-- Proses column -->
                <td class="py-3 px-4 align-top">
                  <div class="space-y-1.5 min-w-[180px]">
                    <button
                      v-for="(p, pi) in (k.proses || [])" :key="pi"
                      @click.prevent="openProcessModal(k, p)"
                      class="w-full text-left px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs text-gray-700 transition"
                    >
                      {{ p.label || p.title || p.nama_proses }}
                    </button>
                    <p v-if="!(k.proses || []).length" class="text-xs text-gray-400 italic">Belum ada proses.</p>
                    <button
                      @click.prevent="toggleAddForm(k.id_kerjasama)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-teal-700 hover:bg-teal-50 text-xs font-medium transition mt-1"
                    >
                      <span class="text-base leading-none">+</span> Tambah Proses
                    </button>
                    <div v-if="showAddFormFor[k.id_kerjasama]" class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
                      <input
                        v-model="newProcessForm[k.id_kerjasama].title"
                        placeholder="Contoh: Proses 1 - Revisi"
                        class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500"
                      />
                      <div class="flex gap-2">
                        <button @click.prevent="addProcess(k)" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Tambah</button>
                        <button @click.prevent="cancelAdd(k.id_kerjasama)" class="flex-1 bg-white border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg transition">Batal</button>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Status column -->
                <td class="py-3 px-4">
                  <span
                    class="inline-block px-2.5 py-1 rounded-full text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': k.status_persetujuan === 'disetujui',
                      'bg-red-100 text-red-800': k.status_persetujuan === 'ditolak',
                      'bg-amber-100 text-amber-800': !['disetujui','ditolak'].includes(k.status_persetujuan),
                    }"
                  >
                    {{ k.status_persetujuan === 'disetujui' ? 'Diterima' : k.status_persetujuan === 'ditolak' ? 'Ditolak' : (k.status_persetujuan ?? 'Proses') }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3.5 flex items-center justify-between border-t border-gray-100">
          <span class="text-xs text-gray-500">Tampilkan {{ kerjasama.per_page }} data / halaman</span>
          <div class="flex gap-2">
            <button
              :disabled="!kerjasama.prev_page_url"
              @click.prevent="goTo(kerjasama.prev_page_url)"
              class="px-4 py-1.5 text-xs border border-gray-200 rounded-lg bg-white hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >← Prev</button>
            <button
              :disabled="!kerjasama.next_page_url"
              @click.prevent="goTo(kerjasama.next_page_url)"
              class="px-4 py-1.5 text-xs border border-gray-200 rounded-lg bg-white hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >Next →</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Process Modal -->
    <Teleport to="body">
      <div v-if="showProcessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
          <div>
            <h3 class="text-base font-semibold text-gray-900">Update Proses Kerjasama</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ activeKerjasama?.judul }}</p>
          </div>

          <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500">Status:</span>
            <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-medium">
              {{ activeProcess?.title }}
            </span>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Penanggung Jawab</label>
            <input
              :value="activeProcess.penanggung"
              readonly
              class="w-full border border-gray-100 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
            <textarea v-model="activeProcess.catatan" rows="4"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition resize-none" />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Upload Dokumen (PDF)</label>
            <input ref="processFileInput" type="file" accept="application/pdf" class="hidden" @change="onFileSelect" />
            <div
              @click="triggerProcessFileInput"
              @dragover.prevent
              @drop.prevent="handleProcessDrop"
              class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-teal-600 transition cursor-pointer"
            >
              <div class="flex flex-col items-center">
                <svg class="w-10 h-10 text-teal-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <p class="font-semibold text-[#17464E] mb-1">Drag & Drop Dokumen Kerjasama (PDF)</p>
                <p class="text-xs text-gray-600 mb-3">atau klik untuk memilih file</p>
                <button type="button" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm">Pilih File</button>
                <p v-if="fileName" class="text-sm text-gray-600 mt-3">✓ {{ fileName }}</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button @click="closeProcessModal" class="px-4 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition">Batal</button>
            <button @click.prevent="saveProcessUpdate" class="px-4 py-2 text-sm rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition">Simpan</button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, reactive } from 'vue'

const props = defineProps({
  kerjasama: Object,
  filters: Object,
})

const page = usePage()
const currentUsername = computed(() => page.props.auth?.user?.username ?? '')

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

const local = ref({
  search: filters.value?.search ?? '',
  tahun:  filters.value?.tahun  ?? '',
  status: filters.value?.status ?? '',
})

const years = computed(() => {
  const now = new Date().getFullYear()
  return Array.from({ length: 6 }).map((_, i) => now - i)
})

function applyFilters() {
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.tahun)  params.tahun  = local.value.tahun
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

const showProcessModal = ref(false)
const activeProcess = ref(null)
const activeKerjasama = ref(null)

function openProcessModal(k, p) {
  activeKerjasama.value = k
  activeProcess.value = {
    ...p,
    penanggung: p.penanggung || currentUsername.value,
  }
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

  k.proses = k.proses || []
  k.proses.unshift({ id: Date.now(), title, label: title })

  newProcessForm[id].title = ''
  showAddFormFor[id] = false

  try {
    await router.post(route('admin.kerjasama.proses.store', k.id_kerjasama), { title })
  } catch (e) {}
}

const fileToUpload = ref(null)
const fileName = ref('')
const processFileInput = ref(null)

function onFileSelect(e) {
  const f = e.target.files?.[0] ?? null
  fileToUpload.value = f
  fileName.value = f ? f.name : ''
}

function triggerProcessFileInput() {
  processFileInput.value?.click()
}

function handleProcessDrop(e) {
  const file = e.dataTransfer.files?.[0] ?? null
  if (file && file.type === 'application/pdf') {
    fileToUpload.value = file
    fileName.value = file.name
  }
}

async function saveProcessUpdate() {
  const k = activeKerjasama.value
  const p = activeProcess.value
  if (!k || !p) return

  k.proses = k.proses || []
  const existing = k.proses.findIndex(x => x.id === p.id)
  existing >= 0 ? k.proses.splice(existing, 1, { ...p }) : k.proses.unshift({ ...p })

  const title = (p.title || '').toLowerCase()
  k.status_persetujuan = title.includes('diterima') ? 'disetujui'
    : title.includes('ditolak') ? 'ditolak'
    : p.title || null

  try {
    const url = route('admin.kerjasama.proses.update', [k.id_kerjasama, p.id])
    await router.put(url, { title: p.title, penanggung: p.penanggung ?? null, catatan: p.catatan ?? null })
  } catch (e) {}

  fileToUpload.value = null
  closeProcessModal()
}
</script>