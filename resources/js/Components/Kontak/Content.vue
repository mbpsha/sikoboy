<script setup>
import InfoCard from './InfoCard.vue'
import instagram from '@/images/instagram.png'
import youtube from '@/images/youtube.png'

const instagramHtml = `
  <div class="flex items-center justify-center gap-2"><img src="${instagram}" class="w-4 h-4"/> setda_boyolali</div>
  <div class="flex items-center justify-center gap-2"><img src="${youtube}" class="w-5 h-4"/> SETDA BOYOLALI</div>
`
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="mx-auto w-full max-w-4xl">
      <div class="mx-auto my-6 w-32 sm:w-40 h-px bg-slate-200"></div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-10 place-items-center">
        <InfoCard
          title="Jam Operasional"
          description="Senin - Kamis : 08.00 - 16.00 WIB<br>Jumat : 08.00 - 15.00 WIB"
          variant="clock"
        />

        <InfoCard
          title="Media Sosial"
          :description="instagramHtml"
          variant="social"
        />
      </div>
    </div>
  </div>
</template>
