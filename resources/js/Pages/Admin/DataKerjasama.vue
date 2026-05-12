<template>
  <AdminLayout title="Ajuan Kerjasama">
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

      <!-- Header with Add Button -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Ajuan Kerjasama</h2>
          <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua data kerjasama</p>
        </div>
        <div class="relative">
          <button
            @click="showAddMenu = !showAddMenu"
            class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kerjasama
          </button>
          <div
            v-if="showAddMenu"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden z-10"
          >
            <Link href="/admin/riwayat-kerjasama/pemerintah" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition">
              ➕ Pemerintah
            </Link>
            <Link href="/admin/riwayat-kerjasama/mitra" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition border-t">
              ➕ Mitra
            </Link>
            <Link href="/admin/riwayat-kerjasama/gabungan" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition border-t">
              ➕ Gabungan
            </Link>
          </div>
        </div>
      </div>

      <!-- Search & Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-3">
          <input
            v-model="local.search"
            placeholder="Cari berdasarkan mitra atau nama kerjasama..."
            class="flex-1 min-w-[220px] rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition"
          />
          <select v-model="local.tahun" @change="applyFilters" class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600">
            <option value="">Semua Tahun</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
          <select v-model="local.status" @change="applyFilters" class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="segera berakhir">Segera Berakhir</option>
            <option value="berakhir">Berakhir</option>
          </select>
          <button @click="applyFilters" class="bg-teal-700 hover:bg-teal-800 text-white text-sm px-5 py-2.5 rounded-full font-medium transition">
            Filter
          </button>
          <button v-if="local.search || local.tahun || local.status" @click="resetAllFilters" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm px-5 py-2.5 rounded-full font-medium transition">
            Reset
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
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1">
                    <span>Tahun</span>
                    <button v-if="local.tahun" @click="local.tahun = ''; applyFilters()" class="text-yellow-300 hover:text-yellow-100" title="Clear filter">✕</button>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 group relative cursor-pointer">
                    <span>Mitra</span>
                    <button class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- MITRA FILTER DROPDOWN -->
                    <div class="hidden group-hover:block absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200 max-w-xs">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueMitra" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.mitra.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.mitra.push(val)
                            } else {
                              columnFilters.mitra = columnFilters.mitra.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.mitra = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Judul</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 group relative cursor-pointer">
                    <span>Jenis Kerjasama</span>
                    <button class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- JENIS KERJASAMA FILTER DROPDOWN -->
                    <div class="hidden group-hover:block absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueJenisKerjasama" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.jenis_kerjasama.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.jenis_kerjasama.push(val)
                            } else {
                              columnFilters.jenis_kerjasama = columnFilters.jenis_kerjasama.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.jenis_kerjasama = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 group relative cursor-pointer">
                    <span>Jenis Dokumen</span>
                    <button class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- JENIS DOKUMEN FILTER DROPDOWN -->
                    <div class="hidden group-hover:block absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueJenisDokumen" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.jenis_dokumen.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.jenis_dokumen.push(val)
                            } else {
                              columnFilters.jenis_dokumen = columnFilters.jenis_dokumen.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.jenis_dokumen = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 group relative cursor-pointer">
                    <span>Urusan</span>
                    <button class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- URUSAN FILTER DROPDOWN -->
                    <div class="hidden group-hover:block absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueUrusan" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.urusan.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.urusan.push(val)
                            } else {
                              columnFilters.urusan = columnFilters.urusan.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.urusan = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Mulai</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Berakhir</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Jangka Waktu</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">File</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Pembiayaan</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">No. Surat Mitra</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Proses</th>
                <th class="py-3 px-4 text-left font-medium">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(k, idx) in filteredKerjasama" :key="k.id_kerjasama" class="hover:bg-gray-50 transition-colors">
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
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.pembiayaan ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.nomor_suratM ?? k.nomor_surat ?? '—' }}</td>

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
                        <button @click.prevent="finishAddProcess(k)" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Selesai Proses</button>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Status column -->
                <td class="py-3 px-4">
                  <span
                    class="inline-block px-2.5 py-1 rounded-full text-xs font-medium"
                    :class="statusBadgeClass(k)"
                  >
                    {{ k.status_display ?? (k.status_persetujuan === 'disetujui' ? 'Diterima' : (k.status_persetujuan ?? 'Proses')) }}
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
import { ref, computed, reactive, watch } from 'vue'
import Swal from 'sweetalert2'

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

const filters   = computed(() => props.filters ?? {})
const indexOffset = computed(() =>
  kerjasama.value.current_page
    ? (kerjasama.value.current_page - 1) * kerjasama.value.per_page
    : 0
)

