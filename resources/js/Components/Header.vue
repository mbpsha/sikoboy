<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import logo from "@/images/logo_byl.png";

const page = usePage();
const isAuthenticated = computed(() => !!page.props?.auth?.user);
const userRole = computed(() => page.props?.auth?.user?.role ?? null);

// Normalized role (lowercase) to avoid case mismatches from backend
const userRoleNorm = computed(() => 
  String(page.props?.auth?.user?.role ?? '').toLowerCase()
);

const isDev = 
  typeof import.meta !== 'undefined' && 
  import.meta.env && 
  import.meta.env.DEV;

// 🔔 Notification state
const showNotificationDropdown = ref(false);
const notifications = ref([]);

// 📱 Mobile menu state
const showMobileMenu = ref(false);

// Compute notification count
const notificationsCount = computed(() => notifications.value.length);

// 🔔 Toggle notification dropdown
const toggleNotificationDropdown = () => {
  showNotificationDropdown.value = !showNotificationDropdown.value;
};

// 🔔 Close notification dropdown
const closeNotificationDropdown = () => {
  showNotificationDropdown.value = false;
};

// 📱 Close mobile menu
const closeMobileMenu = () => {
  showMobileMenu.value = false;
};

// 🔔 Handler when notification is clicked → REDIRECT to ListNotif.vue
const handleNotificationClick = (notification) => {
  if (isDev) {
    console.log('[Header] Notification clicked, redirecting to list:', notification);
  }
  closeNotificationDropdown();
  
  // Redirect to notifications list page
  try {
    router.get(route('mitra.notifications'));
  } catch (e) {
    router.get('/mitra/notifications');
  }
};

// Close dropdowns when clicking outside
onMounted(() => {
  const handleClickOutside = (event) => {
    // Close notification dropdown
    if (
      showNotificationDropdown.value && 
      !event.target.closest('.notification-container')
    ) {
      closeNotificationDropdown();
    }
    
    // Close mobile menu
    if (
      showMobileMenu.value && 
      !event.target.closest('.mobile-menu-container')
    ) {
      closeMobileMenu();
    }
  };
  
  document.addEventListener('click', handleClickOutside);
  
  // Load notifications from props if available
  if (page.props?.notifications) {
    notifications.value = page.props.notifications;
  }
  
  // Debug logging
  if (isDev) {
    console.log('[Header] page.props.auth:', page.props?.auth);
    console.log('[Header] userRole:', userRole.value);
    console.log('[Header] userRoleNorm:', userRoleNorm.value);
    console.log('[Header] isAuthenticated:', isAuthenticated.value);
    console.log('[Header] notifications:', notifications.value);
  }
  
  // Cleanup listener
  return () => {
    document.removeEventListener('click', handleClickOutside);
  };
});

// Get current URL for active nav detection
const currentUrl = computed(() => {
  try {
    if (page && page.url) return String(page.url);
    const props = page && page.props;
    if (props && props.url) return String(props.url);
  } catch (e) {}
  if (typeof window !== 'undefined') return window.location.href;
  return '';
});

// Check if nav link is active
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

// Safe route helpers with fallbacks
const registerHref = computed(() => {
  try {
    return route('register');
  } catch (e) {
    return '/register';
  }
});

const loginHref = computed(() => {
  try {
    return route('login');
  } catch (e) {
    return '/login';
  }
});

const portalHref = computed(() => {
  try {
    if (userRoleNorm.value === 'mitra') return route('mitra.profile.index');
    if (userRoleNorm.value === 'admin') return route('admin.dashboard');
    return route('home');
  } catch (e) {
    if (userRoleNorm.value === 'mitra') return '/mitra/profile';
    if (userRoleNorm.value === 'admin') return '/admin/dashboard';
    return '/';
  }
});

