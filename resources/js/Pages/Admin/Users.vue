<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
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

const page          = usePage()
const currentUserId = computed(() => page.props.auth?.user?.id ?? null)

const users = computed(() => props.users ?? { data: [], per_page: 15, prev_page_url: null, next_page_url: null, current_page: 1 })
const filters = computed(() => props.filters ?? {})

const verifyingUserId = ref(null)
const showCreateModal = ref(false)
const createType      = ref(null)
const togglingUserId  = ref(null)
const isFiltering     = ref(false)

const mitraForm = useForm({
  role: 'mitra', email: '', password: '',
  nama_perusahaan: '', pic: '', no_handphone: '', alamat: '',
})

const adminForm = useForm({
  role: 'admin', email: '', password: '', username: '', instansi: '',
})

const showPasswordMitra = ref(false)
const showPasswordAdmin = ref(false)
const showDetailModal   = ref(false)
const selectedUser      = ref(null)

function openDetail(user) {
  selectedUser.value    = user
  showDetailModal.value = true
}

function closeDetail() {
  showDetailModal.value = false
  selectedUser.value    = null
}

async function deleteUser(id) {
  if (!id) return
  const confirmed = await Swal.fire({
    title: 'Hapus pengguna?',
    text: 'Aksi ini akan menghapus pengguna secara permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#dc2626',
  }).then(r => r.isConfirmed)

  if (!confirmed) return

  router.delete(route('admin.pengguna.destroy', id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Terhapus', timer: 1200, showConfirmButton: false })
      closeDetail()
    },
    onError: () => {
      Swal.fire({ icon: 'error', title: 'Gagal menghapus' })
    },
  })
}

// ✅ Toggle aktif / nonaktif
async function toggleActive(id, isActive) {
  if (!id) return

  const confirmed = await Swal.fire({
    title: isActive ? 'Nonaktifkan pengguna?' : 'Aktifkan pengguna?',
    text: isActive
      ? 'Pengguna tidak akan bisa login setelah dinonaktifkan.'
      : 'Pengguna akan bisa login kembali.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: isActive ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
    cancelButtonText: 'Batal',
    confirmButtonColor: isActive ? '#f97316' : '#16a34a',
  }).then(r => r.isConfirmed)

  if (!confirmed) return

  // optimistic update: apply change locally, rollback on error
  const idx = users.value.data.findIndex(u => u.id === id)
  const prev = idx !== -1 ? users.value.data[idx].is_active : null
  const newIsActive = !isActive

  if (idx !== -1) {
    users.value.data[idx].is_active = newIsActive
    users.value.data[idx].status = newIsActive ? 'aktif' : 'ditolak'
  }
  if (selectedUser.value && selectedUser.value.id === id) {
    selectedUser.value.is_active = newIsActive
    selectedUser.value.status = newIsActive ? 'aktif' : 'ditolak'
  }

  togglingUserId.value = id

  router.put(
    route('admin.pengguna.update-status', id),
    { is_active: newIsActive },
    {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire({
          icon: 'success',
          title: isActive ? 'Pengguna dinonaktifkan' : 'Pengguna diaktifkan',
          timer: 1200,
          showConfirmButton: false,
        })
        closeDetail()
      },
      onError: () => {
        // rollback
        if (idx !== -1 && prev !== null) {
          users.value.data[idx].is_active = prev
          users.value.data[idx].status = prev ? 'aktif' : 'ditolak'
        }
        if (selectedUser.value && selectedUser.value.id === id && prev !== null) {
          selectedUser.value.is_active = prev
          selectedUser.value.status = prev ? 'aktif' : 'ditolak'
        }
        Swal.fire({ icon: 'error', title: 'Gagal mengubah status pengguna' })
      },
      onFinish: () => {
        togglingUserId.value = null
      },
    }
  )
}

function openCreateMitra() { createType.value = 'mitra'; mitraForm.clearErrors(); showCreateModal.value = true }
function openCreateAdmin()  { createType.value = 'admin'; adminForm.clearErrors(); showCreateModal.value = true }
function closeCreate() {
  showCreateModal.value = false
  createType.value      = null
  mitraForm.reset()
  adminForm.reset()
}

