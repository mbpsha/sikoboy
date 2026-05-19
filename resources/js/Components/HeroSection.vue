<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  backgroundImage: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  titleHighlight: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  showScrollIndicator: {
    type: Boolean,
    default: true,
  },
  overlayGradient: {
    type: String,
    default: 'linear-gradient(to bottom, rgba(10,50,60,0.55) 0%, rgba(10,50,60,0.75) 50%, rgba(255,255,255,1) 100%)',
  },
})

const offset = ref(0)
const screenWidth = ref(0)
const visible = ref(false)
const headerOffset = ref(0)

const handleScroll = () => { offset.value = window.scrollY * 0.3 }
const updateWidth = () => { screenWidth.value = window.innerWidth }

const updateHeaderOffset = () => {
  try {
    const hdr = document.querySelector('header')
    headerOffset.value = hdr ? hdr.offsetHeight : 0
  } catch (e) {
    headerOffset.value = 0
  }
}

const _resizeHandler = () => { updateWidth(); updateHeaderOffset() }

onMounted(() => {
  updateWidth()
  updateHeaderOffset()
  window.addEventListener('resize', _resizeHandler)
  window.addEventListener('scroll', handleScroll)
  setTimeout(() => { visible.value = true }, 100)
})

onUnmounted(() => {
  window.removeEventListener('resize', _resizeHandler)
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <section
    class="relative overflow-hidden text-white min-h-[90vh] flex items-center justify-center"
    :style="{ paddingTop: headerOffset + 'px' }"
  >

    <!-- Background parallax -->
    <div class="absolute inset-0 pointer-events-none">
      <div
        class="w-full h-[130%] bg-cover bg-[center_30%]"
        :style="{
          backgroundImage: `url(${backgroundImage})`,
          transform: screenWidth >= 768 ? `translateY(${offset}px)` : 'none',
        }"
      ></div>

      <!-- Overlay gradient -->
      <div 
        class="absolute inset-0" 
        :style="`background: ${overlayGradient};`"
      ></div>
    </div>

    <!-- Content -->
    <div
      class="relative z-10 mx-auto max-w-4xl px-6 text-center transition-all duration-1000"
      :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
    >

      <!-- Title -->
      <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight leading-none mb-4 drop-shadow-lg">
        {{ title }}
        <span v-if="titleHighlight" class="block text-teal-300">{{ titleHighlight }}</span>
      </h1>

      <!-- Decorative line (responsive) -->
      <div class="flex items-center justify-center gap-3 mb-6">
        <div class="h-px w-16 sm:w-40 md:w-80 bg-white/30 rounded-full"></div>
      </div>

      <!-- Subtitle -->
      <p v-if="subtitle" class="text-lg sm:text-xl md:text-2xl font-semibold text-white/95 mb-4 tracking-wide">
        {{ subtitle }}
      </p>

      <!-- Description -->
      <p v-if="description" class="max-w-2xl mx-auto text-base sm:text-lg md:text-xl text-white/75 leading-relaxed font-normal">
        {{ description }}
      </p>

      <!-- Slot for custom content -->
      <slot></slot>

      <!-- Scroll indicator -->
      <div v-if="showScrollIndicator" class="mt-14 flex flex-col items-center gap-1 text-teal-500 text-xs">
        <span>Scroll ke bawah</span>
        <div class="w-5 h-8 rounded-full border border-teal-500 flex items-start justify-center pt-1">
          <div class="w-1 h-2 rounded-full bg-teal-400 animate-bounce"></div>
        </div>
      </div>

    </div>

  </section>
</template>
