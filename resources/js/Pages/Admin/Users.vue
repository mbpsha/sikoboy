<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

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

// Tambah pengguna: dua alur terpisah (mitra / admin)
const showCreateModal = ref(false)
const createType = ref(null) // 'mitra' | 'admin'

const mitraForm = useForm({
  role: 'mitra',
  email: '',
  password: '',
  nama_perusahaan: '',
  pic: '',
  no_handphone: '',
  alamat: '',
})

const adminForm = useForm({
  role: 'admin',
  email: '',
  password: '',
  username: '',
  instansi: '',
})

const showPasswordMitra = ref(false)
const showPasswordAdmin = ref(false)

// Detail modal state
const showDetailModal = ref(false)
const selectedUser = ref(null)

function openDetail(user) {
  selectedUser.value = user
  showDetailModal.value = true
}

function closeDetail() {
  showDetailModal.value = false
  selectedUser.value = null
}

async function deleteUser(id) {
  if (!id) return
  const confirmed = await Swal.fire({
    title: 'Hapus pengguna?',
    text: 'Aksi ini akan menghapus pengguna secara permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal'
  })

  if (confirmed.isConfirmed) {
    const url = (() => { try { return route('admin.pengguna.destroy', id) } catch (e) { return `/admin/pengguna/${id}` } })()
    try {
      await router.delete(url, { preserveScroll: true })
      Swal.fire({ icon: 'success', title: 'Terhapus', timer: 1200, showConfirmButton: false })
      closeDetail()
      try { router.visit(route('admin.pengguna.index')) } catch (e) { router.reload() }
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Gagal menghapus', text: 'Periksa konsol untuk detail.' })
      console.error(e)
    }
  }
}

function openCreateMitra() {
  createType.value = 'mitra'
  mitraForm.clearErrors()
  showCreateModal.value = true
}

function openCreateAdmin() {
  createType.value = 'admin'
  adminForm.clearErrors()
  showCreateModal.value = true
}

function closeCreate() {
  showCreateModal.value = false
  createType.value = null
  mitraForm.reset()
  adminForm.reset()
}

function submitCreateMitra() {
  const url = (() => { try { return route('admin.pengguna.store') } catch (e) { return '/admin/pengguna' } })()

  const missing = []
  if (!mitraForm.email) missing.push('Email')
  if (!mitraForm.nama_perusahaan) missing.push('Nama perusahaan')
  if (!mitraForm.pic) missing.push('PIC / Nama penanggung jawab')
  if (!mitraForm.no_handphone) missing.push('No. HP')
  if (!mitraForm.alamat) missing.push('Alamat')

  if (missing.length) {
    Swal.fire({
      icon: 'warning',
      title: 'Form belum lengkap',
      html: `Silakan lengkapi: <strong>${missing.join(', ')}</strong>`,
    })
    return
  }

  mitraForm.post(url, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Akun mitra berhasil ditambahkan',
        timer: 1500,
        showConfirmButton: false,
      })
      closeCreate()
      try {
        router.visit(route('admin.pengguna.index'))
      } catch (e) {
        router.reload()
      }
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({
        icon: 'error',
        title: 'Gagal menyimpan',
        text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data yang dikirim.'),
      })
    },
  })
}

function submitCreateAdmin() {
  const url = (() => { try { return route('admin.pengguna.store') } catch (e) { return '/admin/pengguna' } })()

  const missing = []
  if (!adminForm.email) missing.push('Email')
  if (!adminForm.username) missing.push('Nama')
  if (!adminForm.instansi) missing.push('Divisi')

  if (missing.length) {
    Swal.fire({
      icon: 'warning',
      title: 'Form belum lengkap',
      html: `Silakan lengkapi: <strong>${missing.join(', ')}</strong>`,
    })
    return
  }

  adminForm.post(url, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Akun admin berhasil ditambahkan',
        timer: 1500,
        showConfirmButton: false,
      })
      closeCreate()
      try {
        router.visit(route('admin.pengguna.index'))
      } catch (e) {
        router.reload()
      }
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({
        icon: 'error',
        title: 'Gagal menyimpan',
        text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data yang dikirim.'),
      })
    },
  })
}

