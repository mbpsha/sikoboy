<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import logo from '@/images/logo_byl.png';

const page = usePage()
// Inertia exposes shared props under `props` — check `auth.user` for logged-in user
const isAuthenticated = computed(() => !!(page.props && page.props.auth && page.props.auth.user))

const currentUrl = computed(() => {
  // Inertia page url can be a path or full URL; normalize to a string
  return (page.url ?? (page.props && page.props.value && page.props.value.url) ?? '')
})

const isActive = (path) => {
  if (!path) return false

  // If path is an anchor/hash (starts with '#'), compare against current URL hash
  try {
    const url = new URL(currentUrl.value, window.location.origin)
    if (path.startsWith('#')) {
      return url.hash === path
    }
    // Compare pathname only so query strings or hashes don't break matching
    return url.pathname === path
  } catch (e) {
    // Fallback: simple string comparison
    if (path.startsWith('#')) return currentUrl.value.endsWith(path)
    return currentUrl.value === path
  }
}
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <!-- Left: emblem + authority text -->
      <div class="flex items-center gap-1 rounded-full px-5 py-2" style="background: rgba(49,113,124,0.6);">
        <img :src="logo" alt="Boyolali Logo" class="h-17 w-17 object-contain mr-1" />
        <div class="text-left text-white">
          <div class="text-l font-semibold">Sekretariat Daerah</div>
          <div class="text-l font-medium tracking-wide">Kabupaten Boyolali</div>
        </div>
      </div>

      <!-- Center: pill nav -->
      <div class="hidden lg:flex items-center justify-center flex-1">
        <nav class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg" style="background-color: rgba(23,70,78,0.95);">
          <Link :href="'/'" :class="isActive('/') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Beranda</Link>
          <a href="#about" :class="isActive('#about') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Tentang</a>
          <a href="#peraturan" :class="isActive('#peraturan') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Peraturan</a>
          <Link :href="'/dokumen'" :class="isActive('/dokumen') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Dokumen</Link>
          <Link :href="'/kontak'" :class="isActive('/kontak') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Kontak</Link>
        </nav>
      </div>

      <!-- Right: CTAs when unauthenticated, Portal when authenticated -->
      <div class="flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <!-- pill CTAs with soft background -->
          <div class="flex items-center gap-3 rounded-full px-3 py-1" style="background: rgba(49,113,124,0.6);">
            <Link href="/register" class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-[#BEBDBD]">Daftar</Link>
            <Link href="/login/mitra" class="rounded-full bg-[#0C505C] px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-[#265e63]">Masuk</Link>
          </div>
        </template>
        <template v-else>
          <Link href="/portal-mitra" class="mx-2 flex items-center gap-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h8"/></svg>
            <span>Portal Mitra</span>
          </Link>
        </template>
      </div>
    </div>
  </header>
</template>
