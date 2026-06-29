<template>
  <AdminLayout title="Manajemen Dokumen">

    <div class="max-w-6xl mx-auto">

      <!-- TITLE -->
      <div class="mb-6">
        <h2 class="text-3xl font-semibold text-teal-700">
          Manajemen Template Dokumen Kerjasama
        </h2>
        <p class="text-gray-500 mt-1">
          Kelola template dokumen yang ditampilkan di halaman publik
        </p>
      </div>

      <!-- CARD -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <!-- FORM -->
        <form @submit.prevent="submitForm" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-10">

          <!-- LEFT -->
          <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-6">
              Tambah Template Baru
            </h3>

            <div class="space-y-5">

              <!-- Nama -->
              <div>
                <label class="block text-gray-600 mb-1">Nama Dokumen</label>
                <input
                  type="text"
                  v-model="form.judul"
                  placeholder="Perjanjian Kerja Sama"
                  class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"
                />
              </div>

              <!-- Kategori -->
              <div>
                <label class="block text-gray-600 mb-1">Kategori Dokumen</label>
                <select
                  v-model="form.id_kategori"
                  class="w-full border rounded-lg px-4 py-2"
                >
                  <option value="" disabled>Pilih kategori</option>
                  <option
                    v-for="kategori in kategoris"
                    :key="kategori.id_kategori"
                    :value="kategori.id_kategori"
                  >
                    {{ kategori.nama_kategori }}
                  </option>
                </select>
              </div>

              <!-- Deskripsi -->
              <div>
                <label class="block text-gray-600 mb-1">Deskripsi Singkat</label>
                <textarea
                  v-model="form.deskripsi"
                  placeholder="Masukkan deskripsi singkat"
                  class="w-full border rounded-lg px-4 py-2"
                  rows="4"
                ></textarea>
              </div>

            </div>
          </div>

          <!-- RIGHT (UPLOAD) -->
          <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4">
              Upload File Template
            </h3>

            <label
              class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl h-80 cursor-pointer hover:bg-gray-50 transition hover:border-teal-400"
            >
              <input
                type="file"
                class="hidden"
                @change="handleFile"
                accept=".pdf,.docx"
              />

              <div class="text-center">

                <!-- ICON ABU -->
                <div class="mb-4">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-16 h-16 text-gray-400 mx-auto"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16" />
                  </svg>
                </div>

                <p class="font-semibold text-gray-600">
                  Drag & drop atau klik untuk upload
                </p>
                <p class="text-sm text-gray-400">
                  PDF atau DOCX (Max. 10MB)
                </p>

                <div
                  v-if="fileName"
                  class="mt-4 inline-flex max-w-full items-center rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700"
                >
                  <span class="block break-words whitespace-normal">{{ fileName }}</span>
                </div>

              </div>
            </label>
          </div>

        </form>

        <!-- BUTTON -->
        <div class="flex gap-4 px-6 pb-6 border-t pt-6">
          <button
            type="button"
            @click="submitForm"
            class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold"
          >
            + Simpan Template
          </button>

          <button
            type="button"
            @click="resetForm"
            class="bg-gray-200 px-6 py-3 rounded-lg font-semibold"
          >
            Reset
          </button>
        </div>

      </div>

      <!-- LIST UPLOADED TEMPLATES -->
      <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-semibold text-gray-700">
              Daftar Dokumen yang Tampil di Halaman Publik
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              Hanya template aktif yang berkategori dan memang muncul di halaman Dokumen
            </p>
          </div>

          <div class="text-sm text-gray-500">
            Total: <span class="font-semibold text-gray-700">{{ publicTemplateCount }}</span>
          </div>
        </div>

        <div v-if="publicDokumenGroups.length" class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
          <section
            v-for="group in publicDokumenGroups"
            :key="group.nama_kategori"
            class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
          >
            <h4 class="text-lg font-semibold text-[#0C505C] text-center">
              {{ group.label }}
            </h4>

            <p v-if="group.deskripsi" class="mt-2 text-sm text-gray-500 text-center">
              {{ group.deskripsi }}
            </p>

            <div class="mt-5 space-y-3">
              <div
                v-for="item in group.items"
                :key="item.id"
                class="rounded-xl border border-gray-200 bg-[#D4E4E8] p-4"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <p class="font-semibold text-[#0C505C] truncate">
                      {{ item.title }}
                    </p>
                    <p v-if="item.description" class="mt-1 text-xs text-slate-600 line-clamp-2">
                      {{ item.description }}
                    </p>
                  </div>

                  <span class="inline-flex shrink-0 rounded-full bg-white px-2 py-1 text-[10px] font-bold text-[#0C8BA3]">
                    {{ item.badge }}
                  </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                  <a
                    :href="item.preview"
                    target="_blank"
                    class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-[#0C505C] hover:bg-gray-50"
                  >
                    Preview
                  </a>
                  <button
                    type="button"
                    @click="openEditTemplate(item)"
                    class="inline-flex items-center rounded-lg border border-white/60 px-3 py-1.5 text-xs font-semibold text-[#0C505C] hover:bg-white"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    @click="deleteTemplate(item)"
                    class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div v-else class="px-6 py-10 text-center text-gray-500">
          Belum ada dokumen yang tampil di halaman publik.
        </div>
      </div>

      <div
        v-if="isEditModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="closeEditModal"
      >
        <div class="w-full max-w-3xl rounded-xl bg-white shadow-xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between border-b px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Edit Template Dokumen</h3>
            <button
              type="button"
              @click="closeEditModal"
              class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitEditForm" class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">
            <div class="space-y-5">
              <div>
                <label class="mb-1 block text-gray-600">Nama Dokumen</label>
                <input
                  v-model="editForm.judul"
                  type="text"
                  class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="mb-1 block text-gray-600">Kategori Dokumen</label>
                <select
                  v-model="editForm.id_kategori"
                  class="w-full rounded-lg border px-4 py-2"
                >
                  <option value="" disabled>Pilih kategori</option>
                  <option
                    v-for="kategori in kategoris"
                    :key="kategori.id_kategori"
                    :value="kategori.id_kategori"
                  >
                    {{ kategori.nama_kategori }}
                  </option>
                </select>
              </div>

              <div>
                <label class="mb-1 block text-gray-600">Deskripsi Singkat</label>
                <textarea
                  v-model="editForm.deskripsi"
                  rows="4"
                  class="w-full rounded-lg border px-4 py-2"
                ></textarea>
              </div>
            </div>

            <div>
              <h4 class="mb-4 text-lg font-semibold text-gray-700">Upload File Template</h4>
              <label
                class="flex h-72 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 transition hover:border-teal-400 hover:bg-gray-50"
              >
                <input
                  type="file"
                  class="hidden"
                  @change="handleEditFile"
                  accept=".pdf,.docx"
                />
                <div class="text-center">
                  <div class="mb-4">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="mx-auto h-14 w-14 text-gray-400"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="1.5"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16" />
                    </svg>
                  </div>
                  <p class="font-semibold text-gray-600">Drag & drop atau klik untuk upload</p>
                  <p class="text-sm text-gray-400">PDF atau DOCX (Max. 10MB)</p>
                  <div
                    v-if="editFileLabel"
                    class="mt-4 inline-flex max-w-full items-center rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700"
                  >
                    <span class="truncate">{{ editFileLabel }}</span>
                  </div>
                </div>
              </label>
            </div>

            <div class="col-span-1 flex justify-end gap-3 border-t pt-5 lg:col-span-2">
              <button
                type="button"
                @click="closeEditModal"
                class="rounded-lg bg-gray-200 px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-300"
              >
                Batal
              </button>
              <button
                type="submit"
                class="rounded-lg bg-teal-600 px-5 py-2.5 font-semibold text-white hover:bg-teal-700"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  templates: { type: Array, default: () => [] },
  publicDokumenGroups: { type: Array, default: () => [] },
  kategoris: { type: Array, default: () => [] },
})

