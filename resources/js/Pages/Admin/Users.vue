<template>
  <AdminLayout title="Pengguna">
    <div class="max-w-6xl mx-auto">
      <!-- Card -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Card header with search and add button -->
        <div class="flex items-center justify-between p-6 border-b" style="background-color:#0C9AA0">
          <div class="flex items-center gap-4">
            <h2 class="text-white font-semibold">Pengguna</h2>
          </div>

          <form :action="route('admin.pengguna.index')" method="get" class="flex items-center gap-3">
            <input name="search" :value="filters.search || ''" placeholder="Cari Berdasarkan nama, email, instansi..." class="rounded-full px-4 py-2 text-sm w-80" />
            <select name="role" class="rounded-full px-3 py-2 text-sm">
              <option value="">Semua Role</option>
              <option value="admin" :selected="filters.role==='admin'">Admin</option>
              <option value="mitra" :selected="filters.role==='mitra'">Mitra</option>
            </select>
            <button type="submit" class="bg-white text-teal-800 px-4 py-2 rounded-full text-sm">Filter</button>
            <Link :href="route('admin.pengguna.index') + '?create=1'" class="bg-white text-teal-800 px-4 py-2 rounded-full text-sm">+ Tambah Pengguna</Link>
          </form>
        </div>

        <!-- Table -->
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
              <thead>
                <tr class="bg-[#0C9AA0] text-white text-sm">
                  <th class="py-3 px-4 text-left rounded-l-md">No</th>
                  <th class="py-3 px-4 text-left">Username</th>
                  <th class="py-3 px-4 text-left">Email</th>
                  <th class="py-3 px-4 text-left">Role</th>
                  <th class="py-3 px-4 text-left">Instansi</th>
                  <th class="py-3 px-4 text-left">Status</th>
                  <th class="py-3 px-4 text-left">Tanggal Daftar</th>
                  <th class="py-3 px-4 text-left rounded-r-md">Aksi</th>
                </tr>
              </thead>

              <tbody class="bg-white text-sm">
                <tr v-for="(user, idx) in users.data" :key="user.id" class="border-b">
                  <td class="py-4 px-4 text-gray-700">{{ indexOffset + idx + 1 }}</td>
                  <td class="py-4 px-4 text-gray-700">
                    <div class="font-medium">{{ user.admin?.nama ?? user.mitra?.pic ?? user.display_name ?? '-' }}</div>
                    <div class="text-xs text-gray-400">{{ user.email }}</div>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.email }}</td>
                  <td class="py-4 px-4">
                    <span v-if="user.role==='admin'" class="px-3 py-1 rounded-full bg-purple-200 text-purple-800 text-xs">Admin</span>
                    <span v-else class="px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs">Mitra</span>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.instansi ?? '-' }}</td>
                  <td class="py-4 px-4">
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs">Aktif</span>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.tanggal_daftar ?? '-' }}</td>
                  <td class="py-4 px-4 text-gray-700">
                    <div class="flex items-center gap-2">
                      <Link :href="route('users.show', user.id)" class="text-teal-700 hover:text-teal-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 12l4 4M9 12l-4 4M12 20V4"/></svg>
                      </Link>
                      <button class="text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 6h18M8 6v13a2 2 0 002 2h4a2 2 0 002-2V6"/><path d="M10 11v6M14 11v6"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-600">Tampilkan {{ users.per_page }} / Halaman</div>
            <div class="flex items-center gap-2">
              <button :disabled="!users.prev_page_url" @click.prevent="goTo(users.prev_page_url)" class="px-3 py-1 bg-white rounded-md">Prev</button>
              <button :disabled="!users.next_page_url" @click.prevent="goTo(users.next_page_url)" class="px-3 py-1 bg-white rounded-md">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const users = page.props.value?.users ?? { data: [], per_page: 15, prev_page_url: null, next_page_url: null, current_page: 1 }
const filters = page.props.value?.filters ?? {}

const indexOffset = (users?.current_page ? ((users.current_page - 1) * users.per_page) : 0)

function goTo(url) {
  if (!url) return
  router.visit(url, { preserveState: false })
}
</script>

<style scoped>
/* Slight card header color tune to match example */
.card-header { background-color: #0C9AA0 }
</style>
