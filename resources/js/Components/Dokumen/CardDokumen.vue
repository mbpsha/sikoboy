<script setup>
import { ref, watch, onUnmounted } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  badge: { type: String, default: 'DOCX' },
  href: { type: String, default: '#' },
  preview: { type: String, default: null },
  buttonText: { type: String, default: 'Lihat Dokumen Kerja Sama' },
})

const showModal = ref(false)
const selectedFile = ref(null)
const closeBtn = ref(null)
let lastActive = null

const onKeyDown = (e) => {
  if (e.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

watch(showModal, (val) => {
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

import { computed } from 'vue'

const selectedType = ref(null)
const originalFile = ref(null)

const filename = computed(() => {
  if (!originalFile.value) return ''
  const parts = originalFile.value.split('/')
  const last = parts.pop() || ''
  // strip querystring and decode percent-encoding for display
  const nameOnly = last.split('?')[0]
  try {
    return decodeURIComponent(nameOnly)
  } catch (e) {
    return nameOnly
  }
})

const fileExt = computed(() => {
  const fn = filename.value || ''
  return fn.split('.').pop()?.toLowerCase() || ''
})

const normalizeAndAbs = (p) => {
  if (!p) return null
  if (p.startsWith('http://') || p.startsWith('https://')) return p
  // replace backslashes, ensure leading slash
  let path = p.replace(/\\/g, '/')
  if (!path.startsWith('/')) path = '/' + path
  // normalize common public paths to the storage-backed public path
  // convert 'public/docs/...' or 'docs/...' to '/storage/docs/...'
  path = path.replace(/^\/public\/docs\//i, '/storage/docs/')
  path = path.replace(/^\/docs\//i, '/storage/docs/')
  // encode URI components but keep slashes
  const parts = path.split('/').map(encodeURIComponent)
  // first element will be empty because path starts with /
  const encoded = parts.join('/').replace(/%2F/g, '/')
  return window.location.origin + encoded
}

const handleClick = async () => {
  if (!props.href || props.href === '#') return
  const ext = props.href.split('.').pop()?.toLowerCase() || ''
  // absolute URL for download / viewers (normalized)
  const abs = normalizeAndAbs(props.href)
  originalFile.value = abs
  // debug: log computed values for troubleshooting
  console.debug('[CardDokumen] props.href ->', props.href)
  console.debug('[CardDokumen] props.preview ->', props.preview)
  console.debug('[CardDokumen] normalized abs ->', abs)

  if (ext === 'pdf') {
    selectedFile.value = abs
    selectedType.value = 'pdf'
    showModal.value = true
    return
  }

  if (ext === 'docx' || ext === 'doc' || ext === 'xlsx' || ext === 'pptx') {
    // If server provided a preview path use it (preferred)
    if (props.preview) {
      const previewAbs = normalizeAndAbs(props.preview)
      console.debug('[CardDokumen] using props.preview ->', previewAbs)
      selectedFile.value = previewAbs
      selectedType.value = 'pdf'
      showModal.value = true
      return
    }

    // Prefer a same-name PDF preview if it exists (same directory, .pdf extension)
    try {
      const previewPdf = normalizeAndAbs(abs.replace(/\.docx$|\.doc$|\.xlsx$|\.pptx$/i, '.pdf'))
      console.debug('[CardDokumen] checking HEAD for previewPdf ->', previewPdf)
      const resp = await fetch(previewPdf, { method: 'HEAD' })
      console.debug('[CardDokumen] HEAD response for previewPdf ->', resp && resp.status)
      if (resp && resp.ok) {
        console.debug('[CardDokumen] previewPdf exists, using ->', previewPdf)
        selectedFile.value = previewPdf
        selectedType.value = 'pdf'
        showModal.value = true
        return
      }
    } catch (e) {
      // fallthrough to office viewer if HEAD fails (CORS or network)
    }

    // fallback: use Microsoft Office online viewer to embed office documents
    selectedFile.value = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(abs)}`
    selectedType.value = 'office'
    showModal.value = true
    return
  }

  window.open(abs, '_blank')
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
}
</script>

<template>
  <div class="bg-[#D4E4E8] rounded-2xl p-4 sm:p-6 md:p-8 shadow-lg w-full">
    <h4 class="font-bold text-center text-[#0C505C] text-base md:text-lg">{{ title }}</h4>

    <div class="flex justify-center mt-2">
      <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        {{ badge }}
      </span>
    </div>

    <p v-if="description" class="text-sm sm:text-xs text-center text-slate-600 mt-3 px-4" v-html="description"></p>

    <div class="mt-4 flex justify-center">
      <button @click="handleClick" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-4 md:px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{ buttonText }}
      </button>
    </div>

    <!-- MODAL FOR PDF: match Peraturan style when previewing PDF -->
    <div v-if="showModal && selectedType === 'pdf'" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
      <div class="bg-white w-full max-w-5xl h-[80vh] rounded-xl shadow-lg relative">

        <button ref="closeBtn" @click="closeModal" class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl z-10" aria-label="Tutup dokumen">✕</button>

        <div class="p-5 md:p-6">
          <h3 class="text-lg font-semibold text-[#17464E] truncate">{{ filename }}</h3>
        </div>

        <div class="px-4 pb-6">
          <div class="bg-white rounded-xl overflow-hidden">
            <iframe :src="selectedFile" class="w-full h-[65vh] rounded-xl"></iframe>
          </div>
        </div>

        <div class="absolute bottom-4 left-4">
          <a v-if="originalFile" :href="originalFile" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-3 py-2 rounded-full shadow" :download="filename">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download as .DOCX
          </a>
        </div>
      </div>
    </div>

    <!-- MODAL FOR OFFICE VIEWER: use header + iframe (fallback) -->
    <div v-if="showModal && selectedType === 'office'" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
      <div class="bg-white w-full max-w-5xl h-[80vh] rounded-xl shadow-lg relative">

        <button ref="closeBtn" @click="closeModal" class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl z-10" aria-label="Tutup dokumen">✕</button>

        <div class="border-b px-6 py-3 bg-slate-50">
          <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="text-sm text-slate-700">
              <strong class="mr-2">{{ filename }}</strong>
              <span class="text-xs text-slate-500">{{ fileExt.toUpperCase() }} preview</span>
            </div>
            <div class="text-xs text-slate-500 italic">Click Download if preview fails</div>
          </div>
        </div>

        <div class="w-full h-[calc(100%-56px)] rounded-b-xl overflow-hidden">
          <iframe :src="selectedFile" class="w-full h-full"></iframe>
        </div>

        <div class="absolute bottom-4 left-4">
          <a v-if="originalFile" :href="originalFile" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-3 py-2 rounded-full shadow" :download="filename">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download as .DOCX
          </a>
        </div>

      </div>
    </div>
  </div>
</template>