const publicTemplateCount = computed(() =>
  props.publicDokumenGroups.reduce(
    (count, group) => count + (group.items?.length ?? 0),
    0
  )
)

const form = ref({
  judul: '',
  id_kategori: '',
  deskripsi: '',
  template_file: null,
})

const fileName = ref(null)
const isEditModalOpen = ref(false)
const selectedTemplateId = ref(null)
const editFileLabel = ref(null)
const editForm = ref({
  judul: '',
  id_kategori: '',
  deskripsi: '',
  template_file: null,
})

const handleFile = (e) => {
  const file = e.target.files[0]

  if (file) {
    form.value.template_file = file
    form.value.judul = file.name.replace(/\.[^.]+$/, '')
    fileName.value = file.name
  }
}

const submitForm = () => {
  if (!form.value.template_file) {
    Swal.fire('Oops...', 'File wajib diupload!', 'warning')
    return
  }

  const data = new FormData()
  data.append('judul', form.value.judul)
  data.append('id_kategori', form.value.id_kategori)
  data.append('deskripsi', form.value.deskripsi)
  data.append('template_file', form.value.template_file)

  router.post(route('admin.manajemen-dokumen.store'), data, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Berhasil!', 'Template dokumen berhasil diupload.', 'success')
      resetForm()
    },
    onError: (errors) => {
      const firstMessage = Object.values(errors)[0]
      const errorMessage = firstMessage
        ? (Array.isArray(firstMessage) ? firstMessage[0] : firstMessage)
        : null
      Swal.fire(
        'Gagal!',
        errorMessage || 'Gagal mengupload template dokumen.',
        'error'
      )
    },
  })
}

