<script setup>
import { ref, onMounted, computed } from 'vue'
import images from '@/images'

// Pertanian images (top=1, main=2, bottom=3)
const topImg    = images.find(i => /landing_pertanian1/i.test(i.alt))?.src || images[0]?.src
const mainImg   = images.find(i => /landing_pertanian2/i.test(i.alt))?.src || images[1]?.src
const bottomImg = images.find(i => /landing_pertanian3/i.test(i.alt))?.src || images[2]?.src

// Peternakan images
const peternakanTopImg    = images.find(i => /landing_peternakan1/i.test(i.alt))?.src || images[3]?.src
const peternakanMainImg   = images.find(i => /landing_peternakan2/i.test(i.alt))?.src || images[4]?.src
const peternakanBottomImg = images.find(i => /landing_peternakan3/i.test(i.alt))?.src || images[5]?.src

// Wisata Alam images
const wisataTopImg    = images.find(i => /landing_wisal2/i.test(i.alt))?.src || images[6]?.src
const wisataMainImg   = images.find(i => /landing_wisal1/i.test(i.alt))?.src || images[7]?.src
const wisataBottomImg = images.find(i => /landing_wisal4/i.test(i.alt))?.src || images[8]?.src

// UMKM images
const umkmTopImg    = images.find(i => /landing_umkm1/i.test(i.alt))?.src || images[9]?.src
const umkmMainImg   = images.find(i => /landing_umkm2/i.test(i.alt))?.src || images[10]?.src
const umkmBottomImg = images.find(i => /landing_umkm3/i.test(i.alt))?.src || images[11]?.src

// Budaya images
const budayaTopImg    = images.find(i => /landing_budaya2/i.test(i.alt))?.src || images[12]?.src
const budayaMainImg   = images.find(i => /landing_budaya1/i.test(i.alt))?.src || images[13]?.src
const budayaBottomImg = images.find(i => /landing_budaya3/i.test(i.alt))?.src || images[14]?.src

// Active tab
const activeTab = ref('pertanian')
const show = ref(false)
const tabVisible = ref(true)

const tabs = [
  { key: 'pertanian',  label: 'Pertanian'   },
  { key: 'peternakan', label: 'Peternakan'  },
  { key: 'wisata',     label: 'Wisata Alam' },
  { key: 'umkm',       label: 'UMKM'        },
  { key: 'budaya',     label: 'Budaya'      },
]

function switchTab(key) {
  if (key === activeTab.value) return
  tabVisible.value = false
  setTimeout(() => {
    activeTab.value = key
    tabVisible.value = true
  }, 250)
}

onMounted(() => {
  setTimeout(() => { show.value = true }, 200)
})

