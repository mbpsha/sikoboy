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

// 📱 Mobile menu state
const showMobileMenu = ref(false);

const rawNotifications = computed(() => page.props?.notifications || [])

// local state: closed notifications (persisted in localStorage) to hide from header popup
const closedMitraNotifications = ref([])

const notifications = computed(() => {
  return rawNotifications.value.filter(n => !closedMitraNotifications.value.includes(n.id))
})

// show count for visible (not-closed) notifications in header
const notificationsCount = computed(() => notifications.value.length)

const toggleNotificationDropdown = () => {
  showNotificationDropdown.value = !showNotificationDropdown.value;
};

const closeNotificationDropdown = () => {
  showNotificationDropdown.value = false;
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
};

const closeMobileMenu = () => {
  showMobileMenu.value = false;
};

// 🔔 Handler ketika notifikasi diklik → REDIRECT ke ListNotif.vue
const handleNotificationClick = (notification) => {
  console.log('[Header] Notification clicked, redirecting to list:', notification);
  // hide this notification from header popup (but keep in full list)
  if (!closedMitraNotifications.value.includes(notification.id)) {
    closedMitraNotifications.value.push(notification.id)
    try { localStorage.setItem('closed_mitra_notifications', JSON.stringify(closedMitraNotifications.value)) } catch (e) {}
  }
  closeNotificationDropdown();
  
  // Redirect ke halaman list notifikasi
  // Jika route helper error, fallback ke URL manual
  try {
    router.get(route('mitra.notifications'));
  } catch (e) {
    router.get('/mitra/notifications');
  }
};

const markAllAsRead = () => {
  const ids = rawNotifications.value.map(n => n.id)
  closedMitraNotifications.value = Array.from(new Set([...closedMitraNotifications.value, ...ids]))
  try { localStorage.setItem('closed_mitra_notifications', JSON.stringify(closedMitraNotifications.value)) } catch (e) {}
  showNotificationDropdown.value = false
}

