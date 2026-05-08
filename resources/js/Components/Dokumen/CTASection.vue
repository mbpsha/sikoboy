<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="mt-12 mb-24">
    <div class="bg-[#0C505C] text-white rounded-3xl p-10 shadow-2xl text-center">
      <h3 class="text-2xl font-bold">Siap Mengajukan Kerja Sama?</h3>
      <p class="mt-3 text-sm text-white/90 max-w-2xl mx-auto">*Buat dokumen Anda secara otomatis dan pastikan Anda mengisi form pengajuan Kerja Sama dengan informasi yang dapat dipertanggungjawabkan</p>
      <div class="mt-6">
        <Link :href="route('mitra.pengajuan.step1')" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Ajukan Kerja Sama
        </Link>
      </div>
    </div>
  </div>
</template>
