<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue'
import HeroSection from '@/Components/Kontak/HeroSection.vue'
</script>

<template>
  <LandingLayout>
    <HeroSection />

    <div class="max-w-6xl mx-auto px-6 py-12">
      <div class="mx-auto w-full max-w-4xl">
        <div class="mx-auto my-8 w-40 h-px bg-slate-200"></div>

        <div class="grid md:grid-cols-2 gap-12 place-items-center">
          <div class="bg-white rounded-2xl p-8 shadow-2xl max-w-sm w-full text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92V21a1 1 0 0 1-1.11 1A19 19 0 0 1 3 5.11 1 1 0 0 1 4 4h4.09a1 1 0 0 1 1 .75 11 11 0 0 0 .7 2.81 1 1 0 0 1-.24 1L8.91 11.2a16 16 0 0 0 5.9 5.9l1.6-1.6a1 1 0 0 1 1-.24 11 11 0 0 0 2.8.7 1 1 0 0 1 .75 1V21z"></path></svg>
            </div>
            <h4 class="text-xl font-semibold text-[#0C505C]">Jam Operasional</h4>
            <p class="mt-3 text-sm text-slate-600">Senin - Kamis : 08.00 - 16.00 WIB<br>Jumat : 08.00 - 15.00 WIB</p>
          </div>

          <div class="bg-white rounded-2xl p-8 shadow-2xl max-w-sm w-full text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-[#E6F0F0] rounded-full mx-auto mb-4 text-[#0C505C]">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h6l2 3h3a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </div>
            <h4 class="text-xl font-semibold text-[#0C505C]">Media Sosial</h4>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
              <div class="flex items-center justify-center gap-2"><img src="@/images/instagram.png" class="w-5 h-5"/> setda_boyolali</div>
              <div class="flex items-center justify-center gap-2"><img src="@/images/youtube.png" class="w-5 h-5"/> SETDA BOYOLALI</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </LandingLayout>
</template>
