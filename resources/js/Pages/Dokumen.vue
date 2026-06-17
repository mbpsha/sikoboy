<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue'
import DokumenHero from '@/Components/Dokumen/HeroSection.vue'
import DokumenContent from '@/Components/Dokumen/Content.vue'
import CardDokumen from '@/Components/Dokumen/CardDokumen.vue'

const props = defineProps({
  dokumenGroups: { type: Array, default: () => [] },
})
</script>

<template>
  <LandingLayout>
    <DokumenHero />

    <section class="bg-white py-16">
      <div class="mx-auto max-w-6xl px-6">

          <!-- INTRO TEXT -->
        <div class="pb-8 mb-8">
          <p class="text-4xl sm:text-5xl md:text-3xl text-[#0C505C] font-bold text-center">
              Template DokumenKerja Sama
          </p>
         </div>

        <!-- Manual Book card pinned just below the title -->
        <div class="max-w-sm mx-auto mb-4">
          <CardDokumen
            title="BUKU PANDUAN PENGGUNA"
            badge="PDF"
            :href="'storage/docs/Manual Book MItra SIKARSA.pdf'"
            :preview="'storage/docs/Manual Book MItra SIKARSA.pdf'"
            buttonText="Lihat Buku Panduan"
            description="Panduan lengkap penggunaan sistem SIKARSA untuk mitra kerjasama."
          />
        </div>
      </div>
    </section>

    <DokumenContent :dokumen-groups="props.dokumenGroups" />
  </LandingLayout>
</template>