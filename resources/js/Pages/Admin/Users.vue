<template>
  <AdminLayout title="Pengguna">
    <div class="max-w-6xl mx-auto">
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b bg-teal-700">
          <div class="flex items-center gap-4">
            <h2 class="text-white font-semibold">Pengguna</h2>
          </div>

          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 flex-1">
              <input v-model="local.search" @input="onSearchInput" placeholder="Cari Berdasarkan nama, email, instansi..." class="rounded-full bg-white px-4 py-2 text-sm w-full" />
              <button @click="applyFilters" title="Apply filters" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 18h4"/><path d="M6 6h12"/><path d="M8 12h8"/></svg>
              </button>
            </div>

            <select v-model="local.role" @change="onRoleChange" class="rounded-full px-3 py-2 text-sm bg-white">
              <option value="">Semua Role</option>
              <option value="admin">Admin</option>
              <option value="mitra">Mitra</option>
            </select>

            <button @click.prevent="openCreate" class="bg-teal-400 text-white px-4 py-2 rounded-full text-sm">+ Tambah Pengguna</button>
            <button @click.prevent="resetFilters" title="Reset filters" class="ml-2 bg-white/20 text-white px-3 py-2 rounded-full text-sm">Reset</button>
          </div>
        </div>

        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto table-lines">
              <thead>
                <tr class="bg-teal-700 text-white text-sm">
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
                    <span
                      class="px-3 py-1 rounded-full text-xs"
                      :class="user.status === 'menunggu_verifikasi'
                        ? 'bg-amber-100 text-amber-800'
                        : user.status === 'ditolak'
                          ? 'bg-red-100 text-red-800'
                          : 'bg-green-100 text-green-800'"
                    >
                      {{ user.status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : user.status === 'ditolak' ? 'Ditolak' : 'Aktif' }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.tanggal_daftar ?? '-' }}</td>
                  <td class="py-4 px-4 text-gray-700">
                    <div class="flex items-center gap-2">
                      <Link :href="route('admin.users.show', user.id)" class="px-3 py-1 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs">Detail</Link>

                      <button
                        v-if="user.role === 'mitra' && user.can_verify"
                        class="px-3 py-1 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 text-xs disabled:opacity-60"
                        :disabled="verifyingUserId === user.id"
                        @click.prevent="verifyMitra(user.id)"
                      >
                        {{ verifyingUserId === user.id ? 'Memverifikasi...' : 'Verifikasi' }}
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="!users.data?.length">
                  <td colspan="8" class="py-6 px-4 text-center text-gray-500">Belum ada data pengguna.</td>
                </tr>
              </tbody>
            </table>
          </div>

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
            <h3 class="text-lg font-semibold mb-4">Tambah Pengguna</h3>
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
import { Link, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  users: {
    type: Object,
    default: () => ({ data: [], per_page: 15, prev_page_url: null, next_page_url: null, current_page: 1 }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const users = props.users
const filters = props.filters
const verifyingUserId = ref(null)

// Create user modal + form
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
      try { router.visit(route('admin.pengguna.index')) } catch (e) { router.reload() }
    }
  })
}

const indexOffset = (users?.current_page ? ((users.current_page - 1) * users.per_page) : 0)

// Local reactive filter state
const local = ref({
  search: filters.search || '',
  role: filters.role || ''
})

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
  router.visit(route('admin.pengguna.index'), { method: 'get', data: params, preserveState: false })
}

function resetFilters() {
  local.value.search = ''
  local.value.role = ''
  router.visit(route('admin.pengguna.index'), { method: 'get', data: {}, preserveState: false, replace: true })
}

function goTo(url) {
  if (!url) return
  router.visit(url, { preserveState: false })
}

function verifyMitra(id) {
  if (!id) return

  verifyingUserId.value = id
  router.put(route('admin.pengguna.verify', id), {}, {
    preserveScroll: true,
    onFinish: () => {
      verifyingUserId.value = null
    },
  })
}
</script>

<style scoped>
/* Slight card header color tune to match example */
.card-header { background-color: #0C9AA0 }
  /* Slight card header color tune to match example */
  .card-header { background-color: #0C9AA0 }

  /* Table helpers: header vertical separators and row dividers */
  .table-lines thead th {
    border-right: 1px solid rgba(255,255,255,0.18);
  }
  .table-lines thead th:last-child {
    border-right: none;
  }
  .table-lines tbody td {
    border-bottom: 1px solid rgba(15,23,42,0.06);
  }

</style>
