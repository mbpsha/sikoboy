<script setup>
import { ref, onMounted, onBeforeUnmount, computed, defineProps, watch } from 'vue'
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
const carouselIndex = ref(0)
const show = ref(false)
const autoplayDelay = 5000
let autoplayTimer = null

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

const normalizeIndex = (index, length) => {
  if (!length) return 0
  const wrapped = index % length
  return wrapped < 0 ? wrapped + length : wrapped
}

// Set initial tab
onMounted(() => {
  if (tabs.value.length > 0) {
    activeTab.value = tabs.value[0].key
    show.value = true
  }

  autoplayTimer = window.setInterval(() => {
    if (!carouselItems.value.length) return
    carouselIndex.value = normalizeIndex(carouselIndex.value + 1, carouselItems.value.length)
  }, autoplayDelay)
})

watch(tabs, (newTabs) => {
  if (!newTabs.length) {
    activeTab.value = ''
    carouselIndex.value = 0
    return
  }

  if (!activeTab.value || !newTabs.some(tab => tab.key === activeTab.value)) {
    activeTab.value = newTabs[0].key
    carouselIndex.value = 0
  }
}, { immediate: true })

function switchTab(key) {
  if (key === activeTab.value) return
  activeTab.value = key
  carouselIndex.value = 0
}

const currentTabItems = computed(() => {
  if (!activeTab.value || !potensiData.value[activeTab.value]) {
    return []
  }

  return potensiData.value[activeTab.value] || []
})

const carouselItems = computed(() => {
  return currentTabItems.value.map((item, idx) => ({
    ...buildDescriptionMeta(item.deskripsi),
    key: `${activeTab.value}-${idx}`,
    title: item.judul,
    desc: item.deskripsi,
    image: item.gambar_url,
  }))
})

// ✅ Tampilkan hanya item yang ada (1, 2, atau 3), dan pastikan item yang tampil tetap di tengah
const visibleCarouselItems = computed(() => {
  const items = carouselItems.value
  const total = items.length

  if (!total) return []

  const center = normalizeIndex(carouselIndex.value, total)

  let positions = []
  if (total === 1) {
    positions = [0]
  } else if (total === 2) {
    // show previous and current (keeps current centered visually)
    positions = [-1, 0]
  } else {
    // normal case: prev, current, next
    positions = [-1, 0, 1]
  }

  // Map positions to unique items
  const seen = new Set()
  return positions.map(offset => {
    const index = normalizeIndex(center + offset, total)
    if (seen.has(index)) return null
    seen.add(index)
    const item = items[index]

    return {
      ...item,
      offset,
      isActive: offset === 0,
      isSide: Math.abs(offset) === 1,
    }
  }).filter(Boolean)
})

// ✅ PERBAIKAN: Style yang lebih clean dan readable
const getCarouselCardStyle = (item) => {
  if (item.isActive) {
    // Kartu aktif di tengah
    return {
      transform: 'translateX(-50%) scale(1)',
      opacity: 1,
      zIndex: 30,
      filter: 'none',
    }
  } else {
    // Kartu samping (prev/next)
    const xOffset = item.offset > 0 ? 280 : -280 // Jarak horizontal
    return {
      transform: `translateX(calc(-50% + ${xOffset}px)) scale(0.85)`,
      opacity: 0.4,
      zIndex: 10,
      filter: 'blur(1px)',
    }
  }
}

const hasCarouselItems = computed(() => carouselItems.value.length > 0)
const detailModalItem = ref(null)
const MAX_PREVIEW_WORDS = 12

function splitWords(text = '') {
  return text
    .trim()
    .split(/\s+/)
    .filter(Boolean)
}

function buildDescriptionMeta(text = '') {
  const words = splitWords(text)
  if (words.length <= MAX_PREVIEW_WORDS) {
    return {
      descPreview: text,
      descIsLong: false,
    }
  }

  return {
    descPreview: `${words.slice(0, MAX_PREVIEW_WORDS).join(' ')}...`,
    descIsLong: true,
  }
}

const openDetailModal = (item) => {
  detailModalItem.value = item
}

const closeDetailModal = () => {
  detailModalItem.value = null
}

function prevSlide() {
  if (!carouselItems.value.length) return
  carouselIndex.value = normalizeIndex(carouselIndex.value - 1, carouselItems.value.length)
}

function nextSlide() {
  if (!carouselItems.value.length) return
  carouselIndex.value = normalizeIndex(carouselIndex.value + 1, carouselItems.value.length)
}

onBeforeUnmount(() => {
  if (autoplayTimer) window.clearInterval(autoplayTimer)
})
</script>

