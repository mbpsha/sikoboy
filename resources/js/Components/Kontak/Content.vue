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
  <!-- CONTACT INFO CARDS SECTION -->
  <section class="relative bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-6">
      <div class="flex flex-col md:flex-row items-center justify-center gap-8">
        <InfoCard
          title="Telepon"
          description="(0276) 321021"
          variant="phone"
        />

        <InfoCard
          title="Alamat"
          :description="[
            'Bagian Tata Pemerintahan',
            'Sekretariat Daerah Kabupaten Boyolali',
            'Komplek Perkantoran Terpadu Kabupaten Boyolali',
            'Jl. Merdeka Barat, Siswodipuran, Boyolali 57311',
            'Provinsi Jawa Tengah',
          ]"
          variant="location"
        />

        <InfoCard
          title="Email"
          description="tapem.setda.byl@gmail.com"
          variant="email"
        />
      </div>

      <div class="mt-20 flex justify-center">
        <button class="bg-[#0C505C] text-white px-12 py-3 rounded-full shadow-2xl text-lg hover:bg-[#0A3D48] transition">
          Hubungi Kami
        </button>
      </div>
    </div>
  </section>

  <!-- ADDITIONAL INFO SECTION -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mx-auto w-full max-w-4xl">

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
