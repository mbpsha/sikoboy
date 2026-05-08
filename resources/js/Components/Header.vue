<script setup>
import { computed, onMounted } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import logo from "@/images/logo_byl.png";

const page = usePage();
const isAuthenticated = computed(() => !!page.props?.auth?.user)
const userRole = computed(() => page.props?.auth?.user?.role ?? null)

// Normalized role (lowercase) to avoid case mismatches from backend
const userRoleNorm = computed(() => String(page.props?.auth?.user?.role ?? '').toLowerCase())

const isDev = typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.DEV

onMounted(() => {
  try {
    if (import.meta.env && import.meta.env.DEV) {
      // Dev-only debugging to inspect auth props when clicking fails
      console.log('[Header] page.props.auth:', page.props?.auth)
      console.log('[Header] userRole:', userRole.value)
      console.log('[Header] userRoleNorm:', userRoleNorm.value)
      console.log('[Header] isAuthenticated:', isAuthenticated.value)
    }
  } catch (e) {
    console.error('[Header] Debug error:', e)
  }
})

const currentUrl = computed(() => {
  // Prefer Inertia page url if available, otherwise fall back to page props url or window location
  try {
    if (page && page.url) return String(page.url)
    const props = page && page.props
    if (props && props.url) return String(props.url)
  } catch (e) {}
  if (typeof window !== 'undefined') return window.location.href
  return ''
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

// Safe route helpers with fallbacks in case Ziggy route() isn't available
const registerHref = computed(() => {
  try { return route('register') } catch (e) { return '/register' }
});

const loginHref = computed(() => {
  try { return route('login') } catch (e) { return '/login' }
});

const portalHref = computed(() => {
  try {
    if (userRoleNorm.value === 'mitra') return route('mitra.profile.index')
    if (userRoleNorm.value === 'admin') return route('admin.dashboard')
    return route('home')
  } catch (e) {
    if (userRoleNorm.value === 'mitra') return '/mitra/profile'
    if (userRoleNorm.value === 'admin') return '/admin/dashboard'
    return '/'
  }
});

const portalLabel = computed(() => {
  return userRoleNorm.value === 'mitra' ? 'Portal Mitra' : (userRoleNorm.value === 'admin' ? 'Dashboard' : 'Portal')
});
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
          <Link href="/" :class="isActive('/') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Beranda</Link>
          <Link href="/about" :class="isActive('/about') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Tentang</Link>
          <Link href="/peraturan" :class="isActive('/peraturan') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Peraturan</Link>
          <Link href="/dokumen" :class="isActive('/dokumen') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Dokumen</Link>
          <Link href="/kontak" :class="isActive('/kontak') ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' : 'mx-2 px-4 py-1 text-sm text-white/90'">Kontak</Link>
        </nav>
      </div>

      <!-- Right: CTAs when unauthenticated, Portal when authenticated -->
      <div class="flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <!-- pill CTAs with soft background -->
          <div class="flex items-center gap-3 rounded-full px-3 py-1" style="background: rgba(49,113,124,0.6);">
              <Link :href="registerHref" class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-[#BEBDBD]">Daftar</Link>
              <Link :href="loginHref" class="rounded-full bg-[#0C505C] px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-[#265e63]">Masuk</Link>
          </div>
        </template>
        <template v-else>
            <Link :href="portalHref" class="mx-2 flex items-center gap-2 rounded-full bg-[#0C505C] text-white px-4 py-2 text-sm font-semibold shadow-md hover:bg-[#0a4a4e]">
                <div class="bg-[#2f6f73] p-2 rounded-xl shadow-sm flex items-center justify-center w-10 h-10 shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="white">
                    <path d="M7 19h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2V9H7v2Zm4 0h2V9h-2v2Zm4 0h2V9h-2v2ZM3 21V3h18v18H3Zm2-2h14V5H5v14Z"/>
                  </svg>
                </div>
            <span>{{ portalLabel }}</span>
          </Link>
        </template>
      </div>
    </div>
  </header>
</template>