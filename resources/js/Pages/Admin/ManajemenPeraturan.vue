<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    peraturans: {
        type: Array,
        default: () => []
    }
})

const showModal = ref(false)
const editingPeraturan = ref(null)
const form = ref({
    judul: '',
    file: null,
    thumbnail: null
})

const isEditing = computed(() => Boolean(editingPeraturan.value))

const resetForm = () => {
    form.value = {
        judul: '',
        file: null,
        thumbnail: null
    }
    editingPeraturan.value = null
}

const openCreateModal = () => {
    resetForm()
    showModal.value = true
}

const openEditModal = (item) => {
    editingPeraturan.value = item
    form.value = {
        judul: item.judul ?? '',
        file: null,
        thumbnail: null
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    resetForm()
}

const fileNameFromPath = (path) => {
    if (!path) {
        return '-'
    }

    return path.split('/').pop()
}

const submit = () => {
    if (!form.value.judul) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Seluruh data wajib diisi.',
            confirmButtonColor: '#0d9488'
        })
        return
    }

    if (!isEditing.value && !form.value.file) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'File dokumen wajib dipilih saat menambah peraturan baru (PDF, DOC, DOCX, XLSX, dll - Max 10MB).',
            confirmButtonColor: '#0d9488'
        })
        return
    }

    const payload = new FormData()
    payload.append('judul', form.value.judul)

    if (form.value.file) {
        payload.append('file', form.value.file)
    }

    if (form.value.thumbnail) {
        payload.append('thumbnail', form.value.thumbnail)
    }

    try {
        const request = isEditing.value
            ? router.post(route('admin.manajemen-peraturan.update', editingPeraturan.value.id), payload, {
                  forceFormData: true,
                  preserveScroll: true,
                  onSuccess: () => {
                      Swal.fire({
                          icon: 'success',
                          title: 'Berhasil!',
                          text: 'Peraturan berhasil diperbarui',
                          timer: 1500,
                          showConfirmButton: false
                      })

                      closeModal()
                  },
                  onError: (errors) => {
                      console.error('Update Error:', errors)
                      const errorMessage = Object.values(errors).flat().join(', ') || 'Gagal memperbarui peraturan'
                      Swal.fire({
                          icon: 'error',
                          title: 'Gagal!',
                          text: errorMessage,
                          confirmButtonColor: '#dc2626'
                      })
                  }
              })
            : router.post(route('admin.manajemen-peraturan.store'), payload, {
                  forceFormData: true,
                  preserveScroll: true,
                  onSuccess: () => {
                      Swal.fire({
                          icon: 'success',
                          title: 'Berhasil!',
                          text: 'Peraturan berhasil ditambahkan',
                          timer: 1500,
                          showConfirmButton: false
                      })

                      closeModal()
                  },
                  onError: (errors) => {
                      console.error('Store Error:', errors)
                      const errorMessage = Object.values(errors).flat().join(', ') || 'Gagal menyimpan peraturan'
                      Swal.fire({
                          icon: 'error',
                          title: 'Gagal!',
                          text: errorMessage,
                          confirmButtonColor: '#dc2626'
                      })
                  }
              })

        return request
    } catch (error) {
        console.error('Submit Error:', error)
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan dokumen',
            confirmButtonColor: '#dc2626'
        })
    }
}

const showPreview = ref(false)
const previewItem = ref(null)

const openPreview = (item) => {
    previewItem.value = item
    showPreview.value = true
}

const closePreview = () => {
    showPreview.value = false
    previewItem.value = null
}

const destroy = (item) => {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus dokumen?',
        text: `Dokumen "${item.judul}" akan dihapus permanen.`,
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (!result.isConfirmed) {
            return
        }

        router.delete(route('admin.manajemen-peraturan.destroy', item.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus',
                    text: 'Peraturan berhasil dihapus',
                    timer: 1200,
                    showConfirmButton: false
                })
            }
        })
    })
}
</script>