const local = ref({
  search: filters.value?.search ?? '',
  tahun:  filters.value?.tahun  ?? '',
  status: filters.value?.status ?? '',
})

// Column filters state
const columnFilters = ref({
  mitra: [],
  jenis_kerjasama: [],
  jenis_dokumen: [],
  urusan: [],
})

// Get unique values for each column
const uniqueMitra = computed(() => {
  const values = kerjasama.value.data.map(item => item.mitra)
  return [...new Set(values)].filter(Boolean).sort()
})

const uniqueJenisKerjasama = computed(() => {
  const values = kerjasama.value.data.map(item => item.jenis_kerjasama)
  return [...new Set(values)].filter(Boolean).sort()
})

const uniqueJenisDokumen = computed(() => {
  const values = kerjasama.value.data.map(item => item.jenis_dokumen)
  return [...new Set(values)].filter(Boolean).sort()
})

const uniqueUrusan = computed(() => {
  const values = kerjasama.value.data.map(item => item.urusan)
  return [...new Set(values)].filter(Boolean).sort()
})

// Filtered data based on column filters
const filteredKerjasama = computed(() => {
  let data = [...kerjasama.value.data]

  if (columnFilters.value.mitra.length > 0) {
    data = data.filter(item => columnFilters.value.mitra.includes(item.mitra))
  }

  if (columnFilters.value.jenis_kerjasama.length > 0) {
    data = data.filter(item => columnFilters.value.jenis_kerjasama.includes(item.jenis_kerjasama))
  }

  if (columnFilters.value.jenis_dokumen.length > 0) {
    data = data.filter(item => columnFilters.value.jenis_dokumen.includes(item.jenis_dokumen))
  }

  if (columnFilters.value.urusan.length > 0) {
    data = data.filter(item => columnFilters.value.urusan.includes(item.urusan))
  }

  return data
})

// Add menu state
const showAddMenu = ref(false)

let searchTimeout = null

const years = computed(() => {
  const now = new Date().getFullYear()
  return Array.from({ length: 6 }).map((_, i) => now - i)
})

function statusBadgeClass(k) {
  if (k?.status_display && String(k.status_display).startsWith('Proses')) {
    return 'bg-blue-100 text-blue-800'
  }
  if (k?.status_persetujuan === 'disetujui') {
    return 'bg-green-100 text-green-800'
  }
  return 'bg-amber-100 text-amber-800'
}

// Auto-search dengan debounce
watch(() => local.value.search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
})

function applyFilters() {
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.tahun)  params.tahun  = local.value.tahun
  if (local.value.status) params.status = local.value.status
  router.visit(route('admin.data-kerjasama.index'), { method: 'get', data: params })
}

function applyColumnFilter(column, value) {
  if (!value) return
  local.value.search = value
  columnFilters.value[column] = value
  applyFilters()
}

function resetAllFilters() {
  local.value.search = ''
  local.value.tahun = ''
  local.value.status = ''
  columnFilters.value = { mitra: '', jenis_kerjasama: '', jenis_dokumen: '', urusan: '' }
  router.visit(route('admin.data-kerjasama.index'), { method: 'get', data: {} })
}

function goTo(url) {
  if (!url) return
  router.visit(url, { preserveState: false })
}

// ─── Add process form ────────────────────────────────────────────────────────
const showAddFormFor = reactive({})
const newProcessForm = reactive({})

function toggleAddForm(id) {
  showAddFormFor[id] = !showAddFormFor[id]
  if (!newProcessForm[id]) newProcessForm[id] = { title: '' }
}

function cancelAdd(id) {
  showAddFormFor[id] = false
  if (newProcessForm[id]) newProcessForm[id].title = ''
}

// Tambah proses baru — kirim ke server, reload data
function addProcess(k) {
  const id    = k.id_kerjasama
  const title = (newProcessForm[id]?.title || '').trim()
  if (!title) return

  // Add a temporary process entry locally. Persist when user opens it and saves.
  if (!k.proses) k.proses = []
  k.proses.push({
    id: null,
    label: title,
    title: title,
    catatan: '',
    penanggung: currentUsername.value,
    __temp: true,
  })

  newProcessForm[id].title = ''
  showAddFormFor[id] = false
}

// Selesaikan semua proses dan simpan ke riwayat
async function finishAddProcess(k) {
  const confirmed = await Swal.fire({
    title: 'Selesaikan Proses?',
    text: 'Data akan disimpan ke riwayat kerjasama.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, selesaikan',
    cancelButtonText: 'Batal',
  }).then(r => r.isConfirmed)

  if (!confirmed) return

  const id    = k.id_kerjasama
  const title = (newProcessForm[id]?.title || '').trim() || 'Proses Selesai'

  const fd = new FormData()
  fd.append('title',       title)
  fd.append('catatan',     'Semua proses telah diselesaikan.')
  fd.append('penanggung',  currentUsername.value)
  fd.append('is_finished', '1')   // flag untuk controller simpan ke riwayat

  router.post(
    route('admin.data-kerjasama.proses.store', id),
    fd,
    {
      preserveScroll: true,
      onSuccess: () => {
        newProcessForm[id].title = ''
        showAddFormFor[id] = false
      },
      onError: (e) => console.error('Gagal selesai semua proses:', e),
    }
  )
}