const tabContent = {
  pertanian: {
    images: { top: topImg, main: mainImg, bottom: bottomImg },
    left: [
      {
        title: 'Lahan Subur dan Produktif',
        desc: 'Boyolali memiliki lahan pertanian yang luas dan subur, cocok untuk pengembangan komoditas unggulan seperti padi, sayuran, dan hortikultura.',
      },
      {
        title: 'Dukungan Pemerintah Daerah',
        desc: 'Pemerintah aktif mendorong modernisasi pertanian dan kemitraan dengan sektor swasta.',
      },
    ],
    right: [
      {
        title: 'Ketersediaan Bahan Baku Stabil',
        desc: 'Produksi hasil pertanian yang konsisten membuka peluang kerja sama di bidang distribusi, pengolahan, hingga ekspor.',
      },
      {
        title: 'Potensi Pengembangan Agroteknologi',
        desc: 'Terbuka peluang kolaborasi dalam smart farming, sistem irigasi modern, dan pengolahan hasil panen.',
      },
    ],
  },
  peternakan: {
    images: { top: peternakanTopImg, main: peternakanMainImg, bottom: peternakanBottomImg },
    left: [
      {
        title: 'Lumbung Susu',
        desc: 'Kabupaten Boyolali dikenal luas sebagai salah satu penghasil susu sapi murni terbesar di Pulau Jawa, dengan populasi sapi perah yang sangat tinggi.',
      },
      {
        title: 'Moderenisasi Peternakan',
        desc: 'Terdapat banyak model peternakan modern yang menerapkan sistem manajemen terintegrasi, mulai dari pemberian pakan otomatis, pemandian sapi, hingga alat pemerahan susu yang higienis.',
      },
    ],
    right: [
      {
        title: 'Peluang Industri Pengolahan Susu',
        desc: 'Potensi besar dalam pengembangan industri hilir susu, mulai dari pasteurisasi, keju, yogurt, hingga produk turunan bernilai tinggi.',
      },
      {
        title: 'Peluang Kolaborasi & Hilirisasi',
        desc: ' Boyolali membuka peluang kolaborasi yang luas bagi investor dan mitra untuk pengembangan teknologi serta manajemen di bidang peternakan.',
      },
    ],
  },
  wisata: {
    images: { top: wisataTopImg, main: wisataMainImg, bottom: wisataBottomImg },
    left: [
      {
        title: 'Embung Manajar',
        desc: 'Embung Manajar adalah tempat penampungan air yang dibangun pada tahun 2019. Embung ini berada di ketinggian 1700 mdpl tepatnya di lereng selatan Gunung Merbabu.',
      },
      {
        title: 'Keindahan Gunung & Pendakian',
        desc: 'Boyolali merupakan gerbang utama menuju jalur pendakian Gunung Merapi dan Merbabu.',
      },
    ],
    right: [
      {
        title: 'Peluang Investasi Pariwisata',
        desc: 'Terbuka kesempatan kolaborasi dalam pengelolaan resort, glamping, wisata edukasi pertanian, dan paket wisata terpadu.',
      },
      {
        title: 'Kekayaan Alam Waduk Cengklik',
        desc: 'Waduk Cengklik dan sejumlah embung menjadi potensi wisata air yang dapat dikembangkan menjadi destinasi rekreasi keluarga.',
      },
    ],
  },
  umkm: {
    images: { top: umkmTopImg, main: umkmMainImg, bottom: umkmBottomImg },
    left: [
      {
        title: 'Olahan Susu Sapi',
        desc: 'Boyolali dikenal sebagai sentra penghasil susu sapi, sehingga banyak berkembang UMKM olahan seperti yogurt, susu pasteurisasi, keju, dan permen susu.',
      },
      {
        title: 'Industri Tahu Lokal',
        desc: ' Industri tahu yang berkembang di berbagai desa menjadi salah satu penggerak ekonomi masyarakat.',
      },
    ],
    right: [
      {
        title: 'Abon dan Olahan Daging Sapi',
        desc: 'Melimpahnya hasil peternakan sapi mendorong UMKM olahan seperti abon, dendeng, dan produk daging lainnya',
      },
      {
        title: 'Akses Pasar yang Luas',
        desc: 'Posisi strategis Boyolali di jalur Solo–Semarang membuka akses pasar regional yang luas untuk produk-produk UMKM lokal.',
      },
    ],
  },
  budaya: {
    images: { top: budayaTopImg, main: budayaMainImg, bottom: budayaBottomImg },
    left: [
      {
        title: 'Festival Budaya Tahunan',
        desc: 'Berbagai festival budaya rutin digelar setiap tahun, menjadi ajang promosi daerah sekaligus daya tarik wisatawan budaya.',
      },
      {
        title: 'Kirab Gunungan Hasil Bumi',
        desc: 'Tradisi kirab dengan gunungan sayur dan hasil bumi menjadi simbol rasa syukur masyarakat.',
      },
    ],
    right: [
      {
        title: 'Kolaborasi Pelestarian Budaya',
        desc: 'Terbuka peluang kemitraan dalam dokumentasi, digitalisasi, dan promosi warisan budaya lokal ke tingkat nasional maupun internasional.',
      },
      {
        title: 'Kesenian Reog Boyolali',
        desc: 'Pertunjukan reog yang masih aktif ditampilkan dalam berbagai acara daerah menjadi daya tarik budaya yang kuat.',
      },
    ],
  },
}

