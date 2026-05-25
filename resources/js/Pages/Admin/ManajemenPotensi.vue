<template>
  <AdminLayout title="Manajemen Potensi">

    <div class="max-w-6xl mx-auto">

      <!-- TITLE -->
      <div class="mb-6">
        <h2 class="text-3xl font-semibold text-teal-700">
          Potensi Unggulan Kabupaten Boyolali
        </h2>
        <p class="text-gray-500 mt-1">
          Kelola konten potensi daerah yang ditampilkan di halaman publik
        </p>
      </div>

      <!-- TABS -->
      <div class="bg-white rounded-xl shadow px-4 py-3 flex gap-2 overflow-x-auto mb-6">
        <button
          v-for="kat in kategoriList"
          :key="kat"
          @click="changeKategori(kat)"
          :class="[
            'px-4 py-2 rounded-lg font-semibold whitespace-nowrap transition-all',
            activeKategori === kat
              ? 'bg-teal-600 text-white'
              : 'text-gray-600 hover:bg-gray-100'
          ]"
        >
          {{ kat }}
        </button>
      </div>

      <!-- FORM TAMBAH POTENSI BARU -->
      <div class="bg-white rounded-xl shadow p-6">
        
        <!-- HEADER -->
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
          <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <h3 class="text-xl font-bold text-gray-800">
            Tambah Potensi Baru
          </h3>
        </div>

        <!-- FORM INPUT -->
        <form @submit.prevent="submitForm" class="space-y-6">

          <!-- NAMA PAKET -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Nama Potensi</label>
            <input
              v-model="newPotensi.judul"
              type="text"
              placeholder="Contoh: Lahan Subur dan Produktif"
              class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-600 bg-gray-50"
            />
          </div>

          <!-- DESKRIPSI -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Deskripsi</label>
            <textarea
              v-model="newPotensi.deskripsi"
              placeholder="Tuliskan deskripsi potensi..."
              rows="4"
              class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-600 bg-gray-50"
            ></textarea>
          </div>

          <!-- GAMBAR -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Upload Gambar</label>
            <label
              class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl h-48 cursor-pointer hover:bg-gray-50 overflow-hidden transition-all"
            >
              <input
                type="file"
                class="hidden"
                @change="handleImage"
                accept="image/png,image/jpeg"
              />

              <div v-if="!newPotensi.imagePreview" class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <p class="font-semibold text-gray-600">Klik untuk upload gambar</p>
                <p class="text-sm text-gray-400">PNG, JPG (Max. 5MB)</p>
              </div>

              <img
                v-if="newPotensi.imagePreview"
                :src="newPotensi.imagePreview"
                class="w-full h-full object-cover"
              />
            </label>
          </div>

          <!-- BUTTONS -->
          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="resetForm"
              class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
            >
              Reset
            </button>
            <button
              type="submit"
              class="px-6 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-all flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Tambah Potensi
            </button>
          </div>

        </form>

      </div>
        </div>

        <!-- TABEL POTENSI -->
        <div class="bg-white rounded-xl shadow mb-8 mt-6">
        
          <!-- HEADER -->
          <div class="flex justify-between items-center p-6">
            <div>
              <h3 class="text-lg font-bold text-gray-800">
                Potensi {{ activeKategori }}
              </h3>
            </div>
            <button
              @click="editModal = false"
              class="bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700 transition-all flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Edit Deskripsi
            </button>
          </div>

          <!-- TABLE -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="px-6 py-4 text-left font-semibold text-gray-700">Nama Potensi</th>
                  <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-4 text-center font-semibold text-gray-700">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="potensiList.length === 0" class="hover:bg-gray-50">
                  <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    Belum ada potensi untuk kategori ini
                  </td>
                </tr>
                <tr v-for="item in potensiList" :key="item.id_potensi" class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 font-medium text-gray-800">{{ item.judul }}</td>
                  <td class="px-6 py-4">
                    <span v-if="item.status_tampil" class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                      Aktif
                    </span>
                    <span v-else class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                      Nonaktif
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center space-x-2 flex justify-center">
                    <button
                      @click="editPotensi(item)"
                      class="bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-teal-700 transition-all"
                    >
                      Edit
                    </button>
                    <button
                      @click="deletePotensi(item.id_potensi)"
                      class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 transition-all"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    <!-- MODAL DETAIL POTENSI -->
    <div v-if="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeEditModal">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-lg font-semibold">Detail Potensi</h3>
          <button
            @click="closeEditModal"
            class="text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- DETAIL CONTENT -->
        <div class="space-y-4 mb-6">
          <!-- NAMA POTENSI -->
          <div>
            <strong class="text-gray-700">Nama Potensi:</strong>
            <p class="text-gray-600">{{ selectedPotensi.judul }}</p>
          </div>

          <!-- DESKRIPSI -->
          <div>
            <strong class="text-gray-700">Deskripsi:</strong>
            <p class="text-gray-600 whitespace-pre-wrap">{{ selectedPotensi.deskripsi }}</p>
          </div>

          <!-- GAMBAR -->
          <div>
            <strong class="text-gray-700">Gambar:</strong>
            <img
              v-if="selectedPotensi.gambar_url"
              :src="selectedPotensi.gambar_url"
              class="w-full h-32 object-cover rounded-lg mt-2"
              alt="Potensi"
            />
            <p v-else class="text-gray-500 text-sm">Belum ada gambar</p>
          </div>

          <!-- STATUS -->
          <div>
            <strong class="text-gray-700">Status:</strong>
            <span
              class="ml-2 px-3 py-1 rounded-full text-xs font-semibold"
              :class="selectedPotensi.status_tampil ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
            >
              {{ selectedPotensi.status_tampil ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
        </div>

        <!-- BUTTONS -->
        <div class="flex justify-end gap-3">
          <button
            @click="openEditForm"
            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all"
          >
            Edit
          </button>
          <button
            @click="deletePotensi(selectedPotensi.id_potensi)"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all"
          >
            Hapus
          </button>
          <button
            @click="closeEditModal"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT POTENSI (Form) -->
    <div v-if="editingPotensi" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="cancelEdit">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-lg font-semibold">Edit Potensi</h3>
          <button
            @click="cancelEdit"
            class="text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- EDIT FORM -->
        <form @submit.prevent="submitEditForm" class="space-y-4">
          <!-- NAMA POTENSI -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Nama Potensi</label>
            <input
              v-model="selectedPotensi.judul"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              placeholder="Masukkan nama potensi"
            />
          </div>

          <!-- DESKRIPSI -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Deskripsi</label>
            <textarea
              v-model="selectedPotensi.deskripsi"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 h-24 resize-none"
              placeholder="Masukkan deskripsi potensi"
            ></textarea>
          </div>

          <!-- GAMBAR -->
          <div>
            <label class="block font-semibold text-gray-700 mb-2">Gambar Potensi</label>
            <div class="space-y-3">
              <!-- Preview Gambar Lama -->
              <div v-if="selectedPotensi.gambar_url && !selectedPotensi.editImagePreview" class="relative">
                <img
                  :src="selectedPotensi.gambar_url"
                  class="w-full h-32 object-cover rounded-lg"
                  alt="Current potensi"
                />
                <button
                  type="button"
                  @click="selectedPotensi.editImagePreview = null; selectedPotensi.editGambar = null"
                  class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-all"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>

              <!-- Upload Area -->
              <label
                class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg h-32 cursor-pointer hover:bg-gray-50 transition-all"
              >
                <input
                  type="file"
                  class="hidden"
                  @change="handleEditImage"
                  accept="image/png,image/jpeg"
                />

                <div v-if="!selectedPotensi.editImagePreview" class="text-center">
                  <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  <p class="text-sm text-gray-600 font-semibold">Klik untuk upload</p>
                </div>

                <img
                  v-if="selectedPotensi.editImagePreview"
                  :src="selectedPotensi.editImagePreview"
                  class="w-full h-full object-cover rounded-lg"
                />
              </label>
            </div>
          </div>

          <!-- STATUS -->
          <div>
            <label class="flex items-center gap-3">
              <input
                v-model="selectedPotensi.status_tampil"
                type="checkbox"
                class="w-4 h-4 text-teal-600 rounded"
              />
              <span class="font-semibold text-gray-700">Tampilkan di halaman publik</span>
            </label>
          </div>

          <!-- BUTTONS -->
          <div class="flex justify-end gap-3 pt-6 border-t">
            <button
              type="button"
              @click="cancelEdit"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-all flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'

const page = usePage()

const kategoriList = page.props.kategori_list || []
const activeKategori = ref(page.props.active_kategori || '')
const editModal = ref(false)
const editingPotensi = ref(false)
const selectedPotensi = ref({
  id_potensi: null,
  judul: '',
  deskripsi: '',
  gambar_url: null,
  editGambar: null,
  editImagePreview: null,
  status_tampil: true,
  poin: []
})

// Potensi yang sudah ada
const potensiData = ref(page.props.potensi_list || [])

const potensiList = computed(() => {
  return potensiData.value.filter(p => p.kategori === activeKategori.value)
})

// Form tambah potensi baru
const newPotensi = ref({
  judul: '',
  deskripsi: '',
  gambar: null,
  imagePreview: null
})

const handleImage = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    newPotensi.value.gambar = file
    newPotensi.value.imagePreview = URL.createObjectURL(file)
  }
}

