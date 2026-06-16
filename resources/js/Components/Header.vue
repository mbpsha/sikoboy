<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import logo from "@/images/logo_byl.png";

const page = usePage();
const isAuthenticated = computed(() => !!page.props?.auth?.user);
const userRoleNorm    = computed(() => String(page.props?.auth?.user?.role ?? '').toLowerCase());

const showNotificationDropdown = ref(false);
const showMobileMenu           = ref(false);
const notifications            = computed(() => page.props?.notifications || []);
const notificationsCount       = computed(() => notifications.value.length);

const portalHref = computed(() => {
  try {
    if (userRoleNorm.value === 'mitra') return route('mitra.profile.index');
    if (userRoleNorm.value === 'admin') return route('admin.dashboard');
    return route('home');
  } catch { return '/'; }
});
const portalLabel  = computed(() =>
  userRoleNorm.value === 'mitra' ? 'Portal Mitra' :
  userRoleNorm.value === 'admin' ? 'Dashboard' : 'Portal'
);
const registerHref = computed(() => { try { return route('register'); } catch { return '/register'; } });
const loginHref    = computed(() => { try { return route('login');    } catch { return '/login';    } });

const currentUrl = computed(() => {
  try { return String(page.url || ''); } catch { return ''; }
});
const isActive = (path) => {
  try {
    return new URL(currentUrl.value, window.location.origin).pathname === path;
  } catch { return false; }
};

const navLinks = [
  { href: '/',          label: 'Beranda'   },
  { href: '/about',     label: 'Tentang'   },
  { href: '/peraturan', label: 'Peraturan' },
  { href: '/dokumen',   label: 'Dokumen'   },
  { href: '/kontak',    label: 'Kontak'    },
];

const handleNotificationClick = () => {
  showNotificationDropdown.value = false;
  try { router.get(route('mitra.notifications')); } catch { router.get('/mitra/notifications'); }
};

const handleOutsideClick = (e) => {
  if (!e.target.closest('.notification-container')) showNotificationDropdown.value = false;
  if (!e.target.closest('.mobile-menu-container') && !e.target.closest('.hamburger-btn'))
    showMobileMenu.value = false;
};