const resetForm = () => {
  form.value = {
    judul: '',
    id_kategori: '',
    deskripsi: '',
    template_file: null,
  }
  fileName.value = null
}

const openEditTemplate = (item) => {
  selectedTemplateId.value = item.id
  editForm.value = {
    judul: item.judul ?? item.title ?? '',
    id_kategori: item.id_kategori ?? '',
    deskripsi: item.deskripsi ?? item.description ?? '',
    template_file: null,
  }
  editFileLabel.value = item.nama_file ?? null
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  selectedTemplateId.value = null
  editForm.value = {
    judul: '',
    id_kategori: '',
    deskripsi: '',
    template_file: null,
  }
  editFileLabel.value = null
}

const handleEditFile = (e) => {
  const file = e.target.files?.[0]
  if (!file) {
    return
  }

  editForm.value.template_file = file
  editFileLabel.value = file.name
}

const submitEditForm = () => {
  if (!selectedTemplateId.value) {
    return
  }

  const data = new FormData()
  data.append('_method', 'put')
  data.append('judul', editForm.value.judul ?? '')
  data.append('deskripsi', editForm.value.deskripsi ?? '')
  if (editForm.value.id_kategori) {
    data.append('id_kategori', editForm.value.id_kategori)
  }

  if (editForm.value.template_file) {
    data.append('template_file', editForm.value.template_file)
  }

  router.post(route('admin.manajemen-dokumen.update', selectedTemplateId.value), data, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Berhasil!', 'Template dokumen berhasil diperbarui.', 'success')
      closeEditModal()
      router.reload({ preserveScroll: true })
    },
    onError: (errors) => {
      const firstMessage = Object.values(errors)[0]
      const errorMessage = firstMessage
        ? (Array.isArray(firstMessage) ? firstMessage[0] : firstMessage)
        : null
      Swal.fire(
        'Gagal!',
        errorMessage || 'Gagal memperbarui template dokumen.',
        'error'
      )
    },
  })
}

const deleteTemplate = (item) => {
  Swal.fire({
    title: 'Hapus dokumen?',
    text: `Dokumen "${item.title}" akan dihapus permanen.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (!result.isConfirmed) {
      return
    }

    router.delete(route('admin.manajemen-dokumen.destroy', item.id), {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire('Terhapus!', 'Template dokumen berhasil dihapus.', 'success')
        router.reload({ preserveScroll: true })
      },
      onError: () => {
        Swal.fire('Gagal!', 'Gagal menghapus template dokumen.', 'error')
      },
    })
  })
}
</script>