const changeKategori = (kat) => {
  activeKategori.value = kat
}

const handleEditImage = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    selectedPotensi.value.editGambar = file
    selectedPotensi.value.editImagePreview = URL.createObjectURL(file)
  }
}

const editPotensi = (item) => {
  selectedPotensi.value = {
    id_potensi: item.id_potensi,
    judul: item.judul,
    deskripsi: item.deskripsi,
    gambar_url: item.gambar_url,
    editGambar: null,
    editImagePreview: null,
    status_tampil: item.status_tampil,
    poin: item.poin || []
  }
  editModal.value = true
  editingPotensi.value = false
}

const openEditForm = () => {
  editingPotensi.value = true
}

const cancelEdit = () => {
  editingPotensi.value = false
}

const closeEditModal = () => {
  editModal.value = false
  editingPotensi.value = false
  selectedPotensi.value = {
    id_potensi: null,
    judul: '',
    deskripsi: '',
    gambar_url: null,
    editGambar: null,
    editImagePreview: null,
    status_tampil: true,
    poin: []
  }
}

const submitEditForm = () => {
  if (!selectedPotensi.value.judul.trim()) {
    Swal.fire('Error!', 'Nama Potensi harus diisi', 'error')
    return
  }

  if (!selectedPotensi.value.deskripsi.trim()) {
    Swal.fire('Error!', 'Deskripsi harus diisi', 'error')
    return
  }

  const data = new FormData()
  data.append('judul', selectedPotensi.value.judul)
  data.append('deskripsi', selectedPotensi.value.deskripsi)
  data.append('status_tampil', selectedPotensi.value.status_tampil ? 1 : 0)

  if (selectedPotensi.value.editGambar) {
    data.append('gambar', selectedPotensi.value.editGambar)
  }

  router.put(route('admin.manajemen-potensi.update', selectedPotensi.value.id_potensi), data, {
    onSuccess: () => {
      closeEditModal()
      Swal.fire('Berhasil!', 'Potensi berhasil diupdate', 'success').then(() => {
        router.visit(route('admin.manajemen-potensi.index'), { preserveState: false })
      })
    },
    onError: () => {
      Swal.fire('Error!', 'Gagal mengupdate potensi', 'error')
    }
  })
}

