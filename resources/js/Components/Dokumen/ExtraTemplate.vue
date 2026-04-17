<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="bg-[#D4E4E8] rounded-3xl p-8 shadow-xl">
    <h3 class="text-2xl font-bold text-[#0C505C] text-center">Kerangka Acuan Kerja</h3>
    <div class="mt-6 flex justify-center">
      <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        DOCX
      </span>
    </div>
    <div class="mt-4 flex justify-center">
      <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
        Lihat Dokumen Kerja Sama
      </Link>
    </div>
  </div>
</template>
