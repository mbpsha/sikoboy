<script setup>
import { Link } from '@inertiajs/vue3'
import CardDokumen from './CardDokumen.vue'
import ExtraTemplate from './ExtraTemplate.vue'
import CTASection from './CTASection.vue'
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

      <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
        <!-- KSDD -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDD</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Daerah Lain)</p>

          <div class="mt-8 space-y-6">
            <CardDokumen
              title="PERJANJIAN KERJA SAMA"
              description="Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pemerintah Daerah Kabupaten Boyolali"
            />

            <CardDokumen
              title="KESEPAKATAN BERSAMA"
              description="Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dan Pemerintah Daerah Kabupaten Boyolali"
            />
          </div>
        </div>

        <!-- KSDPK -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDPK</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Pihak Ketiga)</p>

          <div class="mt-8 space-y-6">
            <CardDokumen
              title="PERJANJIAN KERJA SAMA"
              description="Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pihak Ketiga"
            />

            <CardDokumen
              title="KESEPAKATAN BERSAMA"
              description="Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dengan Pihak Ketiga"
            />
          </div>
        </div>
      </div>

      <!-- Additional templates -->
      <div class="mt-12 max-w-3xl mx-auto space-y-8 px-4 sm:px-0">
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">Nota Kesepakatan Sinergi</h3>
          <div class="mt-6 flex justify-center">
            <CardDokumen
              title="SINERGI"
              description="Nota Kesepakatan antara Kementerian/Lembaga/Instansi Vertikal dan Pemerintah Kabupaten Boyolali"
            />
          </div>
        </div>

        <ExtraTemplate />
      </div>
      <CTASection />

    </div>
</template>