function submitCreateMitra() {
  const missing = []
  if (!mitraForm.email)           missing.push('Email')
  if (!mitraForm.nama_perusahaan) missing.push('Nama perusahaan')
  if (!mitraForm.pic)             missing.push('PIC')
  if (!mitraForm.no_handphone)    missing.push('No. HP')
  if (!mitraForm.alamat)          missing.push('Alamat')
  if (!mitraForm.password)        missing.push('Password')

  if (missing.length) {
    Swal.fire({ icon: 'warning', title: 'Form belum lengkap', html: `Silakan lengkapi: <strong>${missing.join(', ')}</strong>` })
    return
  }

  mitraForm.post(route('admin.pengguna.store'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Akun mitra berhasil ditambahkan', timer: 1500, showConfirmButton: false })
      closeCreate()
      router.visit(route('admin.pengguna.index'))
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({ icon: 'error', title: 'Gagal menyimpan', text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data.') })
    },
  })
}

function submitCreateAdmin() {
  const missing = []
  if (!adminForm.email)    missing.push('Email')
  if (!adminForm.username) missing.push('Nama')
  if (!adminForm.instansi) missing.push('Divisi')
  if (!adminForm.password) missing.push('Password')

  if (missing.length) {
    Swal.fire({ icon: 'warning', title: 'Form belum lengkap', html: `Silakan lengkapi: <strong>${missing.join(', ')}</strong>` })
    return
  }

  adminForm.post(route('admin.pengguna.store'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Akun admin berhasil ditambahkan', timer: 1500, showConfirmButton: false })
      closeCreate()
      router.visit(route('admin.pengguna.index'))
    },
    onError: (errors) => {
      const first = Object.values(errors)[0]
      Swal.fire({ icon: 'error', title: 'Gagal menyimpan', text: Array.isArray(first) ? first[0] : String(first ?? 'Periksa data.') })
    },
  })
}

const indexOffset = computed(() => (users.value?.current_page ? (users.value.current_page - 1) * users.value.per_page : 0))

const local = ref({ search: filters.value.search || '', role: filters.value.role || '' })

const displayedUsers = computed(() => users.value?.data || [])

let debounceTimer = null
function scheduleApplyFilters() {
  isFiltering.value = true
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => applyFilters(), 400)
}

function applyFilters() {
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.role)   params.role   = local.value.role
  router.get(
    route('admin.pengguna.index'),
    params,
    {
      preserveState: true,
      preserveScroll: true,
      onFinish: () => {
        isFiltering.value = false
      },
    }
  )
}

function resetFilters() {
  local.value.search = ''
  local.value.role   = ''
  isFiltering.value = true
  router.visit(route('admin.pengguna.index'), {
    method: 'get',
    data: {},
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isFiltering.value = false
    },
  })
}

function goTo(url) {
  if (!url) return
  isFiltering.value = true
  router.visit(url, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isFiltering.value = false
    },
  })
}

const goToPage = (page) => {
  if (!page) return
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.role) params.role = local.value.role
  isFiltering.value = true
  router.visit(route('admin.pengguna.index'), {
    method: 'get',
    data: { ...params, page },
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isFiltering.value = false
    },
  })
}

function verifyMitra(id) {
  if (!id) return
  verifyingUserId.value = id
  router.put(route('admin.pengguna.verify', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Akun mitra berhasil diverifikasi', timer: 1200, showConfirmButton: false })
        .then(() => router.visit(route('admin.pengguna.index'), { preserveState: false }))
    },
    onError: () => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memverifikasi akun mitra' })
      verifyingUserId.value = null
    },
    onFinish: () => { verifyingUserId.value = null },
  })
}
</script>