<template>
  <AdminLayout title="Manajemen Peraturan">
    <div class="mx-auto max-w-7xl space-y-6">
      <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <h2 class="text-3xl font-bold text-[#0C505C]">
              Manajemen Peraturan
            </h2>
            <p class="mt-2 max-w-2xl text-slate-600">
              Tambahkan, perbarui, atau hapus dokumen peraturan yang tampil di halaman landing.
            </p>
          </div>

          <button
            @click="openCreateModal"
            class="inline-flex items-center justify-center rounded-full bg-[#0C505C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#0a424b]"
          >
            + Tambah Peraturan
          </button>
        </div>
      </div>

      <div v-if="peraturans.length" class="grid gap-6 xl:grid-cols-2">
        <article
          v-for="item in peraturans"
          :key="item.id"
          class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200"
        >
          <div class="grid gap-0 md:grid-cols-[220px,1fr]">
            <div class="min-h-55 bg-slate-100">
              <img
                v-if="item.thumbnail"
                :src="`/storage/${item.thumbnail}`"
                :alt="item.judul"
                class="h-full w-full object-cover"
              />

              <div v-else class="flex h-full items-center justify-center px-6 text-center text-sm font-medium text-slate-400">
                Tidak ada thumbnail
              </div>
            </div>

            <div class="p-6">
              <div class="flex flex-col gap-4">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-600">
                    Dokumen Peraturan
                  </p>
                  <h3 class="mt-2 text-xl font-semibold text-slate-800">
                    {{ item.judul }}
                  </h3>
                </div>

                <div class="space-y-2 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                  <p><span class="font-semibold text-slate-700">File Dokumen:</span> {{ fileNameFromPath(item.file) }}</p>
                  <p><span class="font-semibold text-slate-700">Thumbnail:</span> {{ fileNameFromPath(item.thumbnail) }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                  <button
                    @click="openPreview(item)"
                    class="inline-flex items-center rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-700"
                  >
                    Lihat PDF
                  </button>

                  <button
                    @click="openEditModal(item)"
                    class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                  >
                    Edit
                  </button>

                  <button
                    @click="destroy(item)"
                    class="inline-flex items-center rounded-full border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>

      <div
        v-else
        class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-slate-500"
      >
        Belum ada dokumen peraturan. Klik tombol tambah untuk mulai mengisi.
      </div>
    </div>

    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8"
    >
      <div class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl">
        <div class="mb-6 flex items-start justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-slate-800">
              {{ isEditing ? 'Edit Peraturan' : 'Tambah Peraturan' }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
              {{ isEditing ? 'Perbarui data dokumen yang sudah ada.' : 'Unggah dokumen peraturan baru ke landing page.' }}
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 transition hover:bg-slate-200"
          >
            Tutup
          </button>
        </div>

        <div class="space-y-5">
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">
              Judul Peraturan
            </label>
            <input
              v-model="form.judul"
              type="text"
              placeholder="Contoh: Peraturan Kerja Sama Daerah"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
            />
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">
              File Dokumen (PDF, DOC, DOCX, XLSX, dll) {{ isEditing ? '(opsional bila tidak diganti)' : '' }}
            </label>
            <input
              type="file"
              @change="(e) => (form.file = e.target.files?.[0] ?? null)"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm"
            />
            <p class="mt-1 text-xs text-slate-500">Format: PDF, DOC, DOCX, XLSX, PPT, TXT, dll (Max 10MB)</p>

            <p v-if="isEditing && editingPeraturan?.file" class="mt-2 text-xs text-slate-500">
              File saat ini: {{ fileNameFromPath(editingPeraturan.file) }}
            </p>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">
              Thumbnail {{ isEditing ? '(opsional bila tidak diganti)' : '(opsional)' }}
            </label>
            <input
              type="file"
              accept="image/*"
              @change="(e) => (form.thumbnail = e.target.files?.[0] ?? null)"
              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm"
            />

            <p v-if="isEditing && editingPeraturan?.thumbnail" class="mt-2 text-xs text-slate-500">
              Thumbnail saat ini: {{ fileNameFromPath(editingPeraturan.thumbnail) }}
            </p>
          </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
          <button
            @click="closeModal"
            class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
          >
            Batal
          </button>

          <button
            @click="submit"
            class="rounded-full bg-[#0C505C] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0a424b]"
          >
            {{ isEditing ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- PDF PREVIEW MODAL -->
    <div
      v-if="showPreview && previewItem"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8"
    >
      <div class="w-full max-w-5xl h-[80vh] bg-white rounded-2xl shadow-2xl flex flex-col">
        <!-- HEADER -->
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
          <h3 class="text-lg font-semibold text-slate-800 truncate">
            {{ previewItem.judul }}
          </h3>
          <button
            @click="closePreview"
            class="rounded-full bg-slate-100 px-3 py-1 text-xl font-semibold text-slate-600 transition hover:bg-slate-200"
          >
            ✕
          </button>
        </div>

        <!-- PDF VIEWER -->
        <iframe
          :src="`/storage/${previewItem.file}`"
          class="flex-1 border-0"
        ></iframe>

        <!-- FOOTER -->
        <div class="border-t border-slate-200 px-6 py-3 flex justify-end gap-3">
          <a
            :href="`/storage/${previewItem.file}`"
            target="_blank"
            class="inline-flex items-center rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-700"
          >
            Unduh PDF
          </a>
          <button
            @click="closePreview"
            class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