const current = computed(() => tabContent[activeTab.value])
</script>

<template>
<section class="bg-white py-32 px-6 overflow-hidden">
  <div class="max-w-7xl mx-auto">

    <!-- TITLE -->
    <div
      class="text-center transition-all duration-700"
      :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
    >
      <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-[#0C505C]">
        Potensi Unggulan Kabupaten Boyolali
      </h2>
      <p class="mt-4 text-md text-slate-600 max-w-2xl mx-auto">
        Mendukung Peluang Kolaborasi dan Pengembangan daerah
      </p>
    </div>

    <!-- NAV TABS -->
    <div
      class="mt-8 flex justify-center transition-all duration-700 delay-100"
      :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
    >
      <div class="flex gap-8 text-sm">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="switchTab(tab.key)"
          class="pb-1 transition-all duration-300 font-medium"
          :class="activeTab === tab.key
            ? 'text-[#0D99FF] font-semibold border-b-2 border-[#0D99FF]'
            : 'text-slate-400 hover:text-[#0C505C]'"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- CONTENT -->
    <div
      class="mt-20 grid grid-cols-1 md:grid-cols-3 items-center gap-8 transition-all duration-300"
      :class="tabVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    >

      <!-- LEFT TEXT -->
      <div
        class="space-y-16 pl-4 transition-all duration-700 delay-200"
        :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-10'"
      >
        <div
          v-for="(item, i) in current.left"
          :key="i"
          class="max-w-[280px] hover:-translate-y-1 transition"
        >
          <h3 class="text-[#0C505C] font-bold text-md text-center">
            {{ item.title }}
          </h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed text-center">
            {{ item.desc }}
          </p>
        </div>
      </div>

      <!-- IMAGE STACK -->
      <div
        class="relative flex justify-center transition-all duration-700 delay-300"
        :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'"
      >
        <div class="relative w-[340px] h-[520px]">

          <!-- TOP -->
          <div class="absolute top-0 left-1/2 -translate-x-1/2 z-20 animate-float">
            <div class="w-52 aspect-[3/4] rounded-3xl border-4 border-white overflow-hidden shadow-[0_20px_50px_rgba(12,80,80,0.15)] hover:scale-105 transition">
              <img :src="current.images.top" class="w-full h-full object-cover" />
            </div>
          </div>

          <!-- MIDDLE -->
          <div class="absolute top-[120px] left-0 z-30 -rotate-3 animate-float-delay">
            <div class="w-52 aspect-[3/4] rounded-3xl border-4 border-white overflow-hidden shadow-[0_25px_60px_rgba(12,80,80,0.18)] hover:scale-105 transition">
              <img :src="current.images.main" class="w-full h-full object-cover" />
            </div>
          </div>

          <!-- BOTTOM -->
          <div class="absolute top-[240px] right-0 z-10 rotate-2 animate-float-slow">
            <div class="w-52 aspect-[3/4] rounded-3xl border-4 border-white overflow-hidden shadow-[0_24px_56px_rgba(12,80,80,0.12)] hover:scale-105 transition">
              <img :src="current.images.bottom" class="w-full h-full object-cover" />
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT TEXT -->
      <div
        class="space-y-16 pl-4 transition-all duration-700 delay-200"
        :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'"
      >
        <div
          v-for="(item, i) in current.right"
          :key="i"
          class="max-w-[280px] hover:-translate-y-1 transition"
        >
          <h3 class="text-[#0C505C] font-bold text-md text-center">
            {{ item.title }}
          </h3>
          <p class="text-sm text-slate-500 mt-2 leading-relaxed text-center">
            {{ item.desc }}
          </p>
        </div>
      </div>

    </div>

  </div>
</section>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50%       { transform: translateY(-12px); }
}

.animate-float       { animation: float 6s ease-in-out infinite; }
.animate-float-delay { animation: float 7s ease-in-out infinite; }
.animate-float-slow  { animation: float 8s ease-in-out infinite; }
</style>