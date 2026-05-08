<script setup>
import { ref, onMounted, computed } from 'vue'
import images from '@/images'

// Pertanian images (4 images)
const pertanian1 = images.find(i => /landing_pertanian1/i.test(i.alt))?.src || images[0]?.src
const pertanian2 = images.find(i => /landing_pertanian2/i.test(i.alt))?.src || images[1]?.src
const pertanian3 = images.find(i => /landing_pertanian3/i.test(i.alt))?.src || images[2]?.src
const pertanian4 = images.find(i => /landing_pertanian4/i.test(i.alt))?.src || images[3]?.src

// Peternakan images (4 images)
const peternakan1 = images.find(i => /landing_peternakan1/i.test(i.alt))?.src || images[4]?.src
const peternakan2 = images.find(i => /landing_peternakan2/i.test(i.alt))?.src || images[5]?.src
const peternakan3 = images.find(i => /landing_peternakan3/i.test(i.alt))?.src || images[6]?.src
const peternakan4 = images.find(i => /landing_peternakan4/i.test(i.alt))?.src || images[7]?.src

// Wisata Alam images (4 images)
const wisata1 = images.find(i => /landing_wisal1/i.test(i.alt))?.src || images[8]?.src
const wisata2 = images.find(i => /landing_wisal2/i.test(i.alt))?.src || images[9]?.src
const wisata3 = images.find(i => /landing_wisal3/i.test(i.alt))?.src || images[10]?.src
const wisata4 = images.find(i => /landing_wisal4/i.test(i.alt))?.src || images[11]?.src

// UMKM images (4 images)
const umkm1 = images.find(i => /landing_umkm1/i.test(i.alt))?.src || images[12]?.src
const umkm2 = images.find(i => /landing_umkm2/i.test(i.alt))?.src || images[13]?.src
const umkm3 = images.find(i => /landing_umkm3/i.test(i.alt))?.src || images[14]?.src
const umkm4 = images.find(i => /landing_umkm4/i.test(i.alt))?.src || images[15]?.src

// Budaya images (4 images)
const budaya1 = images.find(i => /landing_budaya1/i.test(i.alt))?.src || images[16]?.src
const budaya2 = images.find(i => /landing_budaya2/i.test(i.alt))?.src || images[17]?.src
const budaya3 = images.find(i => /landing_budaya3/i.test(i.alt))?.src || images[18]?.src
const budaya4 = images.find(i => /landing_budaya4/i.test(i.alt))?.src || images[19]?.src

// State
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

// FULL CONTENT (TIDAK DIUBAH)
const tabContent = {
  pertanian: {
    images: { img1: pertanian1, img2: pertanian2, img3: pertanian3, img4: pertanian4 },
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
    images: { img1: peternakan1, img2: peternakan2, img3: peternakan3, img4: peternakan4 },
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
    images: { img1: wisata1, img2: wisata2, img3: wisata3, img4: wisata4 },
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
    images: { img1: umkm1, img2: umkm2, img3: umkm3, img4: umkm4 },
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
    images: { img1: budaya1, img2: budaya2, img3: budaya3, img4: budaya4 },
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
<section class="bg-white pt-44 pb-32 px-6 overflow-hidden">
  <div class="max-w-7xl mx-auto">

    <!-- TITLE -->
    <div class="text-center">
      <h2 class="text-2xl md:text-3xl font-extrabold text-[#0C505C]">
        Potensi Unggulan Kabupaten Boyolali
      </h2>
      <p class="mt-4 text-md text-slate-600">
        Mendukung Peluang Kolaborasi dan Pengembangan daerah
      </p>
    </div>

    <!-- TABS -->
    <div class="mt-8 flex justify-center">
      <div class="flex gap-8 text-sm">
        <button v-for="tab in tabs" :key="tab.key"
          @click="switchTab(tab.key)"
          :class="activeTab === tab.key
            ? 'text-blue-500 border-b-2 border-blue-500'
            : 'text-gray-400'">
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="mt-20 grid md:grid-cols-3 gap-8 items-center">

      <!-- LEFT TEXT -->
      <div class="space-y-16">
        <div v-for="(item, idx) in current.left" :key="idx" 
             class="opacity-0 animate-fadeInLeft" 
             :style="{ animationDelay: `${idx * 0.15}s`, animationFillMode: 'forwards' }">
          <h3 class="text-center font-bold text-[#0C505C]">{{ item.title }}</h3>
          <p class="text-center text-sm text-gray-500 mt-2">{{ item.desc }}</p>
        </div>
      </div>

      <!-- IMAGES - ALL 3:4 ASPECT RATIO, PLACED CLOSE TO EACH OTHER -->
      <div class="relative flex justify-center items-center min-h-[500px]">
        
        <!-- Image 1: Bottom Left -->
        <div class="absolute left-13 bottom-28 z-10 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-lg bg-gray-100">
            <img :src="current.images.img1" 
                 class="w-full h-full object-cover"
                 alt="Image 1"/>
          </div>
        </div>

        <!-- Image 2: Top Left -->
        <div class="absolute left-18 top-3 z-20 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-lg bg-gray-100">
            <img :src="current.images.img2" 
                 class="w-full h-full object-cover"
                 alt="Image 2"/>
          </div>
        </div>

        <!-- Image 3: Center Right (main image) -->
        <div class="absolute right-12 top-1/3 -translate-y-1/2 z-30 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-xl bg-gray-100">
            <img :src="current.images.img3" 
                 class="w-full h-full object-cover"
                 alt="Image 3"/>
          </div>
        </div>

        <!-- Image 4: Bottom Right (overlapping img3) -->
        <div class="absolute right-18 bottom-15 z-40 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-lg bg-gray-100">
            <img :src="current.images.img4" 
                 class="w-full h-full object-cover"
                 alt="Image 4"/>
          </div>
        </div>

      </div>

      <!-- RIGHT TEXT -->
      <div class="space-y-16">
        <div v-for="(item, idx) in current.right" :key="idx"
             class="opacity-0 animate-fadeInRight"
             :style="{ animationDelay: `${idx * 0.15}s`, animationFillMode: 'forwards' }">
          <h3 class="text-center font-bold text-[#0C505C]">{{ item.title }}</h3>
          <p class="text-center text-sm text-gray-500 mt-2">{{ item.desc }}</p>
        </div>
      </div>

    </div>

  </div>
</section>
</template>

<style scoped>
@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-40px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(40px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fadeInLeft {
  animation: fadeInLeft 0.6s ease-out forwards;
}

.animate-fadeInRight {
  animation: fadeInRight 0.6s ease-out forwards;
}

/* Smooth transitions */
.relative > div {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>