const portalLabel = computed(() => {
  return userRoleNorm.value === 'mitra' 
    ? 'Portal Mitra' 
    : userRoleNorm.value === 'admin' 
      ? 'Dashboard' 
      : 'Portal';
});
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 sm:px-6 py-4">
      <!-- Left: emblem + authority text -->
      <div class="flex items-center gap-1 rounded-full px-3 sm:px-5 py-2" style="background: rgba(49,113,124,0.6);">
        <img 
          :src="logo" 
          alt="Boyolali Logo" 
          class="h-12 w-12 sm:h-17 sm:w-17 object-contain mr-1" 
        />
        <div class="text-left text-white hidden sm:block">
          <div class="text-sm sm:text-base font-semibold">Sekretariat Daerah</div>
          <div class="text-xs sm:text-sm font-medium tracking-wide">Kabupaten Boyolali</div>
        </div>
      </div>

      <!-- Center: Desktop pill nav (hidden on mobile) -->
      <div class="hidden lg:flex items-center justify-center flex-1 mx-4">
        <nav 
          class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg" 
          style="background-color: rgba(23,70,78,0.95);"
        >
          <Link 
            href="/" 
            :class="isActive('/') 
              ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' 
              : 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors'"
          >
            Beranda
          </Link>
          <Link 
            href="/about" 
            :class="isActive('/about') 
              ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' 
              : 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors'"
          >
            Tentang
          </Link>
          <Link 
            href="/peraturan" 
            :class="isActive('/peraturan') 
              ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' 
              : 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors'"
          >
            Peraturan
          </Link>
          <Link 
            href="/dokumen" 
            :class="isActive('/dokumen') 
              ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' 
              : 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors'"
          >
            Dokumen
          </Link>
          <Link 
            href="/kontak" 
            :class="isActive('/kontak') 
              ? 'mx-2 rounded-full bg-white text-[#17464E] px-4 py-1 text-sm font-semibold' 
              : 'mx-2 px-4 py-1 text-sm text-white/90 hover:text-white transition-colors'"
          >
            Kontak
          </Link>
        </nav>
      </div>

      <!-- Right: CTAs, Notifications, and Portal -->
      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Unauthenticated: Register & Login buttons -->
        <template v-if="!isAuthenticated">
          <div class="hidden sm:flex items-center gap-3 rounded-full px-3 py-1" style="background: rgba(49,113,124,0.6);">
            <Link 
              :href="registerHref" 
              class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-[#BEBDBD] transition-colors"
            >
              Daftar
            </Link>
            <Link 
              :href="loginHref" 
              class="rounded-full bg-[#0C505C] px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-[#265e63] transition-colors"
            >
              Masuk
            </Link>
          </div>
          
          <!-- Mobile: Compact login button -->
          <div class="sm:hidden flex gap-2">
            <Link 
              :href="loginHref" 
              class="rounded-full bg-[#0C505C] px-3 py-2 text-xs font-semibold text-white shadow-md hover:bg-[#265e63] transition-colors"
            >
              Masuk
            </Link>
          </div>
        </template>

        <!-- Authenticated: Notifications, Portal, and Mobile Menu -->
        <template v-else>
          <!-- 🔔 NOTIFICATION BELL -->
          <div class="relative notification-container">
            <button 
              @click.stop="toggleNotificationDropdown"
              class="relative p-2 rounded-full bg-[#2f6f73]/60 hover:bg-[#2f6f73] transition-colors"
              title="Notifikasi"
              aria-label="Notifikasi"
            >
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="w-5 h-5 sm:w-6 sm:h-6 text-white" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor" 
                stroke-width="2"
              >
                <path 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" 
                />
              </svg>
              
              <!-- Badge with notification count -->
              <span 
                v-if="notificationsCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse"
              >
                {{ notificationsCount > 9 ? '9+' : notificationsCount }}
              </span>
            </button>

            <!-- Dropdown Notifications (Desktop & Mobile) -->
            <div 
              v-if="showNotificationDropdown" 
              class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] bg-white rounded-2xl shadow-2xl z-50 overflow-hidden"
            >
              <div class="bg-[#2f6f73] px-4 py-3">
                <h3 class="text-white font-semibold text-sm">Notifikasi</h3>
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
                        <svg 
                          xmlns="http://www.w3.org/2000/svg" 
                          class="w-5 h-5 text-yellow-600" 
                          fill="none" 
                          viewBox="0 0 24 24" 
                          stroke="currentColor"
                        >
                          <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
                          />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-800 truncate">{{ notif.title }}</p>
                      <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ notif.message }}</p>
                      <p class="text-xs text-yellow-600 mt-2 font-medium">
                        {{ notif.days_left }} hari lagi
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="bg-gray-50 px-4 py-2 text-center border-t border-gray-100">
                <Link 
                  :href="route('mitra.notifications')" 
                  class="text-sm text-[#2f6f73] font-medium hover:underline transition-colors"
                >
                  Lihat Semua →
                </Link>
              </div>
            </div>
          </div>
          <!-- 🔔 END NOTIFICATION BELL -->

          <!-- Portal Link (Desktop) -->
          <Link 
            :href="portalHref" 
            class="hidden sm:flex items-center gap-2 rounded-full bg-[#0C505C] text-white px-4 py-2 text-sm font-semibold shadow-md hover:bg-[#0a4a4e] transition-colors"
          >
            <div class="bg-[#2f6f73] p-2 rounded-xl shadow-sm flex items-center justify-center w-10 h-10 shrink-0">
              <svg
                class="w-5 h-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M8 7h8M8 12h8M8 17h8" />
              </svg>
            </div>
            <span>{{ portalLabel }}</span>
          </Link>

          <!-- 📱 Mobile Menu Button -->
          <button 
            @click.stop="showMobileMenu = !showMobileMenu"
            class="sm:hidden p-2 rounded-full hover:bg-white/10 transition-colors"
            title="Menu"
            aria-label="Menu"
          >
            <svg 
              class="w-6 h-6 text-white" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                v-if="!showMobileMenu"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4 6h16M4 12h16M4 18h16" 
              />
              <path 
                v-else
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M6 18L18 6M6 6l12 12" 
              />
            </svg>
          </button>
        </template>
      </div>
    </div>

    <!-- 📱 Mobile Menu Dropdown (only for authenticated users) -->
    <div 
      v-if="showMobileMenu && isAuthenticated"
      class="sm:hidden mobile-menu-container border-t border-gray-200"
      style="background: rgba(23,70,78,0.95);"
    >
      <nav class="flex flex-col px-4 py-3 gap-1 max-w-6xl mx-auto">
        <Link 
          href="/" 
          @click="closeMobileMenu"
          :class="isActive('/') 
            ? 'block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold' 
            : 'block rounded-lg px-4 py-2 text-sm text-white/90 hover:bg-white/10 transition-colors'"
        >
          Beranda
        </Link>
        <Link 
          href="/about" 
          @click="closeMobileMenu"
          :class="isActive('/about') 
            ? 'block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold' 
            : 'block rounded-lg px-4 py-2 text-sm text-white/90 hover:bg-white/10 transition-colors'"
        >
          Tentang
        </Link>
        <Link 
          href="/peraturan" 
          @click="closeMobileMenu"
          :class="isActive('/peraturan') 
            ? 'block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold' 
            : 'block rounded-lg px-4 py-2 text-sm text-white/90 hover:bg-white/10 transition-colors'"
        >
          Peraturan
        </Link>
        <Link 
          href="/dokumen" 
          @click="closeMobileMenu"
          :class="isActive('/dokumen') 
            ? 'block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold' 
            : 'block rounded-lg px-4 py-2 text-sm text-white/90 hover:bg-white/10 transition-colors'"
        >
          Dokumen
        </Link>
        <Link 
          href="/kontak" 
          @click="closeMobileMenu"
          :class="isActive('/kontak') 
            ? 'block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold' 
            : 'block rounded-lg px-4 py-2 text-sm text-white/90 hover:bg-white/10 transition-colors'"
        >
          Kontak
        </Link>
        
        <!-- Divider -->
        <div class="border-t border-white/20 my-2"></div>
        
        <!-- Portal Link (Mobile) -->
        <Link 
          :href="portalHref" 
          @click="closeMobileMenu"
          class="flex items-center gap-2 rounded-lg bg-[#0C505C] text-white px-4 py-2 text-sm font-semibold hover:bg-[#0a4a4e] transition-colors"
        >
          <div class="bg-[#2f6f73] p-2 rounded-lg shadow-sm flex items-center justify-center w-8 h-8 shrink-0">
            <svg
              class="w-4 h-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <path d="M8 7h8M8 12h8M8 17h8" />
            </svg>
          </div>
          <span>{{ portalLabel }}</span>
        </Link>
        
        <!-- Register Link (Mobile - only if not authenticated, but this is in authenticated block) -->
        <Link 
          :href="registerHref" 
          @click="closeMobileMenu"
          class="block rounded-lg bg-white text-[#17464E] px-4 py-2 text-sm font-semibold hover:bg-[#BEBDBD] transition-colors text-center"
        >
          Daftar
        </Link>
      </nav>
    </div>
  </header>
</template>