<template>
<section class="bg-gradient-to-b from-slate-50 to-white pt-24 pb-20 px-6 overflow-hidden">
  <div class="max-w-7xl mx-auto">

    <!-- ✅ TITLE - Lebih Modern -->
    <div class="text-center mb-12 px-4">
      <h2 class="text-2xl md:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-blue-600 leading-tight pb-1">
        Potensi Unggulan Kabupaten Boyolali
      </h2>
      <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
        Mendukung Peluang Kolaborasi dan Pengembangan Daerah
      </p>
    </div>

    <!-- ✅ TABS - Lebih Visible -->
    <div class="mt-8 flex justify-center">
      <div class="inline-flex gap-2 p-2 bg-white rounded-2xl shadow-lg border border-gray-200">
        <button 
          v-for="tab in tabs" 
          :key="tab.key"
          @click="switchTab(tab.key)"
          :class="[
            'px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300',
            activeTab === tab.key
              ? 'bg-gradient-to-r from-teal-500 to-blue-500 text-white shadow-md'
              : 'text-gray-600 hover:bg-gray-100'
          ]">
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- ✅ CAROUSEL - Lebih Clean -->
    <div class="mt-16 relative" :class="show ? 'opacity-100' : 'opacity-0'">
      
      <!-- Navigation Buttons -->
      <button
        type="button"
        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 z-40 h-14 w-14 rounded-full bg-white text-slate-800 shadow-2xl ring-1 ring-slate-200 hover:bg-teal-50 hover:scale-110 hover:ring-teal-400 transition-all duration-300 flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed"
        @click="prevSlide"
        :disabled="!hasCarouselItems"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <button
        type="button"
        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 z-40 h-14 w-14 rounded-full bg-white text-slate-800 shadow-2xl ring-1 ring-slate-200 hover:bg-teal-50 hover:scale-110 hover:ring-teal-400 transition-all duration-300 flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed"
        @click="nextSlide"
        :disabled="!hasCarouselItems"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      <!-- Carousel Container -->
      <div class="overflow-hidden px-4 md:px-8 py-8">
        <div class="relative h-[380px] md:h-[420px]">
          
          <article
            v-for="item in visibleCarouselItems"
            :key="item.key"
            class="absolute left-1/2 top-0 w-[260px] md:w-[340px] transition-all duration-700 ease-out will-change-transform"
            :style="getCarouselCardStyle(item)"
          >
            <!-- ✅ CARD - Lebih Readable -->
            <div
              class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500"
              role="button"
              tabindex="0"
              @click="openDetailModal(item)"
              @keydown.enter.prevent="openDetailModal(item)"
              @keydown.space.prevent="openDetailModal(item)"
            >
              
              <!-- Image -->
              <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                <img
                  v-if="item.image"
                  :src="item.image"
                  :alt="item.title"
                  class="h-full w-full object-cover transition-transform duration-700 hover:scale-110"
                />
                <div
                  v-else
                  class="flex h-full w-full items-center justify-center bg-gradient-to-br from-teal-100 to-blue-100"
                >
                  <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>

                <!-- Badge Kategori -->
                <div class="absolute top-4 left-4">
                  <span class="px-4 py-2 bg-white/95 backdrop-blur-sm rounded-full text-xs font-bold text-teal-600 shadow-lg">
                    {{ activeTab.charAt(0).toUpperCase() + activeTab.slice(1) }}
                  </span>
                </div>
              </div>

              <!-- ✅ Content - Lebih Jelas -->
              <div class="p-6 md:p-8">
                <h3 class="text-xl md:text-2xl font-bold text-slate-800 leading-tight mb-3">
                  {{ item.title }}
                </h3>
                <p class="text-sm md:text-base text-slate-600 leading-relaxed line-clamp-3">
                  <span>{{ item.descPreview }}</span>
                  <button
                    v-if="item.descIsLong"
                    type="button"
                    class="ml-1 font-semibold text-teal-600 hover:text-teal-700"
                    @click.stop="openDetailModal(item)"
                  >
                    selengkapnya
                  </button>
                </p>
              </div>

            </div>
          </article>

        </div>
      </div>

      <!-- ✅ INDICATOR DOTS -->
      <div class="flex justify-center gap-2 mt-6">
        <button
          v-for="(item, idx) in carouselItems"
          :key="idx"
          @click="carouselIndex = idx"
          :class="[
            'h-2 rounded-full transition-all duration-300',
            idx === carouselIndex
              ? 'w-8 bg-gradient-to-r from-teal-500 to-blue-500'
              : 'w-2 bg-gray-300 hover:bg-gray-400'
          ]"
        ></button>
      </div>

    </div>

    <div
      v-if="detailModalItem"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="closeDetailModal"
    >
      <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b px-5 py-4">
          <h3 class="text-lg font-bold text-slate-800">Detail Potensi</h3>
          <button
            type="button"
            class="rounded-md p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
            @click="closeDetailModal"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-5 md:p-6">
          <div class="overflow-hidden rounded-xl bg-slate-100">
            <img
              v-if="detailModalItem.image"
              :src="detailModalItem.image"
              :alt="detailModalItem.title"
              class="h-56 w-full object-cover md:h-72"
            />
            <div
              v-else
              class="flex h-56 w-full items-center justify-center bg-gradient-to-br from-teal-100 to-blue-100 md:h-72"
            >
              <svg class="h-20 w-20 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>

          <h4 class="mt-5 text-xl font-bold text-slate-800 md:text-2xl">{{ detailModalItem.title }}</h4>
          <p class="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-slate-600 md:text-base">
            {{ detailModalItem.desc }}
          </p>
        </div>
      </div>
    </div>

  </div>
</section>
</template>

<style scoped>
/* Smooth transitions */
article {
  transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Line clamp untuk deskripsi */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Fade in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

section {
  animation: fadeIn 0.8s ease-out;
}
</style>