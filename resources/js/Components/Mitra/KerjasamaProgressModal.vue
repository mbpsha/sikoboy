<script setup>
import { ref, watch } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  isOpen:        { type: Boolean, default: false },
  kerjasamaNama: { type: String,  default: '' },
  kerjasamaId:   { type: [Number, String], default: null },
  items:         { type: Array,   default: () => [] },
})

defineEmits(['close'])

const progressItems = ref([])
watch(() => props.items, (v) => { progressItems.value = v || [] }, { immediate: true })

// File input & state per item index
const selectedFiles  = ref({})
const isUploading    = ref({})
const fileInputRefs  = ref({})
const uploadedFiles  = ref({}) // lokasi_file returned after successful upload

const setFileRef = (el, idx) => { if (el) fileInputRefs.value[idx] = el }

const handleFileSelect = (e, idx) => {
  const file = e.target.files?.[0]
  if (file) selectedFiles.value = { ...selectedFiles.value, [idx]: file }
}

const doUpload = async (idx) => {
  const file = selectedFiles.value[idx]
  if (!file || !props.kerjasamaId) return

  isUploading.value = { ...isUploading.value, [idx]: true }
  try {
    const fd    = new FormData()
    fd.append('file', file)
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
    const res   = await fetch(`/mitra/kerjasama/${props.kerjasamaId}/revisi`, {
      method: 'POST', body: fd, credentials: 'same-origin',
      headers: token ? { 'X-CSRF-TOKEN': token } : {},
    })
    if (!res.ok) throw new Error()
    const data = await res.json()
    // Store uploaded file path so the UI updates without a full reload
    uploadedFiles.value = { ...uploadedFiles.value, [idx]: {
      name: file.name,
      lokasi_file: data.lokasi_file ?? null,
    }}
    delete selectedFiles.value[idx]
    await Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Dokumen revisi berhasil diupload.', timer: 2000, showConfirmButton: false })
  } catch {
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal upload. Coba lagi.' })
  } finally {
    isUploading.value = { ...isUploading.value, [idx]: false }
  }
}