// ─── Process Modal ────────────────────────────────────────────────────────────
const showProcessModal  = ref(false)
const activeProcess     = ref(null)
const activeKerjasama   = ref(null)
const fileToUpload      = ref(null)
const fileName          = ref('')
const processFileInput  = ref(null)

function openProcessModal(k, p) {
  activeKerjasama.value = k
  activeProcess.value   = {
    ...p,
    penanggung: p.penanggung || currentUsername.value,
    // If this is a temporary process (just a title), keep catatan empty
    catatan:    p.id ? (p.catatan || '') : '',
  }
  showProcessModal.value = true
  // reset file setiap buka modal
  fileToUpload.value = null
  fileName.value     = ''
}

function closeProcessModal() {
  showProcessModal.value = false
  activeProcess.value    = null
  activeKerjasama.value  = null
  fileToUpload.value     = null
  fileName.value         = ''
}

function onFileSelect(e) {
  const f = e.target.files?.[0] ?? null
  fileToUpload.value = f
  fileName.value     = f ? f.name : ''
}

function triggerProcessFileInput() {
  processFileInput.value?.click()
}

function handleProcessDrop(e) {
  const file = e.dataTransfer.files?.[0] ?? null
  if (file?.type === 'application/pdf') {
    fileToUpload.value = file
    fileName.value     = file.name
  }
}

// Simpan update proses (catatan + file) — pakai POST + FormData
async function saveProcessUpdate() {
  const k = activeKerjasama.value
  const p = activeProcess.value
  if (!k || !p) return

  const fd = new FormData()
  fd.append('title',      p.title      ?? '')
  fd.append('penanggung', p.penanggung ?? currentUsername.value)
  fd.append('catatan',    p.catatan    ?? '')
  if (fileToUpload.value) {
    fd.append('file', fileToUpload.value)
  }

  // If this process is not yet persisted, call store (POST). Otherwise use update (PUT).
  if (!p.id) {
    router.post(
      route('admin.data-kerjasama.proses.store', k.id_kerjasama),
      fd,
      {
        preserveScroll: true,
        onSuccess: () => {
          fileToUpload.value = null
          closeProcessModal()
        },
        onError: (e) => console.error('Gagal simpan proses baru:', e),
      }
    )
  } else {
    fd.append('_method', 'PUT')
    router.post(
      route('admin.data-kerjasama.proses.update', [k.id_kerjasama, p.id]),
      fd,
      {
        preserveScroll: true,
        onSuccess: () => {
          fileToUpload.value = null
          closeProcessModal()
        },
        onError: (e) => console.error('Gagal simpan proses:', e),
      }
    )
  }
}

// Selesai & Simpan ke Riwayat dari modal
async function endProcess() {
  const k = activeKerjasama.value
  const p = activeProcess.value
  if (!k || !p) return

  const confirmed = await Swal.fire({
    title: 'Selesaikan Proses?',
    text: 'Data akan disimpan ke riwayat kerjasama.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, selesaikan',
    cancelButtonText: 'Batal',
  }).then(r => r.isConfirmed)

  if (!confirmed) return

  const fd = new FormData()
  fd.append('title',       p.title      ?? 'Selesai')
  fd.append('penanggung',  p.penanggung ?? currentUsername.value)
  fd.append('catatan',     p.catatan    ?? '')
  fd.append('is_finished', '1')
  if (fileToUpload.value) {
    fd.append('file', fileToUpload.value)
  }
  // If the process is new, call store; otherwise update
  if (!p.id) {
    router.post(
      route('admin.data-kerjasama.proses.store', k.id_kerjasama),
      fd,
      {
        preserveScroll: true,
        onSuccess: () => {
          fileToUpload.value = null
          closeProcessModal()
        },
        onError: (e) => console.error('Gagal selesai proses baru:', e),
      }
    )
  } else {
    fd.append('_method', 'PUT')
    router.post(
      route('admin.data-kerjasama.proses.update', [k.id_kerjasama, p.id]),
      fd,
      {
        preserveScroll: true,
        onSuccess: () => {
          fileToUpload.value = null
          closeProcessModal()
        },
        onError: (e) => console.error('Gagal selesai proses:', e),
      }
    )
  }
}
</script>
