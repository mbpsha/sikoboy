<script setup>
import { ref } from "vue";

// DATA HARDCODE (SUDAH ADA TYPE)
const peraturan = ref([
  {
    title: "PERATURAN MENTERI DALAM NEGERI NOMOR 22 TAHUN 2020",
    file: "/docs/Permendagri Nomor 22 Tahun 2020.pdf",
    thumbnail: "/images/Permendagri.png",
    type: "pdf",
  },
  {
    title: "PERATURAN BUPATI NOMOR 98 TAHUN 2022",
    file: "/docs/Peraturan Bupati Nomor 98 Tahun 2022.pdf",
    thumbnail: "/images/PeraturanBupati.png",
    type: "pdf",
  },
  {
    title: "SOP PENYELENGGARAAN KERJA SAMA DAERAH",
    file: "/docs/SOP KERJASAMA DAERAH FIKS.xls",
    thumbnail: "/images/SOP.png",
    type: "xls",
  },
]);

const showModal = ref(false);
const selectedFile = ref(null);

// HANDLE CLICK
const handleClick = (item) => {
  if (item.type === "pdf") {
    selectedFile.value = item.file;
    showModal.value = true;
  } else if (item.type === "xls") {
    window.open(item.file, "_blank");
  }
};

const closeModal = () => {
  showModal.value = false;
  selectedFile.value = null;
};
</script>

<template>
  <section class="py-12 md:py-16 px-4 md:px-10 bg-gray-50">
    <div class="max-w-7xl mx-auto">

      <!-- TITLE -->
      <h2 class="text-xl md:text-2xl font-bold text-center text-[#17464E] mb-8 md:mb-10">
        Peraturan-Peraturan Terkait Kerja Sama
      </h2>

      <!-- GRID RESPONSIVE -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8">

        <div
          v-for="(item, index) in peraturan"
          :key="index"
          class="bg-white rounded-2xl shadow-md p-5 md:p-6 text-center 
                 hover:shadow-xl hover:-translate-y-1 transition duration-300"
        >
          <!-- TITLE -->
          <h3 class="text-[#17464E] font-semibold text-sm md:text-base mb-4 leading-snug">
            {{ item.title }}
          </h3>

          <!-- PREVIEW -->
          <div class="bg-gray-100 rounded-xl p-3 md:p-4 mb-4">
            <img
              :src="item.thumbnail"
              alt="preview"
              class="mx-auto h-32 md:h-40 object-contain"
            />
          </div>

          <!-- BUTTON -->
          <button
            @click="handleClick(item)"
            class="bg-[#17464E] text-white text-sm md:text-base px-4 py-2 rounded-full 
                   hover:bg-[#12363C] transition duration-300"
          >
            {{ item.type === "pdf" ? "Lihat Dokumen Peraturan" : "Buka Dokumen" }}
          </button>
        </div>

      </div>

      <!-- MODAL PDF -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
      >
        <div class="bg-white w-full max-w-5xl h-[80vh] rounded-xl shadow-lg relative">

          <!-- CLOSE -->
          <button
            @click="closeModal"
            class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl z-10"
          >
            ✕
          </button>

          <!-- IFRAME -->
          <iframe
            :src="selectedFile"
            class="w-full h-full rounded-xl"
          ></iframe>

        </div>
      </div>

    </div>
  </section>
</template>