const indexOffset = computed(() => (users?.current_page ? ((users.current_page - 1) * users.per_page) : 0))

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

            <button type="button" @click.prevent="openCreateMitra" class="bg-teal-400 text-white px-4 py-2 rounded-full text-sm">+ Tambah Mitra</button>
            <button type="button" @click.prevent="openCreateAdmin" class="bg-indigo-500 text-white px-4 py-2 rounded-full text-sm">+ Tambah Admin</button>
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
                  <th class="py-3 px-4 text-left">Perusahaan / Divisi</th>
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
                  <!-- Tampilkan nama_perusahaan dari relasi mitra -->
                  <td class="py-4 px-4 text-gray-700">{{ user.mitra?.nama_perusahaan ?? user.instansi ?? '-' }}</td>
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
                      <button @click.prevent="openDetail(user)" class="px-3 py-1 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs">Detail</button>

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

        <!-- Tambah Mitra -->
        <div v-if="showCreateModal && createType === 'mitra'" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeCreate">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
            <h3 class="text-xl font-bold mb-1">Tambah Pengguna Mitra</h3>
            <p class="text-sm text-gray-500 mb-4">Menyimpan akun login (users) beserta profil mitra (nama perusahaan, PIC, kontak, alamat).</p>
            <form @submit.prevent="submitCreateMitra" class="space-y-3">
              <div>
                <label class="text-sm font-medium">Email login <span class="text-red-600">*</span></label>
                <input type="email" v-model="mitraForm.email" autocomplete="email" class="w-full border rounded px-3 py-2" />
                <p v-if="mitraForm.errors.email" class="text-red-500 text-sm">{{ mitraForm.errors.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Nama perusahaan <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.nama_perusahaan" class="w-full border rounded px-3 py-2" />
                <p v-if="mitraForm.errors.nama_perusahaan" class="text-red-500 text-sm">{{ mitraForm.errors.nama_perusahaan }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">PIC (nama penanggung jawab) <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.pic" class="w-full border rounded px-3 py-2" />
                <p v-if="mitraForm.errors.pic" class="text-red-500 text-sm">{{ mitraForm.errors.pic }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">No. handphone <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.no_handphone" class="w-full border rounded px-3 py-2" />
                <p v-if="mitraForm.errors.no_handphone" class="text-red-500 text-sm">{{ mitraForm.errors.no_handphone }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Alamat <span class="text-red-600">*</span></label>
                <textarea v-model="mitraForm.alamat" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                <p v-if="mitraForm.errors.alamat" class="text-red-500 text-sm">{{ mitraForm.errors.alamat }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Password <span class="text-gray-500 text-xs font-normal">(opsional, min. 8 karakter jika diisi)</span></label>
                <div class="relative">
                  <input :type="showPasswordMitra ? 'text' : 'password'" v-model="mitraForm.password" autocomplete="new-password" class="w-full border rounded px-3 py-2 pr-10" />
                  <button type="button" @click="showPasswordMitra = !showPasswordMitra" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 p-1">
                    <svg v-if="showPasswordMitra" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                  </button>
                </div>
                <p v-if="mitraForm.errors.password" class="text-red-500 text-sm">{{ mitraForm.errors.password }}</p>
              </div>
              <div class="flex gap-2 justify-end mt-4">
                <button type="button" @click="closeCreate" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                <button type="submit" :disabled="mitraForm.processing" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:opacity-50">
                  {{ mitraForm.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tambah Admin -->
        <div v-if="showCreateModal && createType === 'admin'" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeCreate">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-1">Tambah Pengguna Admin</h3>
            <p class="text-sm text-gray-500 mb-4">Menyimpan akun login (users) beserta profil admin (nama dan divisi).</p>
            <form @submit.prevent="submitCreateAdmin" class="space-y-3">
              <div>
                <label class="text-sm font-medium">Email login <span class="text-red-600">*</span></label>
                <input type="email" v-model="adminForm.email" autocomplete="email" class="w-full border rounded px-3 py-2" />
                <p v-if="adminForm.errors.email" class="text-red-500 text-sm">{{ adminForm.errors.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Nama <span class="text-red-600">*</span></label>
                <input v-model="adminForm.username" class="w-full border rounded px-3 py-2" placeholder="Nama lengkap admin" />
                <p v-if="adminForm.errors.username" class="text-red-500 text-sm">{{ adminForm.errors.username }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Divisi <span class="text-red-600">*</span></label>
                <input v-model="adminForm.instansi" class="w-full border rounded px-3 py-2" placeholder="Nama divisi / bidang" />
                <p v-if="adminForm.errors.instansi" class="text-red-500 text-sm">{{ adminForm.errors.instansi }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Password <span class="text-gray-500 text-xs font-normal">(opsional, min. 8 karakter jika diisi)</span></label>
                <div class="relative">
                  <input :type="showPasswordAdmin ? 'text' : 'password'" v-model="adminForm.password" autocomplete="new-password" class="w-full border rounded px-3 py-2 pr-10" />
                  <button type="button" @click="showPasswordAdmin = !showPasswordAdmin" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 p-1">
                    <svg v-if="showPasswordAdmin" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                  </button>
                </div>
                <p v-if="adminForm.errors.password" class="text-red-500 text-sm">{{ adminForm.errors.password }}</p>
              </div>
              <div class="flex gap-2 justify-end mt-4">
                <button type="button" @click="closeCreate" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                <button type="submit" :disabled="adminForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
                  {{ adminForm.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Detail User Modal -->
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeDetail">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
            <div class="flex items-start justify-between">
              <h3 class="text-lg font-semibold">Detail Pengguna</h3>
              <button @click="closeDetail" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <div class="mt-4 space-y-3">
              <div><strong>Nama:</strong> {{ selectedUser?.admin?.nama ?? selectedUser?.mitra?.pic ?? selectedUser?.display_name ?? '-' }}</div>
              <div><strong>Email:</strong> {{ selectedUser?.email ?? '-' }}</div>
              <div><strong>Role:</strong> {{ selectedUser?.role ?? '-' }}</div>
              <div v-if="selectedUser?.role === 'admin' && selectedUser?.admin">
                <h4 class="font-semibold mt-2">Data Admin</h4>
                <div><strong>Nama:</strong> {{ selectedUser.admin.nama ?? '-' }}</div>
                <div><strong>Divisi:</strong> {{ selectedUser.admin.divisi ?? '-' }}</div>
              </div>
              <div v-if="selectedUser?.mitra?.nama_perusahaan"><strong>Nama Perusahaan:</strong> {{ selectedUser.mitra.nama_perusahaan }}</div>
              <div v-if="selectedUser?.mitra">
                <h4 class="font-semibold mt-2">Data Mitra</h4>
                <div><strong>PIC:</strong> {{ selectedUser.mitra.pic ?? '-' }}</div>
                <div><strong>No. HP:</strong> {{ selectedUser.mitra.no_handphone ?? '-' }}</div>
                <div><strong>Alamat:</strong> {{ selectedUser.mitra.alamat ?? '-' }}</div>
              </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
              <button v-if="selectedUser?.role !== 'admin'" @click="deleteUser(selectedUser?.id)" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
              <button v-else class="px-4 py-2 bg-gray-200 text-gray-600 rounded cursor-not-allowed" disabled>Hapus</button>
              <button @click="closeDetail" class="px-4 py-2 bg-gray-200 rounded">Tutup</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.card-header { 
  background-color: #0C9AA0 
}

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