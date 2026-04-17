<script setup>
const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  variant: { type: String, default: 'phone' },
})
</script>

<template>
  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-2xl max-w-sm w-full text-center">
    <div class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
      <template v-if="variant === 'clock'">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </template>
      <template v-else-if="variant === 'phone'">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92V21a1 1 0 0 1-1.11 1A19 19 0 0 1 3 5.11 1 1 0 0 1 4 4h4.09a1 1 0 0 1 1 .75 11 11 0 0 0 .7 2.81 1 1 0 0 1-.24 1L8.91 11.2a16 16 0 0 0 5.9 5.9l1.6-1.6a1 1 0 0 1 1-.24 11 11 0 0 0 2.8.7 1 1 0 0 1 .75 1V21z"></path></svg>
      </template>
      <template v-else>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h6l2 3h3a2 2 0 0 1 2 2z"></path>
          <circle cx="12" cy="13" r="4"></circle>
        </svg>
      </template>
    </div>
    <h4 class="text-lg sm:text-xl font-semibold text-[#0C505C]">{{ title }}</h4>
    <div class="mt-3 text-sm sm:text-base text-slate-600" v-html="description"></div>
  </div>
</template>
