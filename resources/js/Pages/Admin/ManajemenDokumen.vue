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

        <!-- TABS -->
        <div class="flex gap-10 px-6 pt-6 border-b text-gray-400 font-semibold overflow-x-auto whitespace-nowrap">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="activeTab = tab"
            :class="[
              'pb-2 transition',
              activeTab === tab
                ? 'text-teal-600 border-b-2 border-teal-600'
                : 'hover:text-gray-600'
            ]"
          >
            {{ tab }}
          </button>
        </div>

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
                  v-model="form.nama"
                  placeholder="Perjanjian Kerja Sama"
                  class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"
                />
              </div>

              <!-- Kategori -->
              <div>
                <label class="block text-gray-600 mb-1">Kategori Dokumen</label>
                <select v-model="form.kategori" class="w-full border rounded-lg px-4 py-2">
                  <option v-for="tab in tabs" :key="tab">
                    {{ tab }}
                  </option>
                </select>
              </div>

              <!-- Sub Kategori -->
              <div>
                <label class="block text-gray-600 mb-1">Sub Kategori</label>
                <select v-model="form.sub_kategori" class="w-full border rounded-lg px-4 py-2">
                  <option>Kesepakatan Kerja Sama</option>
                  <option>Nota Kesepakatan</option>
                  <option>Sinergi</option>
                  <option>Kerangka Acuan Kerja</option>
                </select>
              </div>

              <!-- Deskripsi -->
              <div>
                <label class="block text-gray-600 mb-1">Deskripsi Singkat</label>
                <textarea
                  v-model="form.deskripsi"
                  placeholder="Masukkan deskripsi singkat"
                  class="w-full border rounded-lg px-4 py-2"
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
                accept=".pdf"
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
                  PDF (Max. 10MB)
                </p>

              </div>
            </label>

            <p v-if="fileName" class="mt-3 text-sm text-teal-600">
              File dipilih: {{ fileName }}
            </p>
          </div>

        </form>

        <!-- BUTTON -->
        <div class="flex gap-4 px-6 pb-6 border-t pt-6">
          <button
            @click="submitForm"
            class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold"
          >
            + Simpan Template
          </button>

          <button
            @click="resetForm"
            class="bg-gray-200 px-6 py-3 rounded-lg font-semibold"
          >
            Reset
          </button>
        </div>

      </div>

    </div>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

/* =========================
   TABS
========================= */
const tabs = [
  'KSDD (Kerja Sama Daerah Dengan Daerah Lain)',
  'KSDPK (Kerja Sama Daerah Dengan Pihak Ketiga)',
  'Sinergi',
  'Kerangka Acuan Kerja'
]

const activeTab = ref(tabs[0])

/* =========================
   FORM
========================= */
const form = ref({
  nama: '',
  kategori: tabs[0],
  sub_kategori: '',
  deskripsi: '',
  template_file: null
})

const fileName = ref(null)

/* =========================
   HANDLE FILE
========================= */
const handleFile = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.template_file = file
    fileName.value = file.name
  }
}

/* =========================
   SYNC TAB → FORM
========================= */
watch(activeTab, (val) => {
  form.value.kategori = val
})

/* =========================
   SUBMIT
========================= */
const submitForm = () => {
  if (!form.value.template_file) {
    alert('File wajib diupload!')
    return
  }

  const data = new FormData()
  data.append('template_file', form.value.template_file)

  router.post(route('admin.manajemen-dokumen.store'), data)
}

/* =========================
   RESET
========================= */
const resetForm = () => {
  form.value = {
    nama: '',
    kategori: tabs[0],
    sub_kategori: '',
    deskripsi: '',
    template_file: null
  }
  fileName.value = null
}
</script>