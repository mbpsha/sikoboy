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

          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 flex-1">
              <input v-model="local.search" @input="onSearchInput" placeholder="Cari Berdasarkan nama, email, instansi..." class="rounded-full px-4 py-2 text-sm w-full" />
              <button @click="applyFilters" title="Apply filters" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 18h4"/><path d="M6 6h12"/><path d="M8 12h8"/></svg>
              </button>
            </div>

            <select v-model="local.role" @change="onRoleChange" class="rounded-full px-3 py-2 text-sm">
              <option value="">Semua Role</option>
              <option value="admin">Admin</option>
              <option value="mitra">Mitra</option>
            </select>
            <button @click.prevent="openCreate" class="bg-white text-teal-800 px-4 py-2 rounded-full text-sm">+ Tambah Pengguna</button>
            <button @click.prevent="resetFilters" title="Reset filters" class="ml-2 bg-white/20 text-white px-3 py-2 rounded-full text-sm">Reset</button>
          </div>
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
        
        <!-- Create User Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4">Tambah Pengguna</h3>
            <form @submit.prevent="submitCreate" class="space-y-3">
              <div>
                <label class="text-sm font-medium">Nama / Username</label>
                <input v-model="createForm.username" class="w-full border rounded px-3 py-2" />
                <p v-if="createForm.errors.username" class="text-red-500 text-sm">{{ createForm.errors.username }}</p>
              </div>

              <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" v-model="createForm.email" class="w-full border rounded px-3 py-2" />
                <p v-if="createForm.errors.email" class="text-red-500 text-sm">{{ createForm.errors.email }}</p>
              </div>

              <div class="flex gap-2">
                <div class="flex-1">
                  <label class="text-sm font-medium">Role</label>
                  <select v-model="createForm.role" class="w-full border rounded px-3 py-2">
                    <option value="mitra">Mitra</option>
                    <option value="admin">Admin</option>
                  </select>
                  <p v-if="createForm.errors.role" class="text-red-500 text-sm">{{ createForm.errors.role }}</p>
                </div>
                <div class="flex-1">
                  <label class="text-sm font-medium">Instansi</label>
                  <input v-model="createForm.instansi" class="w-full border rounded px-3 py-2" />
                  <p v-if="createForm.errors.instansi" class="text-red-500 text-sm">{{ createForm.errors.instansi }}</p>
                </div>
              </div>

              <div>
                <label class="text-sm font-medium">Password (opsional)</label>
                <input type="password" v-model="createForm.password" class="w-full border rounded px-3 py-2" />
                <p v-if="createForm.errors.password" class="text-red-500 text-sm">{{ createForm.errors.password }}</p>
              </div>

              <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="closeCreate" class="px-4 py-2 rounded bg-gray-200">Batal</button>
                <button type="submit" :disabled="createForm.processing" class="px-4 py-2 rounded bg-teal-600 text-white">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage, router, useForm } from '@inertiajs/vue3'
import { ref, onBeforeUnmount, computed, onMounted } from 'vue'

const page = usePage()
const users = computed(() => page.props.value?.users ?? { data: [], per_page: 15, prev_page_url: null, next_page_url: null, current_page: 1 })
const filters = computed(() => page.props.value?.filters ?? {})

const indexOffset = computed(() => (users.value?.current_page ? ((users.value.current_page - 1) * users.value.per_page) : 0))

onMounted(() => {
  try {
    if (typeof window !== 'undefined' && import.meta.env && import.meta.env.DEV) {
      console.log('[Admin/Users] page.props:', page.props.value)
      console.log('[Admin/Users] users prop:', users.value)
    }
  } catch (e) { console.error(e) }
})

// Local reactive filter state
const local = ref({
  search: filters.search || '',
  role: filters.role || ''
})

// Create user modal state + form
const showCreateModal = ref(false)
const createForm = useForm({
  username: '',
  email: '',
  role: 'mitra',
  instansi: '',
  password: ''
})

function openCreate() {
  showCreateModal.value = true
}

function closeCreate() {
  showCreateModal.value = false
  createForm.reset()
}

function submitCreate() {
  const url = (() => { try { return route('admin.pengguna.store') } catch (e) { return '/admin/pengguna' } })()
  createForm.post(url, {
    onSuccess: () => {
      closeCreate()
      // refresh list
      try { router.visit(route('admin.pengguna.index')) } catch (e) { router.reload() }
    },
  })
}

let debounceTimer = null
function scheduleApplyFilters() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => applyFilters(), 400)
}

function onSearchInput() {
  scheduleApplyFilters()
}

function onRoleChange() {
  scheduleApplyFilters()
}

function applyFilters() {
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.role) params.role = local.value.role
  router.visit(route('admin.pengguna.index'), { method: 'get', data: params, preserveState: true, replace : true })
}

function goTo(url) {
  if (!url) return
  router.visit(url, { preserveState: true })
}

onBeforeUnmount(() => {
  clearTimeout(debounceTimer)
})
</script>

<style scoped>
/* Slight card header color tune to match example */
.card-header { background-color: #37c3c8 }
</style>
