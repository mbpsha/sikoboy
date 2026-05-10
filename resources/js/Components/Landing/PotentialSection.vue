<script setup>
import { ref, onMounted, computed, defineProps } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
  potensiData: {
    type: Object,
    default: () => ({})
  }
})

// Get potensi data from props or Inertia page
const potensiData = computed(() => {
  return props.potensiData || page.props.value?.potensiData || {}
})

// State
const activeTab = ref('')
const show = ref(false)
const tabVisible = ref(true)

// Dynamic tabs from database
const tabs = computed(() => {
  if (!potensiData.value || Object.keys(potensiData.value).length === 0) {
    return []
  }
  return Object.keys(potensiData.value).map(kategori => ({
    key: kategori,
    label: kategori.charAt(0).toUpperCase() + kategori.slice(1)
  }))
})

// Set initial tab
onMounted(() => {
  if (tabs.value.length > 0) {
    activeTab.value = tabs.value[0].key
    setTimeout(() => { show.value = true }, 200)
  }
})

function switchTab(key) {
  if (key === activeTab.value) return
  tabVisible.value = false
  setTimeout(() => {
    activeTab.value = key
    tabVisible.value = true
  }, 250)
}

// Get current tab content
const current = computed(() => {
  if (!activeTab.value || !potensiData.value[activeTab.value]) {
    return { left: [], right: [], images: {} }
  }

  const items = potensiData.value[activeTab.value] || []
  const images = {}
  
  // Collect images from items (up to 4 per tab)
  items.slice(0, 4).forEach((item, idx) => {
    images[`img${idx + 1}`] = item.gambar_url
  })

  // Build display list: main items + their poin
  const displayList = []
  items.forEach(item => {
    // Add main potensi item
    displayList.push({
      title: item.judul,
      desc: item.deskripsi
    })
    
    // Add poin items under it
    item.poin?.forEach(p => {
      const parts = p.isi.split(':')
      displayList.push({
        title: parts[0]?.trim() || p.isi,
        desc: parts[1]?.trim() || ''
      })
    })
  })

  // Distribute list: first half on left, second half on right
  const midpoint = Math.ceil(displayList.length / 2)
  
  return {
    left: displayList.slice(0, midpoint),
    right: displayList.slice(midpoint),
    images
  }
})
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
        <div v-if="current.images.img1" class="absolute left-13 bottom-28 z-10 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-lg bg-gray-100">
            <img :src="current.images.img1" 
                 class="w-full h-full object-cover"
                 alt="Image 1"/>
          </div>
        </div>

        <!-- Image 2: Top Left -->
        <div v-if="current.images.img2" class="absolute left-18 top-3 z-20 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-lg bg-gray-100">
            <img :src="current.images.img2" 
                 class="w-full h-full object-cover"
                 alt="Image 2"/>
          </div>
        </div>

        <!-- Image 3: Center Right (main image) -->
        <div v-if="current.images.img3" class="absolute right-12 top-1/3 -translate-y-1/2 z-30 transition-all duration-500 hover:scale-105 hover:z-50">
          <div class="w-36 h-48 rounded-xl overflow-hidden shadow-xl bg-gray-100">
            <img :src="current.images.img3" 
                 class="w-full h-full object-cover"
                 alt="Image 3"/>
          </div>
        </div>

        <!-- Image 4: Bottom Right (overlapping img3) -->
        <div v-if="current.images.img4" class="absolute right-18 bottom-15 z-40 transition-all duration-500 hover:scale-105 hover:z-50">
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