const getIcon = (item) => {
  const t = (item.title || '').toLowerCase()
  if (t.includes('diterima') || t.includes('selesai') || t.includes('ditandatangani'))
    return { bg: 'bg-green-500', symbol: '✓' }
  if (t.includes('ditolak'))  return { bg: 'bg-red-500',    symbol: '✕' }
  if (t.includes('revisi'))   return { bg: 'bg-orange-400', symbol: '!' }
  return { bg: 'bg-green-500', symbol: '✓' }
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">

      <!-- Header -->
      <div class="px-6 pt-5 pb-4 border-b border-gray-100 flex items-start justify-between shrink-0">
        <div>
          <h2 class="text-base font-bold text-gray-800">Progres Kerjasama</h2>
          <p class="text-sm text-gray-400 mt-0.5 truncate max-w-sm">{{ kerjasamaNama }}</p>
        </div>
        <button @click="$emit('close')"
          class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="px-6 py-5 overflow-y-auto flex-1">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">Status Tracking</p>

        <!-- Empty -->
        <div v-if="!progressItems.length" class="text-center py-12">
          <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <p class="text-sm text-gray-400">Belum ada proses yang dicatat.</p>
        </div>

        <!-- Timeline -->
        <div v-else class="relative">
          <div class="absolute left-[15px] top-8 bottom-8 w-px bg-gray-200"></div>

          <div v-for="(item, idx) in progressItems" :key="item.id ?? idx" class="relative flex gap-4 mb-4 last:mb-0">

            <!-- Hidden file input per item -->
            <input
              :ref="el => setFileRef(el, idx)"
              type="file" accept=".pdf" class="hidden"
              @change="(e) => handleFileSelect(e, idx)"
            />

            <!-- Dot -->
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 z-10 shadow-sm"
              :class="getIcon(item).bg">
              {{ getIcon(item).symbol }}
            </div>

            <!-- Card -->
            <div class="flex-1 bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">

              <!-- Card header -->
              <div class="px-4 pt-3 pb-2 border-b border-gray-50">
                <h4 class="text-sm font-semibold text-gray-800">{{ item.title ?? '—' }}</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Selesai pada {{ item.tanggal || '—' }}</p>
              </div>

              <!-- Card body -->
              <div class="px-4 py-3 space-y-3">

                <!-- Catatan -->
                <div>
                  <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Catatan :</p>
                  <p :class="[
                    'text-xs rounded-lg px-3 py-2 leading-relaxed',
                    item.file ? 'bg-orange-50 text-orange-700' : 'bg-gray-50 text-gray-600'
                  ]">
                    {{ item.catatan || 'Tidak ada catatan' }}
                  </p>
                </div>

                <!-- File dari admin — selalu tampil jika ada -->
                <div v-if="item.file">
                  <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Lampiran Dokumen</p>
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">
                      <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
                      Dari Admin
                    </span>
                    <a :href="'/storage/' + item.file" target="_blank"
                      class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700 font-medium hover:bg-blue-100 transition">
                      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                      </svg>
                      <span class="truncate max-w-[180px]">{{ item.file.split('/').pop() }}</span>
                      <span class="text-blue-400 text-[10px] font-bold shrink-0">Download</span>
                    </a>
                  </div>
                </div>

                <!-- Upload revisi mitra — HANYA muncul jika admin sudah upload file -->
                <div v-if="item.file" class="pt-2 border-t border-gray-100">
                  <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">
                    Upload Dokumen Revisi dari Mitra
                  </p>

                  <!-- Sudah upload sebelumnya (dari server) -->
                  <div v-if="item.file_mitra"
                    class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-100 rounded-lg">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full shrink-0">
                      <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4-1.3 4-4s-1.3-4-4-4-4 1.3-4 4 1.3 4 4 4zm0 2c-2.7 0-8 1.3-8 4v1h16v-1c0-2.7-5.3-4-8-4z"/></svg>
                      Dari Mitra
                    </span>
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <span class="text-xs text-green-700 font-medium flex-1 truncate">
                      {{ item.file_mitra.split('/').pop() }}
                    </span>
                    <a :href="'/storage/' + item.file_mitra" target="_blank"
                      class="text-green-600 text-[10px] font-bold hover:underline shrink-0">Lihat</a>
                  </div>

                  <!-- Baru saja diupload di sesi ini (sebelum reload) -->
                  <div v-else-if="uploadedFiles[idx]"
                    class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-100 rounded-lg">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full shrink-0">
                      <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4-1.3 4-4s-1.3-4-4-4-4 1.3-4 4 1.3 4 4 4zm0 2c-2.7 0-8 1.3-8 4v1h16v-1c0-2.7-5.3-4-8-4z"/></svg>
                      Dari Mitra
                    </span>
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <span class="text-xs text-green-700 font-medium flex-1 truncate">
                      {{ uploadedFiles[idx].name }}
                    </span>
                    <a v-if="uploadedFiles[idx].lokasi_file" :href="'/storage/' + uploadedFiles[idx].lokasi_file" target="_blank"
                      class="text-green-600 text-[10px] font-bold hover:underline shrink-0">Lihat</a>
                  </div>

                  <!-- Belum upload -->
                  <div v-else>
                    <p class="text-[10px] text-gray-400 mb-2">Selesai pada -</p>
                    <!-- File sudah dipilih -->
                    <div v-if="selectedFiles[idx]"
                      class="flex items-center gap-2 mb-2 px-3 py-1.5 bg-green-50 border border-green-100 rounded-lg">
                      <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                      </svg>
                      <span class="text-xs text-green-700 font-medium flex-1 truncate">{{ selectedFiles[idx].name }}</span>
                      <button @click="delete selectedFiles[idx]" class="text-red-400 hover:text-red-600 text-[10px] font-bold shrink-0">✕</button>
                    </div>

                    <!-- Belum pilih file -->
                    <div v-else @click="fileInputRefs[idx]?.click()"
                      class="flex items-center gap-3 px-3 py-2.5 bg-gray-100 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-200 transition mb-2">
                      <svg class="w-5 h-5 text-gray-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                      </svg>
                      <span class="text-xs text-gray-500 font-medium">Pilih File (*PDF)</span>
                    </div>

                    <div class="flex gap-2">
                      <button v-if="selectedFiles[idx]" @click="fileInputRefs[idx]?.click()"
                        class="px-3 py-1.5 text-xs border border-gray-200 text-gray-600 bg-white rounded-lg hover:bg-gray-50 transition">
                        Ganti File
                      </button>
                      <button @click="doUpload(idx)"
                        :disabled="!selectedFiles[idx] || isUploading[idx]"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                        <span v-if="isUploading[idx]" class="animate-spin inline-block w-3 h-3 border border-white border-t-transparent rounded-full"></span>
                        {{ isUploading[idx] ? 'Mengirim...' : 'Upload Sekarang' }}
                      </button>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Oleh -->
              <div v-if="item.penanggung || item.pegawai"
                class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-right">
                <span class="text-[10px] text-gray-400 italic">
                  Oleh : <span class="font-semibold text-gray-500">{{ item.penanggung || item.pegawai }}</span>
                </span>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 bg-gray-50">
        <button @click="$emit('close')"
          class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
          Tutup
        </button>
      </div>

    </div>
  </div>
</template>

<style scoped>
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 20px; }
::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>