<template>
  <AdminLayout title="Pengguna">
    <div class="max-w-6xl mx-auto">
      <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <!-- Top Bar -->
        <div class="flex items-center justify-between p-6 border-b bg-teal-700">
          <h2 class="text-white font-semibold">Pengguna</h2>
          <div class="flex items-center gap-3 flex-wrap">
            <input
              v-model="local.search"
              @input="scheduleApplyFilters"
              placeholder="Cari nama, email, instansi..."
              class="rounded-full bg-white px-4 py-2 text-sm min-w-[220px]"
            />
            <select v-model="local.role" @change="scheduleApplyFilters" class="rounded-full px-3 py-2 text-sm bg-white">
              <option value="">Semua Role</option>
              <option value="admin">Admin</option>
              <option value="mitra">Mitra</option>
            </select>
            <button @click="applyFilters" title="Filter" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 4 21 4 14 12 14 19 10 21 10 12 3 4"/></svg>
            </button>
            <button @click.prevent="openCreateMitra" class="bg-teal-400 hover:bg-teal-300 text-white px-4 py-2 rounded-full text-sm font-medium">+ Mitra</button>
            <button @click.prevent="openCreateAdmin"  class="bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 rounded-full text-sm font-medium">+ Admin</button>
            <button @click.prevent="resetFilters" class="bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded-full text-sm">Reset</button>
          </div>
        </div>

        <!-- Table -->
        <div class="p-6">
          <div v-if="isFiltering" class="mb-4 rounded-lg border border-teal-100 bg-teal-50 px-4 py-3 text-sm text-teal-700">
            Memproses pencarian...
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto table-lines">
              <thead>
                <tr class="bg-teal-700 text-white text-sm">
                  <th class="py-3 px-4 text-left">No</th>
                  <th class="py-3 px-4 text-left">Username</th>
                  <th class="py-3 px-4 text-left">Email</th>
                  <th class="py-3 px-4 text-left">Role</th>
                  <th class="py-3 px-4 text-left">ID Mitra</th>
                  <th class="py-3 px-4 text-left">Perusahaan / Divisi</th>
                  <th class="py-3 px-4 text-left">Status</th>
                  <th class="py-3 px-4 text-left">Tanggal Daftar</th>
                  <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white text-sm">
                <tr v-for="(user, idx) in displayedUsers" :key="user.id" class="border-b" :class="{ 'opacity-60 bg-gray-50': !user.is_active }">
                  <td class="py-4 px-4 text-gray-700">{{ indexOffset + idx + 1 }}</td>
                  <td class="py-4 px-4 text-gray-700">
                    <div class="font-medium">{{ user.admin?.nama ?? user.mitra?.pic ?? user.display_name ?? '-' }}</div>
                    <div class="text-xs text-gray-400">{{ user.email }}</div>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.email }}</td>
                  <td class="py-4 px-4">
                    <span v-if="user.role === 'admin'" class="px-3 py-1 rounded-full bg-purple-200 text-purple-800 text-xs">Admin</span>
                    <span v-else class="px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs">Mitra</span>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.id ?? '-' }}</td>
                  <td class="py-4 px-4 text-gray-700">{{ user.mitra?.nama_perusahaan ?? user.instansi ?? '-' }}</td>
                  <td class="py-4 px-4">
                    <!-- ✅ Badge nonaktif -->
                    <span
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="!user.is_active
                        ? 'bg-gray-200 text-gray-500'
                        : user.status === 'menunggu_verifikasi'
                          ? 'bg-amber-100 text-amber-800'
                          : user.status === 'ditolak'
                            ? 'bg-red-100 text-red-800'
                            : 'bg-green-100 text-green-800'"
                    >
                      {{ !user.is_active ? 'Nonaktif'
                        : user.status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi'
                        : user.status === 'ditolak' ? 'Ditolak'
                        : 'Aktif' }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-gray-700">{{ user.tanggal_daftar ?? '-' }}</td>
                  <td class="py-4 px-4">
                    <div class="flex items-center gap-2 flex-wrap">
                      <button @click.prevent="openDetail(user)" class="px-3 py-1 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs">Detail</button>

                      <button
                        v-if="user.role === 'mitra' && user.can_verify"
                        :disabled="verifyingUserId === user.id"
                        @click.prevent="verifyMitra(user.id)"
                        class="px-3 py-1 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 text-xs disabled:opacity-60"
                      >
                        {{ verifyingUserId === user.id ? 'Memverifikasi...' : 'Verifikasi' }}
                      </button>

                      <!-- ✅ Tombol toggle aktif di tabel -->
                      <button
                        v-if="user.id !== currentUserId"
                        @click.prevent="toggleActive(user.id, user.is_active)"
                        :disabled="togglingUserId === user.id"
                        class="px-3 py-1 rounded-md text-xs font-medium transition border"
                        :class="user.is_active
                          ? 'bg-orange-50 text-orange-600 hover:bg-orange-100 border-orange-200'
                          : 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200'"
                      >
                        {{ togglingUserId === user.id ? 'Memproses...' : (user.is_active ? 'Nonaktifkan' : 'Aktifkan') }}
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!isFiltering && !displayedUsers?.length">
                  <td colspan="8" class="py-6 px-4 text-center text-gray-500">Belum ada data pengguna.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="(users?.last_page || 1) > 1" class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-600">Tampilkan {{ users.per_page }} / Halaman</div>
            <div class="flex items-center justify-end gap-2">
              <button @click.prevent="goToPage(users.current_page - 1)" :disabled="!users.prev_page_url" class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50">Sebelumnya</button>

              <button
                v-for="page in users.last_page"
                :key="page"
                @click.prevent="goToPage(page)"
                class="px-3 py-2 text-sm rounded-lg border"
                :class="page === users.current_page ? 'bg-teal-600 text-white' : 'bg-white'"
              >
                {{ page }}
              </button>

              <button @click.prevent="goToPage(users.current_page + 1)" :disabled="!users.next_page_url" class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50">Berikutnya</button>
            </div>
          </div>
        </div>

        <!-- Modal Tambah Mitra -->
        <div v-if="showCreateModal && createType === 'mitra'" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="closeCreate">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
            <h3 class="text-xl font-bold mb-1">Tambah Pengguna Mitra</h3>
            <p class="text-sm text-gray-500 mb-4">Menyimpan akun login beserta profil mitra.</p>
            <form @submit.prevent="submitCreateMitra" class="space-y-3">
              <div>
                <label class="text-sm font-medium">Email login <span class="text-red-600">*</span></label>
                <input type="email" v-model="mitraForm.email" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="mitraForm.errors.email" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Nama perusahaan <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.nama_perusahaan" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="mitraForm.errors.nama_perusahaan" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.nama_perusahaan }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">PIC <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.pic" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="mitraForm.errors.pic" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.pic }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">No. handphone <span class="text-red-600">*</span></label>
                <input v-model="mitraForm.no_handphone" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="mitraForm.errors.no_handphone" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.no_handphone }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Alamat <span class="text-red-600">*</span></label>
                <textarea v-model="mitraForm.alamat" rows="3" class="w-full border rounded px-3 py-2 mt-1"></textarea>
                <p v-if="mitraForm.errors.alamat" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.alamat }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Password <span class="text-red-600">*</span></label>
                <div class="relative mt-1">
                  <input :type="showPasswordMitra ? 'text' : 'password'" v-model="mitraForm.password" class="w-full border rounded px-3 py-2 pr-10" />
                  <button type="button" @click="showPasswordMitra = !showPasswordMitra" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg v-if="showPasswordMitra" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                  </button>
                </div>
                <p v-if="mitraForm.errors.password" class="text-red-500 text-xs mt-1">{{ mitraForm.errors.password }}</p>
              </div>
              <div class="flex gap-2 justify-end pt-2">
                <button type="button" @click="closeCreate" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm">Batal</button>
                <button type="submit" :disabled="mitraForm.processing" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 text-sm">
                  {{ mitraForm.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Modal Tambah Admin -->
        <div v-if="showCreateModal && createType === 'admin'" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="closeCreate">
          <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-1">Tambah Pengguna Admin</h3>
            <p class="text-sm text-gray-500 mb-4">Menyimpan akun login beserta profil admin.</p>
            <form @submit.prevent="submitCreateAdmin" class="space-y-3">
              <div>
                <label class="text-sm font-medium">Email login <span class="text-red-600">*</span></label>
                <input type="email" v-model="adminForm.email" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="adminForm.errors.email" class="text-red-500 text-xs mt-1">{{ adminForm.errors.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Nama <span class="text-red-600">*</span></label>
                <input v-model="adminForm.username" placeholder="Nama lengkap admin" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="adminForm.errors.username" class="text-red-500 text-xs mt-1">{{ adminForm.errors.username }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Divisi <span class="text-red-600">*</span></label>
                <input v-model="adminForm.instansi" placeholder="Nama divisi / bidang" class="w-full border rounded px-3 py-2 mt-1" />
                <p v-if="adminForm.errors.instansi" class="text-red-500 text-xs mt-1">{{ adminForm.errors.instansi }}</p>
              </div>
              <div>
                <label class="text-sm font-medium">Password <span class="text-red-600">*</span></label>
                <div class="relative mt-1">
                  <input :type="showPasswordAdmin ? 'text' : 'password'" v-model="adminForm.password" class="w-full border rounded px-3 py-2 pr-10" />
                  <button type="button" @click="showPasswordAdmin = !showPasswordAdmin" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg v-if="showPasswordAdmin" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><path d="M3 3l18 18"/></svg>
                  </button>
                </div>
                <p v-if="adminForm.errors.password" class="text-red-500 text-xs mt-1">{{ adminForm.errors.password }}</p>
              </div>
              <div class="flex gap-2 justify-end pt-2">
                <button type="button" @click="closeCreate" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm">Batal</button>
                <button type="submit" :disabled="adminForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 text-sm">
                  {{ adminForm.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Modal Detail -->
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="closeDetail">
          <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

            <!-- Header -->
            <div class="bg-teal-700 px-6 py-5 flex items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                {{ (selectedUser?.admin?.nama ?? selectedUser?.mitra?.pic ?? selectedUser?.email ?? '?').charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-white font-semibold text-base truncate">
                  {{ selectedUser?.admin?.nama ?? selectedUser?.mitra?.pic ?? selectedUser?.email ?? '-' }}
                </p>
                <p class="text-teal-200 text-sm truncate">{{ selectedUser?.email ?? '-' }}</p>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                  <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="selectedUser?.role === 'admin' ? 'bg-purple-200 text-purple-800' : 'bg-sky-200 text-sky-800'">
                    {{ selectedUser?.role === 'admin' ? 'Administrator' : 'Mitra' }}
                  </span>
                  <!-- ✅ Badge status aktif di header modal -->
                  <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="selectedUser?.is_active ? 'bg-green-200 text-green-800' : 'bg-gray-300 text-gray-600'">
                    {{ selectedUser?.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </div>
              </div>
              <button @click="closeDetail" class="text-white/70 hover:text-white text-xl shrink-0">✕</button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 space-y-4">
              <div v-if="selectedUser?.role === 'admin' && selectedUser?.admin" class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi Admin</p>
                <div class="bg-gray-50 rounded-xl divide-y divide-gray-100">
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">Nama</span>
                    <span class="text-sm font-medium text-gray-800">{{ selectedUser.admin.nama ?? '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">Divisi</span>
                    <span class="text-sm font-medium text-gray-800">{{ selectedUser.admin.divisi ?? '-' }}</span>
                  </div>
                </div>
              </div>

              <div v-if="selectedUser?.mitra" class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi Mitra</p>
                <div class="bg-gray-50 rounded-xl divide-y divide-gray-100">
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">Nama Perusahaan</span>
                    <span class="text-sm font-medium text-gray-800 text-right max-w-[60%]">{{ selectedUser.mitra.nama_perusahaan ?? '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">PIC</span>
                    <span class="text-sm font-medium text-gray-800">{{ selectedUser.mitra.pic ?? '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">No. HP</span>
                    <span class="text-sm font-medium text-gray-800">{{ selectedUser.mitra.no_handphone ?? '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-xs text-gray-500">Alamat</span>
                    <span class="text-sm font-medium text-gray-800 text-right max-w-[60%]">{{ selectedUser.mitra.alamat ?? '-' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-5 flex justify-end gap-2 flex-wrap">
              <!-- ✅ Tombol toggle di modal -->
              <button
                v-if="selectedUser?.id !== currentUserId"
                @click="toggleActive(selectedUser?.id, selectedUser?.is_active)"
                :disabled="togglingUserId === selectedUser?.id"
                class="px-4 py-2 text-sm rounded-lg font-medium transition border"
                :class="selectedUser?.is_active
                  ? 'bg-orange-50 text-orange-600 hover:bg-orange-100 border-orange-200'
                  : 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200'"
              >
                {{ togglingUserId === selectedUser?.id ? 'Memproses...' : (selectedUser?.is_active ? 'Nonaktifkan' : 'Aktifkan') }}
              </button>

              <button
                v-if="selectedUser?.role !== 'admin'"
                @click="deleteUser(selectedUser?.id)"
                class="px-4 py-2 text-sm rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 font-medium transition"
              >
                Hapus
              </button>
              <button v-else class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200" disabled>
                Hapus
              </button>

              <button @click="closeDetail" class="px-4 py-2 text-sm rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition">
                Tutup
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.table-lines thead th { border-right: 1px solid rgba(255,255,255,0.18); }
.table-lines thead th:last-child { border-right: none; }
.table-lines tbody td { border-bottom: 1px solid rgba(15,23,42,0.06); }
</style>