onMounted(()   => document.addEventListener('click', handleOutsideClick));
onUnmounted(() => document.removeEventListener('click', handleOutsideClick));
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 sm:px-6 py-3">

      <!-- ── Logo ── -->
      <Link href="/" class="flex items-center gap-2 rounded-full px-3 py-2 shrink-0"
        style="background: rgba(49,113,124,0.6);">
        <img :src="logo" alt="Boyolali Logo" class="h-10 w-10 object-contain" />
        <div class="text-white hidden sm:block leading-tight">
          <p class="text-sm font-semibold">Sekretariat Daerah</p>
          <p class="text-xs tracking-wide">Kabupaten Boyolali</p>
        </div>
      </Link>

      <!-- ── Desktop nav (lg+) ── -->
      <nav class="hidden lg:flex items-center rounded-full px-2 py-1.5 gap-0.5 shadow-lg"
        style="background: rgba(23,70,78,0.95);">
        <Link v-for="link in navLinks" :key="link.href" :href="link.href"
          :class="isActive(link.href)
            ? 'rounded-full bg-white text-[#17464E] px-4 py-1.5 text-sm font-semibold'
            : 'px-4 py-1.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-full transition'">
          {{ link.label }}
        </Link>
      </nav>

      <!-- ── Right actions ── -->
      <div class="flex items-center gap-2">

        <!-- Unauthenticated (desktop) -->
        <template v-if="!isAuthenticated">
          <div class="hidden sm:flex items-center gap-2 rounded-full px-3 py-1.5"
            style="background: rgba(49,113,124,0.6);">
            <Link :href="registerHref"
              class="rounded-full bg-white text-[#17464E] px-4 py-1.5 text-sm font-semibold hover:bg-gray-100 transition">
              Daftar
            </Link>
            <Link :href="loginHref"
              class="rounded-full bg-[#0C505C] text-white px-4 py-1.5 text-sm font-semibold hover:bg-[#0a3f4a] transition">
              Masuk
            </Link>
          </div>
        </template>

        <!-- Authenticated extras -->
        <template v-if="isAuthenticated">
          <!-- Notification bell -->
          <div class="relative notification-container">
            <button @click.stop="showNotificationDropdown = !showNotificationDropdown"
              class="relative p-2 rounded-full bg-white/30 hover:bg-white/25 border border-white/20 transition-all duration-200">
              <svg class="w-5 h-5 text-teal-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span v-if="notificationsCount > 0"
                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                {{ notificationsCount > 9 ? '9+' : notificationsCount }}
              </span>
            </button>

            <div v-if="showNotificationDropdown"
              class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl overflow-hidden z-50">
              <div class="bg-[#0C505C] px-4 py-3 flex justify-between items-center">
                <h3 class="text-white font-semibold text-sm">Notifikasi</h3>
                <button @click="showNotificationDropdown = false" class="text-white/70 hover:text-white text-lg">×</button>
              </div>
              <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                <p v-if="!notificationsCount" class="text-center text-sm text-gray-400 py-8">Tidak ada notifikasi</p>
                <div v-for="(notif, i) in notifications" :key="i"
                  @click="handleNotificationClick(notif)"
                  class="flex gap-3 px-4 py-3 hover:bg-yellow-50 cursor-pointer transition">
                  <div class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ notif.title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ notif.message }}</p>
                    <p class="text-xs text-yellow-600 mt-1">{{ notif.days_left != null ? notif.days_left + ' hari lagi' : 'Baru' }}</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-2 bg-gray-50 border-t text-center">
                <Link :href="route('mitra.notifications')" class="text-sm text-[#0C505C] font-medium hover:underline">
                  Lihat Semua →
                </Link>
              </div>
            </div>
          </div>

          <!-- Portal (desktop) -->
          <Link :href="portalHref"
            class="hidden sm:flex items-center gap-2 rounded-full bg-[#0C505C] text-white px-3 py-1.5 text-sm font-semibold hover:bg-[#0a3f4a] transition shadow-md">
            <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M8 7h8M8 12h8M8 17h8"/>
              </svg>
            </div>
            <span class="hidden md:inline">{{ portalLabel }}</span>
          </Link>
        </template>

        <!-- ── Hamburger — SELALU ADA di mobile ── -->
        <button @click.stop="showMobileMenu = !showMobileMenu"
          class="hamburger-btn lg:hidden p-2 rounded-full bg-white/10 hover:bg-white/20 transition">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

      </div>
    </div>

    <!-- ── Mobile dropdown — UNTUK SEMUA USER ── -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="showMobileMenu"
        class="mobile-menu-container lg:hidden mx-3 mb-2 rounded-2xl shadow-2xl overflow-hidden border border-white/10"
        style="background: rgba(12,60,70,0.97);">

        <!-- Nav links -->
        <nav class="px-3 pt-3 pb-2 space-y-1">
          <Link v-for="link in navLinks" :key="link.href" :href="link.href"
            @click="showMobileMenu = false"
            :class="isActive(link.href)
              ? 'flex items-center justify-between rounded-xl bg-white text-[#17464E] px-4 py-2.5 text-sm font-semibold'
              : 'flex items-center rounded-xl text-white/80 hover:text-white hover:bg-white/10 px-4 py-2.5 text-sm transition'">
            {{ link.label }}
            <span v-if="isActive(link.href)" class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
          </Link>
        </nav>

        <div class="border-t border-white/10 mx-3"></div>

        <!-- Auth actions -->
        <div class="px-3 py-3 space-y-2">
          <template v-if="!isAuthenticated">
            <Link :href="loginHref" @click="showMobileMenu = false"
              class="flex items-center justify-center w-full rounded-xl bg-[#0C505C] text-white px-4 py-2.5 text-sm font-semibold hover:bg-[#0a3f4a] transition">
              Masuk
            </Link>
            <Link :href="registerHref" @click="showMobileMenu = false"
              class="flex items-center justify-center w-full rounded-xl bg-white text-[#17464E] px-4 py-2.5 text-sm font-semibold hover:bg-gray-100 transition">
              Daftar
            </Link>
          </template>
          <template v-else>
            <Link :href="portalHref" @click="showMobileMenu = false"
              class="flex items-center gap-3 w-full rounded-xl bg-[#0C505C] text-white px-4 py-2.5 text-sm font-semibold hover:bg-[#0a3f4a] transition">
              <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <path d="M8 7h8M8 12h8M8 17h8"/>
                </svg>
              </div>
              {{ portalLabel }}
            </Link>
          </template>
        </div>

      </div>
    </Transition>
  </header>
</template>