<script setup>
import { computed, ref, watch, onUnmounted } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  badge: { type: String, default: 'PDF' },
  href: { type: String, default: '#' },
  preview: { type: String, default: null },
  buttonText: { type: String, default: 'Lihat Dokumen' },
  fileSize: { type: String, default: '' },
})

const showPreview = ref(false)
const closeBtn = ref(null)
const zoomLevel = ref(100)
const currentPage = ref(1)
const totalPages = ref(5)
let lastActive = null

const normalizeAndAbs = (pathValue) => {
  if (!pathValue) return null
  if (pathValue.startsWith('http://') || pathValue.startsWith('https://')) return pathValue

  let path = pathValue.replace(/\\/g, '/')
  if (!path.startsWith('/')) path = '/' + path

  path = path.replace(/^\/public\/docs\//i, '/storage/docs/')
  path = path.replace(/^\/docs\//i, '/storage/docs/')

  const encoded = path.split('/').map(encodeURIComponent).join('/').replace(/%2F/g, '/')
  return window.location.origin + encoded
}

const previewSrc = computed(() => normalizeAndAbs(props.preview || props.href))
const downloadSrc = computed(() => normalizeAndAbs(props.href || props.preview))

const filename = computed(() => {
  const source = downloadSrc.value || previewSrc.value || ''
  const parts = source.split('/')
  const last = parts.pop() || ''
  const nameOnly = last.split('?')[0]
  try {
    return decodeURIComponent(nameOnly)
  } catch (error) {
    return nameOnly
  }
})

const openPreview = () => {
  if (!previewSrc.value) return
  showPreview.value = true
}

const closePreview = () => {
  showPreview.value = false
}

const handleZoom = (direction) => {
  if (direction === 'in' && zoomLevel.value < 200) zoomLevel.value += 10
  if (direction === 'out' && zoomLevel.value > 50) zoomLevel.value -= 10
}

const handlePageChange = (direction) => {
  if (direction === 'next' && currentPage.value < totalPages.value) currentPage.value++
  if (direction === 'prev' && currentPage.value > 1) currentPage.value--
}

const downloadPDF = async () => {
  try {
    const response = await fetch(downloadSrc.value)
    if (!response.ok) throw new Error('Gagal download file')

    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = filename.value

    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Download error:', error)
    alert('Gagal mengunduh file. Silahkan coba lagi.')
  }
}

const onKeyDown = (event) => {
  if (event.key === 'Escape' && showPreview.value) closePreview()
  if (event.key === 'ArrowRight') handlePageChange('next')
  if (event.key === 'ArrowLeft') handlePageChange('prev')
}

watch(showPreview, (val) => {
  if (val) {
    lastActive = document.activeElement
    setTimeout(() => closeBtn.value && closeBtn.value.focus(), 50)
    document.addEventListener('keydown', onKeyDown)
  } else {
    document.removeEventListener('keydown', onKeyDown)
    lastActive && lastActive.focus && lastActive.focus()
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', onKeyDown)
})
</script>

<template>
  <!-- Card Container -->
  <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-4 sm:p-6 shadow-md border border-teal-100 w-full overflow-hidden hover:shadow-lg transition-shadow">
    <!-- Title -->
    <h4 class="font-bold text-center text-teal-900 text-base md:text-lg break-words px-2">
      {{ title }}
    </h4>

    <!-- Badge -->
    <div class="flex justify-center mt-2">
      <span class="text-xs inline-flex items-center gap-1.5 text-teal-700 font-semibold bg-white px-3 py-1 rounded-full shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        {{ badge }}
      </span>
    </div>

    <!-- Description -->
    <p v-if="description" class="text-sm text-center text-gray-600 mt-3 px-4 line-clamp-2" v-html="description"></p>

    <!-- Button -->
    <div class="mt-5 flex justify-center">
      <button 
        @click="openPreview"
        class="inline-flex items-center gap-2.5 bg-gradient-to-r from-teal-600 to-teal-500 text-white px-5 md:px-7 py-2.5 rounded-full shadow-md hover:shadow-lg hover:scale-105 transition-all font-semibold text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{ buttonText }}
      </button>
    </div>
  </div>

  <!-- Preview Modal -->
  <teleport to="body" v-if="showPreview">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 py-8" @click.self="closePreview">
      <div 
        class="w-full max-w-4xl h-[88vh] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden"
        @click.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <h3 class="text-sm font-semibold text-slate-800 truncate">{{ title }}</h3>
              <p v-if="fileSize" class="text-[11px] text-slate-400 mt-0.5">{{ fileSize }}</p>
              <p v-else class="text-[11px] text-slate-400 mt-0.5">Dokumen</p>
            </div>
          </div>

          <button
            ref="closeBtn"
            @click="closePreview"
            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition shrink-0 ml-3"
            aria-label="Tutup dokumen"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Preview Area -->
        <div class="flex-1 bg-slate-100 overflow-hidden">
          <iframe
            :src="previewSrc ? `${previewSrc}#toolbar=1&navpanes=0` : ''"
            class="w-full h-full border-0"
            loading="lazy"
          ></iframe>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-100 px-5 py-3 flex items-center justify-between shrink-0 bg-gray-50">
          <p class="text-xs text-slate-400 truncate max-w-xs hidden sm:block">{{ filename }}</p>
          <div class="flex gap-2 ml-auto">
            <button
              @click="downloadPDF"
              class="inline-flex items-center gap-1.5 rounded-lg bg-[#0C505C] hover:bg-[#0a424b] px-4 py-2 text-xs font-semibold text-white transition"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
              </svg>
              Unduh PDF
            </button>

            <button
              @click="closePreview"
              class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<style scoped>
/* Custom scrollbar untuk preview area */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>