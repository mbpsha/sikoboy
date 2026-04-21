<template>
  <aside style="background-color:#0C505C" class="text-white w-64 h-screen fixed left-0 top-0 z-40 flex flex-col justify-between overflow-hidden">
    
    <!-- TOP -->
    <div>
      <!-- LOGO -->
      <div class="px-6 pt-6 pb-4">
        <div class="text-3xl font-extrabold tracking-widest">SIKOBOY</div>
        <p class="text-xs text-teal-100 mt-1">Admin Dashboard</p>
        <div class="mt-4 border-t border-teal-700"></div>
      </div>

      <!-- MENU -->
      <nav class="mt-2 space-y-2 px-3">
        <Link :href="route('admin.dashboard')" :class="navClass('/admin/dashboard')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"/>
          </svg>
          <span class="text-sm">Beranda</span>
        </Link>

        <Link :href="route('admin.pengguna.index')" :class="navClass('/admin/pengguna')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <!-- Kepala utama -->
            <circle cx="9" cy="8" r="3" />
            <!-- Badan utama -->
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-3 3-5 5-5s5 2 5 5"/>
          </svg>
          <span class="text-sm">Pengguna</span>
        </Link>

        <Link :href="route('admin.data-kerjasama.index')" :class="navClass('/admin/data-kerjasama')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
          </svg>
          <span class="text-sm">Data Kerjasama</span>
        </Link>

        <Link href="#" :class="navClass('/admin/riwayat')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="9"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
          </svg>
          <span class="text-sm">Riwayat Kerjasama</span>
        </Link>

        <Link href="#" :class="navClass('/admin/potensi')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            <circle cx="9" cy="6" r="2"/>
            <circle cx="15" cy="12" r="2"/>
            <circle cx="11" cy="18" r="2"/>
          </svg>
          <span class="text-sm">Manajemen Potensi</span>
        </Link>

        <Link href="#" :class="navClass('/admin/dokumen')">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6M9 17h6"/>
          </svg>
          <span class="text-sm">Manajemen Dokumen</span>
        </Link>
      </nav>
    </div>

    <!-- BOTTOM (LOGOUT) -->
    <div class="p-4">
      <button
        @click="showConfirm = true"
        class="w-full bg-red-600 hover:bg-red-700 transition p-3 rounded-lg font-semibold"
      >
        Logout
      </button>
    </div>

    <!-- MODAL KONFIRMASI -->
    <div v-if="showConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-80 text-center">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
          Yakin ingin logout?
        </h2>

        <div class="flex justify-center gap-4">
          <button
            @click="showConfirm = false"
            class="px-4 py-2 bg-gray-600 rounded-lg"
          >
            Batal
          </button>

          <button
            @click="logout"
            class="px-4 py-2 bg-red-600 text-white rounded-lg"
          >
            Logout
          </button>
        </div>
      </div>
    </div>

  </aside>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import axios from 'axios'

const page = usePage()
const showConfirm = ref(false)

// ACTIVE MENU
const navClass = (url) => {
  const base = 'flex items-center gap-3 px-2 py-2 rounded-full transition w-full';
  if (page.url && page.url.startsWith(url)) {
    return base + ' bg-teal-100 text-teal-900 font-semibold';
  }
  return base + ' text-white/90 hover:bg-teal-700/30 hover:text-white';
}

const iconClass = (url) => {
  const base = 'w-9 h-9 flex items-center justify-center rounded-md';
  if (page.url && page.url.startsWith(url)) {
    return base + ' bg-white text-teal-900';
  }
  return base + ' bg-white/10 text-white';
}

// LOGOUT FUNCTION
const logout = () => {
  router.post(route('logout'), {}, {
    onSuccess: () => window.location.href = '/'
  })
}
</script>