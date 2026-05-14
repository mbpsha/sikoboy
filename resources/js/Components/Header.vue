<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3"; // ← Import router ditambahkan
import logo from "@/images/logo_byl.png";

const page = usePage();
const isAuthenticated = computed(() => !!page.props?.auth?.user)
const userRole = computed(() => page.props?.auth?.user?.role ?? null)

// Normalized role (lowercase) to avoid case mismatches from backend
const userRoleNorm = computed(() => String(page.props?.auth?.user?.role ?? '').toLowerCase())

const isDev = typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.DEV

// 🔔 Notification state
const showNotificationDropdown = ref(false);

const notifications = computed(() => {
  return page.props?.notifications || [];
});

const notificationsCount = computed(() => {
  const count = page.props?.notifications_count || 0;
  return count > 0 ? count : 0;
});

const toggleNotificationDropdown = () => {
  showNotificationDropdown.value = !showNotificationDropdown.value;
};

const closeNotificationDropdown = () => {
  showNotificationDropdown.value = false;
};

// 🔔 Handler ketika notifikasi diklik → REDIRECT ke ListNotif.vue
const handleNotificationClick = (notification) => {
  console.log('[Header] Notification clicked, redirecting to list:', notification);
  closeNotificationDropdown();
  
  // Redirect ke halaman list notifikasi
  // Jika route helper error, fallback ke URL manual
  try {
    router.get(route('mitra.notifications'));
  } catch (e) {
    router.get('/mitra/notifications');
  }
};

// Close dropdown when clicking outside
onMounted(() => {
  const handleClickOutside = (event) => {
    if (showNotificationDropdown.value && !event.target.closest('.notification-container')) {
      closeNotificationDropdown();
    }
  };
  
  document.addEventListener('click', handleClickOutside);
  
  // Cleanup listener
  return () => {
    document.removeEventListener('click', handleClickOutside);
  };
  
  try {
    if (import.meta.env && import.meta.env.DEV) {
      console.log('[Header] page.props.auth:', page.props?.auth)
      console.log('[Header] userRole:', userRole.value)
      console.log('[Header] userRoleNorm:', userRoleNorm.value)
      console.log('[Header] isAuthenticated:', isAuthenticated.value)
      console.log('[Header] notifications:', notifications.value)
    }
  } catch (e) {
    console.error('[Header] Debug error:', e)
  }
})

const currentUrl = computed(() => {
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

  try {
    const url = new URL(currentUrl.value, window.location.origin)
    if (path.startsWith('#')) {
      return url.hash === path
    }
    return url.pathname === path
  } catch (e) {
    if (path.startsWith('#')) return currentUrl.value.endsWith(path)
    return currentUrl.value === path
  }
}

// Safe route helpers with fallbacks
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
            <!-- 🔔 NOTIFICATION BELL -->
            <div class="relative notification-container">
              <button 
                @click.stop="toggleNotificationDropdown"
                class="relative p-2 rounded-full bg-[#2f6f73]/60 hover:bg-[#2f6f73] transition-colors"
                title="Notifikasi"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- Badge -->
                <span 
                  v-if="notificationsCount > 0"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse"
                >
                  {{ notificationsCount > 9 ? '9+' : notificationsCount }}
                </span>
              </button>

              <!-- Dropdown Notifications -->
              <div 
                v-if="showNotificationDropdown" 
                class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl z-50 overflow-hidden"
              >
                <div class="bg-[#2f6f73] px-4 py-3">
                  <h3 class="text-white font-semibold">Notifikasi</h3>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                  <div v-if="notificationsCount === 0" class="p-4 text-center text-gray-500 text-sm">
                    Tidak ada notifikasi
                  </div>
                  
                  <div 
                    v-for="(notif, index) in notifications" 
                    :key="index"
                    class="p-4 border-b border-gray-100 hover:bg-yellow-50 transition-colors cursor-pointer"
                    @click="handleNotificationClick(notif)"
                  >
                    <div class="flex gap-3">
                      <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ notif.title }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ notif.message }}</p>
                        <p class="text-xs text-yellow-600 mt-2 font-medium">
                          {{ notif.days_left === null || notif.days_left === undefined ? 'Baru' : `${notif.days_left} hari lagi` }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-2 text-center">
                  <Link :href="route('mitra.notifications')" class="text-sm text-[#2f6f73] font-medium hover:underline">
                    Lihat Semua
                  </Link>
                </div>
              </div>
            </div>
            <!-- 🔔 END NOTIFICATION BELL -->

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
