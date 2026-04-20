<script setup>
import { Link } from '@inertiajs/vue3'
const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  badge: { type: String, default: 'DOCX' },
  href: { type: String, default: '#' },
  buttonText: { type: String, default: 'Lihat Dokumen Kerja Sama' },
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
      <Link :href="href" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-4 md:px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{ buttonText }}
      </Link>
    </div>
  </div>
</template>
