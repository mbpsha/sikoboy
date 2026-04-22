<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

// PAKAI CARA INI BIAR LOGO TETAP MUNCUL DI RESOURCES
const logo = new URL('@/images/logo_byl.png', import.meta.url).href;

const page = usePage();

const isAuthenticated = computed(() => 
  !!(page.props && page.props.auth && page.props.auth.user)
);

const currentUrl = computed(() => {
  try {
    if (page && page.url) return String(page.url);
    const props = page && page.props && page.props.value;
    if (props && props.url) return String(props.url);
  } catch (e) {}
  if (typeof window !== 'undefined') return window.location.href;
  return '';
});

const isActive = (path) => {
  if (!path) return false;
  try {
    const url = new URL(currentUrl.value, window.location.origin);
    if (path.startsWith('#')) {
      return url.hash === path;
    }
    return url.pathname === path;
  } catch (e) {
    if (path.startsWith('#')) return currentUrl.value.endsWith(path);
    return currentUrl.value === path;
  }
};

// Helper class biar rapi
const activeClass = 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold';
const normalClass = 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors';
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      
      <div class="flex items-center gap-1 rounded-full px-5 py-2" style="background: rgba(49,113,124,0.6); backdrop-filter: blur(4px);">
        <img :src="logo" alt="Boyolali Logo" class="h-12 w-12 object-contain mr-1" />
        <div class="text-left text-white">
          <div class="text-[10px] uppercase tracking-tighter opacity-80">Sekretariat Daerah</div>
          <div class="text-sm font-bold tracking-wide uppercase leading-tight">Kabupaten Boyolali</div>
        </div>
      </div>

      <div class="hidden lg:flex items-center justify-center flex-1">
        <nav class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg" style="background-color: rgba(23,70,78,0.95);">
          <Link href="/" :class="isActive('/') ? activeClass : normalClass">Beranda</Link>
          <Link href="/about" :class="isActive('/about') ? activeClass : normalClass">Tentang</Link>
          <Link href="/peraturan" :class="isActive('/peraturan') ? activeClass : normalClass">Peraturan</Link>
          <Link href="/dokumen" :class="isActive('/dokumen') ? activeClass : normalClass">Dokumen</Link>
          <Link href="/kontak" :class="isActive('/kontak') ? activeClass : normalClass">Kontak</Link>
        </nav>
      </div>

      <div class="flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <div class="flex items-center gap-3 rounded-full px-3 py-1" style="background: rgba(49,113,124,0.6); backdrop-filter: blur(4px);">
            <Link href="/register" class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-gray-200">
              Daftar
            </Link>
            <Link href="/login/mitra" class="rounded-full bg-[#0C505C] px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-[#1a5a63]">
              Masuk
            </Link>
          </div>
        </template>

        <template v-else>

          <Link href="/portal-mitra" class="mx-2 flex items-center gap-2 rounded-full bg-[#0C505C] text-white px-4 py-2 text-sm font-semibold shadow-md hover:bg-[#0a4a4e]">
                <div class="bg-[#2f6f73] p-2 rounded-xl shadow-sm flex items-center justify-center w-10 h-10 shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="white">
                    <path d="M7 19h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2V9H7v2Zm4 0h2V9h-2v2Zm4 0h2V9h-2v2ZM3 21V3h18v18H3Zm2-2h14V5H5v14Z"/>
                  </svg>
                </div>

            <span>Portal Mitra</span>
          </Link>
        </template>
      </div>
    </div>
  </header>
</template>