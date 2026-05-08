<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import logo from '@/images/logo_byl.png';

const page = usePage()

// Inertia exposes shared props under `props` — check `auth.user` for logged-in user
const isAuthenticated = computed(() => !!(page.props && page.props.auth && page.props.auth.user))

// Determine active nav item based on current route
const activeNav = computed(() => {
  const currentRoute = page.url
  if (currentRoute === '/' || currentRoute.startsWith('/?')) return 'beranda'
  if (currentRoute.startsWith('#about') || currentRoute.includes('about')) return 'tentang'
  if (currentRoute.startsWith('#contact') || currentRoute.includes('contact')) return 'kontak'
  if (currentRoute.startsWith('/dokumen')) return 'dokumen'
  return 'beranda'
})

const isNavActive = (nav) => activeNav.value === nav
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
          <a href="/" :class="['nav-link mx-2 rounded-full px-4 py-1 text-sm font-semibold', isNavActive('beranda') ? 'nav-link-active bg-white text-[#17464E]' : 'text-white/90 hover:text-white']">Beranda</a>
          <a href="#about" :class="['nav-link mx-2 px-4 py-1 text-sm font-semibold rounded-full', isNavActive('tentang') ? 'nav-link-active bg-white text-[#17464E]' : 'text-white/90 hover:text-white']">Tentang</a>
          <a href="#contact" :class="['nav-link mx-2 px-4 py-1 text-sm font-semibold rounded-full', isNavActive('kontak') ? 'nav-link-active bg-white text-[#17464E]' : 'text-white/90 hover:text-white']">Kontak</a>
          <a href="/dokumen" :class="['nav-link mx-2 px-4 py-1 text-sm font-semibold rounded-full', isNavActive('dokumen') ? 'nav-link-active bg-white text-[#17464E]' : 'text-white/90 hover:text-white']">Dokumen</a>
        </nav>
      </div>

      <!-- Right: CTAs when unauthenticated, Portal when authenticated -->
      <div class="flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <!-- pill CTAs with soft background -->
          <div class="flex items-center gap-3 rounded-full px-3 py-1" style="background: rgba(49,113,124,0.06);">
            <Link href="/register" class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-[#265e63]">Daftar</Link>
            <Link href="/login/mitra" class="rounded-full bg-[#31717C] px-5 py-2 text-sm font-semibold text-white shadow-md ring-2 ring-white hover:bg-[#265e63]">Masuk</Link>
          </div>
        </template>
        <template v-else>
          <Link href="/portal-mitra" class="flex items-center gap-2 rounded-full bg-[#17464E] px-4 py-2 text-white font-medium shadow">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h8"/></svg>
            <span>Portal Mitra</span>
          </Link>
        </template>
      </div>
    </div>
  </header>
</template>

<style scoped>
.nav-link {
  transition: all 400ms cubic-bezier(0.34, 1.56, 0.64, 1);
  background-color: transparent;
  color: rgba(255, 255, 255, 0.9);
}

.nav-link:hover {
  color: white;
  transform: scale(1.05);
}

.nav-link-active {
  background-color: white;
  color: #17464E;
}
</style>
