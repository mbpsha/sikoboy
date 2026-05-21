<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen:        { type: Boolean, default: false },
  kerjasamaNama: { type: String,  default: '' },
  kerjasamaId:   { type: [Number, String], default: null },
  items:         { type: Array,   default: () => [] },
})

defineEmits(['close'])

const progressItems = ref([])
watch(() => props.items, (v) => { progressItems.value = v || [] }, { immediate: true })

const getIcon = (item) => {
  const t = (item.title || '').toLowerCase()
  if (t.includes('diterima') || t.includes('selesai') || t.includes('ditandatangani'))
    return { bg: 'bg-green-500', symbol: '✓' }
  if (t.includes('ditolak'))  return { bg: 'bg-red-500',    symbol: '✕' }
  if (t.includes('revisi'))   return { bg: 'bg-orange-400', symbol: '!' }
  return { bg: 'bg-green-500', symbol: '✓' } // default hijau + ceklis
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

            <!-- Dot — selalu ceklis hijau kecuali ada kondisi khusus -->
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

                <div v-if="item.file" class="pt-2 border-t border-gray-100">
                  <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Lampiran Dokumen</p>
                  <a :href="'/storage/' + item.file" target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700 font-medium hover:bg-blue-100 transition">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                    <span class="truncate max-w-[180px]">{{ item.file.split('/').pop() }}</span>
                  </a>
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