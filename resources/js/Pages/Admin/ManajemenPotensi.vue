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
              Isi judul dan deskripsi umum kategori potensi
            </p>
          </div>

          <button
            @click="addPoin"
            class="bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold"
          >
            + Tambah Poin
          </button>
        </div>

        <!-- FORM -->
        <form @submit.prevent="submitForm" class="space-y-5">

          <!-- Judul -->
          <div>
            <label class="font-semibold text-gray-700">Judul Utama *</label>
            <input
              v-model="form.judul"
              type="text"
              class="w-full border rounded-lg px-4 py-2 mt-2"
              placeholder="Contoh : Pertanian terpadu Boyolali"
            />
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="font-semibold text-gray-700">Deskripsi Singkat *</label>
            <textarea
              v-model="form.deskripsi"
              class="w-full border rounded-lg px-4 py-2 mt-2 h-32"
              placeholder="Tuliskan deskripsi..."
            ></textarea>
          </div>

          <!-- UPLOAD GAMBAR -->
          <div>
            <label class="font-semibold text-gray-700">Upload Gambar</label>

            <label
              class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl h-64 mt-2 cursor-pointer hover:bg-gray-50"
            >
              <input
                type="file"
                class="hidden"
                @change="handleImage"
                accept="image/png,image/jpeg"
              />

              <div class="text-center">
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
                  PNG, JPG, JPEG (Maks. 5MB)
                </p>
              </div>
            </label>

            <p v-if="imageName" class="text-sm text-teal-600 mt-2">
              {{ imageName }}
            </p>
          </div>

          <!-- POIN -->
          <div>
            <label class="font-semibold text-gray-700">Poin</label>

            <div class="space-y-2 mt-2">
              <div
                v-for="(p, i) in form.poin"
                :key="i"
                class="flex gap-2"
              >
                <input
                  v-model="form.poin[i]"
                  class="w-full border rounded-lg px-3 py-2"
                  placeholder="Isi poin..."
                />

                <button
                  @click.prevent="removePoin(i)"
                  class="text-red-500"
                >
                  ✕
                </button>
              </div>
            </div>
          </div>

        </form>

      </div>

      <!-- BUTTON -->
      <div class="flex justify-between mt-6">
        <button
          @click="resetForm"
          class="bg-gray-200 px-6 py-3 rounded-lg"
        >
          Reset
        </button>

        <button
          @click="submitForm"
          class="bg-teal-600 text-white px-6 py-3 rounded-lg"
        >
          Simpan Perubahan
        </button>
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

const form = ref({
  kategori: activeKategori.value,
  judul: page.props.potensi?.judul || '',
  deskripsi: page.props.potensi?.deskripsi || '',
  poin: page.props.potensi?.poin?.map(p => p.isi) || [],
  gambar: null
})

const imageName = ref(null)

const handleImage = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.gambar = file
    imageName.value = file.name
  }
}

const addPoin = () => {
  form.value.poin.push('')
}

const removePoin = (i) => {
  form.value.poin.splice(i, 1)
}

const changeKategori = (kat) => {
  router.get(route('admin.manajemen-potensi.index'), { kategori: kat })
}

const submitForm = () => {
  const data = new FormData()
  data.append('kategori', form.value.kategori)
  data.append('judul', form.value.judul)
  data.append('deskripsi', form.value.deskripsi)

  form.value.poin.forEach((p, i) => {
    data.append(`poin[${i}]`, p)
  })

  if (form.value.gambar) {
    data.append('gambar', form.value.gambar)
  }

  router.post(route('admin.manajemen-potensi.store'), data)
}

const resetForm = () => {
  location.reload()
}
</script>