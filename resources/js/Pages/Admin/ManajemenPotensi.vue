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
      <div class="bg-white rounded-xl shadow px-4 py-3 flex gap-6 overflow-x-auto mb-6">
        <button
          v-for="kat in kategoriList"
          :key="kat"
          @click="changeKategori(kat)"
          :class="[
            'px-4 py-2 rounded-lg font-semibold whitespace-nowrap',
            activeKategori === kat
              ? 'bg-teal-600 text-white'
              : 'text-gray-600 hover:bg-gray-100'
          ]"
        >
          {{ kat }}
        </button>
      </div>

      <!-- CARD -->
      <div class="bg-white rounded-xl shadow p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-xl font-semibold text-gray-700">
              Informasi Utama
            </h3>
            <p class="text-gray-400 text-sm">
              Tambahkan beberapa potensi dalam satu kategori
            </p>
          </div>

          <button
            @click="addForm"
            class="bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold"
          >
            + Tambah Potensi
          </button>
        </div>

        <!-- FORM -->
        <form @submit.prevent="submitForm" class="space-y-6">

          <div
            v-for="(item, index) in form"
            :key="index"
            class="border rounded-xl p-5 space-y-4"
          >

            <!-- HEADER CARD -->
            <div class="flex justify-between items-center">
              <h3 class="font-semibold text-gray-700">
                Data Potensi {{ index + 1 }}
              </h3>

              <button
                v-if="form.length > 1"
                @click.prevent="removeForm(index)"
                class="text-red-500"
              >
                Hapus
              </button>
            </div>

            <!-- Judul -->
            <div>
              <label class="font-semibold text-gray-700">Judul Utama</label>
              <input
                v-model="item.judul"
                class="w-full border rounded-lg px-3 py-2 mt-2"
                placeholder="Contoh: Pertanian Boyolali"
              />
            </div>

            <!-- Deskripsi -->
            <div>
              <label class="font-semibold text-gray-700">Deskripsi</label>
              <textarea
                v-model="item.deskripsi"
                class="w-full border rounded-lg px-3 py-2 mt-2"
                placeholder="Tuliskan deskripsi..."
              ></textarea>
            </div>

            <!-- UPLOAD GAMBAR -->
            <div>
              <label class="font-semibold text-gray-700">Upload Gambar</label>

              <label
                class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl h-64 mt-2 cursor-pointer hover:bg-gray-50 overflow-hidden"
              >
                <input
                  type="file"
                  class="hidden"
                  @change="(e) => handleImage(e, index)"
                  accept="image/png,image/jpeg"
                />

                <!-- kosong -->
                <div v-if="!item.imagePreview" class="text-center">
                  <svg
                    class="w-16 h-16 text-gray-400 mx-auto mb-3"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                  >
                    <path d="M12 16V4m0 0l-4 4m4-4l4 4" />
                    <path d="M4 20h16" />
                  </svg>

                  <p class="font-semibold text-gray-600">
                    Klik untuk upload gambar
                  </p>
                  <p class="text-sm text-gray-400">
                    PNG, JPG, atau JPEG (Maks. 5MB)
                  </p>
                </div>

                <!-- preview -->
                <img
                  v-if="item.imagePreview"
                  :src="item.imagePreview"
                  class="w-full h-full object-cover rounded-xl"
                />
              </label>
            </div>

          </div>

          <!-- BUTTON -->
          <div class="flex justify-between mt-6">
            <button
              type="button"
              @click="resetForm"
              class="bg-gray-200 px-6 py-3 rounded-lg"
            >
              Reset
            </button>

            <button
              type="submit"
              class="bg-teal-600 text-white px-6 py-3 rounded-lg"
            >
              Simpan Perubahan
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
import { ref } from 'vue'

const page = usePage()

const kategoriList = page.props.kategori_list
const activeKategori = ref(page.props.active_kategori)

// ✅ TANPA POIN
const form = ref([
  {
    judul: '',
    deskripsi: '',
    gambar: null,
    imagePreview: null
  }
])

const addForm = () => {
  form.value.push({
    judul: '',
    deskripsi: '',
    gambar: null,
    imagePreview: null
  })
}

const removeForm = (index) => {
  form.value.splice(index, 1)
}

const handleImage = (e, index) => {
  const file = e.target.files[0]
  if (file) {
    form.value[index].gambar = file
    form.value[index].imagePreview = URL.createObjectURL(file)
  }
}

const changeKategori = (kat) => {
  router.get(route('admin.manajemen-potensi.index'),
    { kategori: kat },
    { preserveState: false }
  )
}

const submitForm = () => {
  const data = new FormData()

  data.append('kategori', activeKategori.value)

  form.value.forEach((item, i) => {
    data.append(`potensi[${i}][judul]`, item.judul)
    data.append(`potensi[${i}][deskripsi]`, item.deskripsi)

    if (item.gambar) {
      data.append(`potensi[${i}][gambar]`, item.gambar)
    }
  })

  router.post(route('admin.manajemen-potensi.store'), data)
}

const resetForm = () => {
  form.value = [
    {
      judul: '',
      deskripsi: '',
      gambar: null,
      imagePreview: null
    }
  ]
}
</script>