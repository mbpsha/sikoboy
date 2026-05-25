<script setup>
import { computed, ref, watch, onUnmounted } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  badge: { type: String, default: 'PDF' },
  href: { type: String, default: '#' },
  preview: { type: String, default: null },
  buttonText: { type: String, default: 'Lihat Dokumen Kerja Sama' },
})

const showPreview = ref(false)
const closeBtn = ref(null)
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

const onKeyDown = (event) => {
  if (event.key === 'Escape' && showPreview.value) {
    closePreview()
  }
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
      <button @click="openPreview" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-4 md:px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{ buttonText }}
      </button>
    </div>

    <div v-if="showPreview" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8">
      <div class="w-full max-w-5xl h-[80vh] bg-white rounded-2xl shadow-2xl flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
          <h3 class="text-lg font-semibold text-slate-800 truncate">
            {{ props.title }}
          </h3>

          <button
            ref="closeBtn"
            @click="closePreview"
            class="rounded-full bg-slate-100 px-3 py-1 text-xl font-semibold text-slate-600 transition hover:bg-slate-200"
            aria-label="Tutup dokumen"
          >
            ✕
          </button>
        </div>

        <iframe
          :src="previewSrc"
          class="flex-1 border-0"
        ></iframe>

        <div class="border-t border-slate-200 px-6 py-3 flex justify-end gap-3">
          <a
            v-if="downloadSrc"
            :href="downloadSrc"
            target="_blank"
            class="inline-flex items-center rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-700"
            :download="filename"
          >
            Unduh PDF
          </a>

          <button
            @click="closePreview"
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