const deletePotensi = (id) => {
  Swal.fire({
    title: 'Hapus Potensi?',
    text: 'Data ini tidak dapat dikembalikan',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.manajemen-potensi.destroy', id), {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Potensi berhasil dihapus', 'success').then(() => {
            router.visit(route('admin.manajemen-potensi.index'), { preserveState: false })
          })
        },
        onError: () => {
          Swal.fire('Error!', 'Gagal menghapus potensi', 'error')
        }
      })
    }
  })
}

const submitForm = () => {
  if (!newPotensi.value.judul.trim()) {
    Swal.fire('Error!', 'Nama Potensi harus diisi', 'error')
    return
  }

  if (!newPotensi.value.deskripsi.trim()) {
    Swal.fire('Error!', 'Deskripsi harus diisi', 'error')
    return
  }

  const data = new FormData()
  data.append('kategori', activeKategori.value)
  data.append('judul', newPotensi.value.judul)
  data.append('deskripsi', newPotensi.value.deskripsi)
  data.append('status_tampil', true)

  if (newPotensi.value.gambar) {
    data.append('gambar', newPotensi.value.gambar)
  }

  router.post(route('admin.manajemen-potensi.store'), data, {
    onSuccess: () => {
      resetForm()
      Swal.fire('Berhasil!', 'Potensi baru ditambahkan', 'success').then(() => {
        router.visit(route('admin.manajemen-potensi.index'), { preserveState: false })
      })
    },
    onError: () => {
      Swal.fire('Error!', 'Gagal menambahkan potensi', 'error')
    }
  })
}

const resetForm = () => {
  newPotensi.value = {
    judul: '',
    deskripsi: '',
    gambar: null,
    imagePreview: null
  }
}
</script>