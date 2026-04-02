<script setup>
import logo from '@/images/logo_byl.png';
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <!-- Left: emblem + authority text -->
      <div class="flex items-center gap-3">
        <img :src="logo" alt="Boyolali Logo" class="h-21 w-17 object-contain" />
        <div class="text-left text-[#17464E]">
          <div class="text-l font-semibold">Sekretariat Daerah</div>
          <div class="text-l font-medium tracking-wide">Kabupaten Boyolali</div>
        </div>
      </div>

      <!-- Center: pill nav -->
      <div class="hidden lg:flex items-center justify-center flex-1">
        <nav class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg" style="background-color: rgba(23,70,78,0.95);">
          <a href="/" class="mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold">Beranda</a>
          <a href="#about" class="mx-2 px-4 py-1 text-sm text-white/90">Tentang</a>
          <a href="#contact" class="mx-2 px-4 py-1 text-sm text-white/90">Kontak</a>
          <a href="/dokumen" class="mx-2 px-4 py-1 text-sm text-white/90">Dokumen</a>
        </nav>
      </div>

      <!-- Right: CTAs -->
      <div class="flex items-center gap-3">
        <div class="rounded-full bg-white px-2 py-1 flex items-center gap-2 shadow-sm">
          <a href="/register" class="rounded-full bg-white px-4 py-1 text-sm font-medium text-[#31717C]">Daftar</a>
          <a href="/login" class="rounded-full bg-[#31717C] px-4 py-1 text-sm font-medium text-white ring-2 ring-white">Masuk</a>
        </div>
      </div>
    </div>
  </header>
</template>
