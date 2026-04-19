<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue'
import HeroSection from '@/Components/Kontak/HeroSection.vue'
import KontakContent from '@/Components/Kontak/Content.vue'
</script>

<template>
  <LandingLayout>
    <HeroSection />
    <KontakContent />
    <section class="max-w-4xl mx-auto mt-8 px-4 sm:px-6">
      <div class="bg-white/5 rounded-lg p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="block w-full p-2 rounded-md">
          <div class="flex items-center gap-3">
            <img src="/images/instagram.png" alt="Instagram" class="w-6 h-6" />
            <span class="text-sm">@setda_boyolali</span>
          </div>
        </div>

        <div class="block w-full p-2 rounded-md">
          <div class="flex items-center gap-3">
            <img src="/images/youtube.png" alt="YouTube" class="w-6 h-6" />
            <span class="text-sm">SETDA BOYOLALI</span>
          </div>
        </div>

        <div class="block w-full p-2 rounded-md">
          <div class="flex items-center gap-3">
            <span class="text-lg">✉️</span>
            <span class="text-sm">setda@boyolali.go.id</span>
          </div>
        </div>

        <div class="block w-full p-2 rounded-md">
          <div class="flex items-center gap-3">
            <span class="text-lg">📍</span>
            <span class="text-sm">Lihat Lokasi</span>
          </div>
        </div>
      </div>
    </section>
  </LandingLayout>
</template>