// Close dropdown when clicking outside
onMounted(() => {
  const handleClickOutside = (event) => {
    if (showNotificationDropdown.value && !event.target.closest('.notification-container')) {
      closeNotificationDropdown();
    }
    if (showMobileMenu.value && !event.target.closest('.mobile-menu-container')) {
      closeMobileMenu();
    }
  };

  document.addEventListener('click', handleClickOutside);

  // load closed mitra notifications from localStorage
  try {
    const stored = localStorage.getItem('closed_mitra_notifications')
    if (stored) closedMitraNotifications.value = JSON.parse(stored)
  } catch (e) {
    closedMitraNotifications.value = []
  }

  // Cleanup listener
  return () => {
    document.removeEventListener('click', handleClickOutside);
  };
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
  <header class="fixed inset-x-0 top-0 z-50 bg-white/10 backdrop-blur-md border-b border-white/10">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-3 sm:px-6 py-3 sm:py-4">
      <!-- Left: emblem + authority text -->
      <div class="flex items-center gap-1 rounded-full px-2 sm:px-5 py-2" style="background: rgba(49,113,124,0.6);">
        <img :src="logo" alt="Boyolali Logo" class="h-10 sm:h-14 md:h-17 w-10 sm:w-14 md:w-17 object-contain" />
        <div class="text-left text-white hidden sm:block">
          <div class="font-semibold text-xs sm:text-sm md:text-base leading-tight">Sekretariat Daerah</div>
          <div class="font-medium tracking-wide text-xs sm:text-sm md:text-base leading-tight">Kabupaten Boyolali</div>
        </div>
      </div>

      <!-- Center: pill nav (Desktop only lg:) -->
      <div class="hidden lg:flex items-center justify-center flex-1">
        <nav class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg" style="background-color: rgba(23,70,78,0.95);">
          <Link href="/" :class="isActive('/') ? 'mx-1 md:mx-2 rounded-full bg-white text-[#17464E] px-3 md:px-4 py-1 text-xs md:text-sm font-semibold' : 'mx-1 md:mx-2 px-3 md:px-4 py-1 text-xs md:text-sm text-white/90 hover:text-white transition'">Beranda</Link>
          <Link href="/about" :class="isActive('/about') ? 'mx-1 md:mx-2 rounded-full bg-white text-[#17464E] px-3 md:px-4 py-1 text-xs md:text-sm font-semibold' : 'mx-1 md:mx-2 px-3 md:px-4 py-1 text-xs md:text-sm text-white/90 hover:text-white transition'">Tentang</Link>
          <Link href="/peraturan" :class="isActive('/peraturan') ? 'mx-1 md:mx-2 rounded-full bg-white text-[#17464E] px-3 md:px-4 py-1 text-xs md:text-sm font-semibold' : 'mx-1 md:mx-2 px-3 md:px-4 py-1 text-xs md:text-sm text-white/90 hover:text-white transition'">Peraturan</Link>
          <Link href="/dokumen" :class="isActive('/dokumen') ? 'mx-1 md:mx-2 rounded-full bg-white text-[#17464E] px-3 md:px-4 py-1 text-xs md:text-sm font-semibold' : 'mx-1 md:mx-2 px-3 md:px-4 py-1 text-xs md:text-sm text-white/90 hover:text-white transition'">Dokumen</Link>
          <Link href="/kontak" :class="isActive('/kontak') ? 'mx-1 md:mx-2 rounded-full bg-white text-[#17464E] px-3 md:px-4 py-1 text-xs md:text-sm font-semibold' : 'mx-1 md:mx-2 px-3 md:px-4 py-1 text-xs md:text-sm text-white/90 hover:text-white transition'">Kontak</Link>
        </nav>
      </div>

      <!-- Right: CTAs when unauthenticated, Portal when authenticated + Mobile menu -->
      <div class="flex items-center gap-1 sm:gap-3">
        <!-- 📱 Mobile menu button (visible on lg:hidden) -->
        <button 
          @click.stop="toggleMobileMenu"
          class="lg:hidden p-2 rounded-full bg-[#2f6f73]/60 hover:bg-[#2f6f73] transition-colors"
          title="Menu"
        >
          <svg v-if="!showMobileMenu" xmlns="http://www.w3.org/2000/svg" class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <template v-if="!isAuthenticated">
          <!-- pill CTAs with soft background (Desktop) -->
          <div class="hidden sm:flex items-center gap-2 md:gap-3 rounded-full px-2 md:px-3 py-1" style="background: rgba(49,113,124,0.6);">
              <Link :href="registerHref" class="rounded-full bg-white text-[#17464E] px-3 md:px-5 py-1 sm:py-2 text-xs sm:text-sm font-semibold shadow-sm hover:bg-[#BEBDBD] transition">Daftar</Link>
              <Link :href="loginHref" class="rounded-full bg-[#0C505C] px-3 md:px-5 py-1 sm:py-2 text-xs sm:text-sm font-semibold text-white shadow-md hover:bg-[#265e63] transition">Masuk</Link>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl z-50 overflow-hidden"
              >
                <div class="bg-[#2f6f73] px-4 py-3">
                  <h3 class="text-white font-semibold text-sm">Notifikasi</h3>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                  <div v-if="notificationsCount === 0" class="p-4 text-center text-gray-500 text-xs sm:text-sm">
                    Tidak ada notifikasi
                  </div>
                  
                  <div 
                    v-for="(notif, index) in notifications" 
                    :key="index"
                    class="p-3 sm:p-4 border-b border-gray-100 hover:bg-yellow-50 transition-colors cursor-pointer"
                    @click="handleNotificationClick(notif)"
                  >
                    <div class="flex gap-2 sm:gap-3">
                      <div class="flex-shrink-0">
                        <div class="w-8 sm:w-10 h-8 sm:h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 sm:w-5 h-4 sm:h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-gray-800 break-words">{{ notif.title }}</p>
                        <p class="text-xs text-gray-600 mt-1 break-words">{{ notif.message }}</p>
                        <p class="text-xs text-yellow-600 mt-2 font-medium">
                          {{ notif.days_left === null || notif.days_left === undefined ? 'Baru' : `${notif.days_left} hari lagi` }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-2 text-center">
                  <Link :href="route('mitra.notifications')" class="text-xs sm:text-sm text-[#2f6f73] font-medium hover:underline">
                    Lihat Semua
                  </Link>
                </div>
              </div>
            </div>
            <!-- 🔔 END NOTIFICATION BELL -->

            <Link :href="portalHref" class="mx-1 sm:mx-2 flex items-center gap-1 sm:gap-2 rounded-full bg-[#0C505C] text-white px-2 sm:px-4 py-2 text-xs sm:text-sm font-semibold shadow-md hover:bg-[#0a4a4e] transition">
                <div class="bg-[#2f6f73] p-1.5 sm:p-2 rounded-lg sm:rounded-xl shadow-sm flex items-center justify-center w-7 sm:w-8 md:w-10 h-7 sm:h-8 md:h-10 shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 sm:w-4 md:w-5 h-3.5 sm:h-4 md:h-5" viewBox="0 0 24 24" fill="white">
                    <path d="M7 19h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2Zm-8-4h2V9H7v2Zm4 0h2V9h-2v2Zm4 0h2V9h-2v2ZM3 21V3h18v18H3Zm2-2h14V5H5v14Z"/>
                  </svg>
                </div>
            <span class="hidden sm:inline text-xs md:text-sm">{{ portalLabel }}</span>
          </Link>
        </template>
      </div>
    </div>

    <!-- 📱 Mobile Menu Dropdown (showMobileMenu on lg:hidden) -->
    <div 
      v-if="showMobileMenu"
      class="mobile-menu-container lg:hidden bg-white/95 backdrop-blur border-t border-gray-200 shadow-lg"
    >
      <nav class="flex flex-col divide-y divide-gray-200 max-h-96 overflow-y-auto">
        <Link href="/" class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-medium text-[#17464E] hover:bg-gray-50 transition-colors">Beranda</Link>
        <Link href="/about" class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-medium text-[#17464E] hover:bg-gray-50 transition-colors">Tentang</Link>
        <Link href="/peraturan" class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-medium text-[#17464E] hover:bg-gray-50 transition-colors">Peraturan</Link>
        <Link href="/dokumen" class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-medium text-[#17464E] hover:bg-gray-50 transition-colors">Dokumen</Link>
        <Link href="/kontak" class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-medium text-[#17464E] hover:bg-gray-50 transition-colors">Kontak</Link>
        <div v-if="!isAuthenticated" class="px-4 sm:px-6 py-3 flex gap-2">
          <Link :href="registerHref" class="flex-1 text-center rounded-lg bg-white text-[#17464E] px-3 py-2 text-xs sm:text-sm font-semibold border border-[#17464E] hover:bg-gray-50 transition">Daftar</Link>
          <Link :href="loginHref" class="flex-1 text-center rounded-lg bg-[#0C505C] px-3 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-[#0a3a42] transition">Masuk</Link>
        </div>
      </nav>
    </div>
  </header>
</template>
