<template>
  <aside class="bg-teal-900 text-white w-64 min-h-screen fixed md:static">
    
    <!-- LOGO -->
    <div class="p-6 text-2xl font-bold tracking-widest">
      SIKOBOY
      <p class="text-sm font-normal mt-1">Admin Dashboard</p>
    </div>

    <!-- MENU -->
    <nav class="mt-6 space-y-2 px-4">
      <Link :href="route('admin.dashboard')" :class="navClass('/admin/dashboard')">
        Beranda
      </Link>

      <Link href="#" :class="navClass('/admin/pengguna')">
        Pengguna
      </Link>

      <Link href="#" :class="navClass('/admin/kerjasama')">
        Data Kerjasama
      </Link>

      <Link href="#" :class="navClass('/admin/riwayat')">
        Riwayat Kerjasama
      </Link>

      <Link href="#" :class="navClass('/admin/potensi')">
        Manajemen Potensi
      </Link>

      <Link href="#" :class="navClass('/admin/dokumen')">
        Manajemen Dokumen
      </Link>
    </nav>
  </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const navClass = (url) => {
  return [
    "flex items-center gap-3 p-3 rounded-lg transition",
    page.url.startsWith(url)
      ? "bg-teal-700"
      : "hover:bg-teal-700"
  ]
}
</script>