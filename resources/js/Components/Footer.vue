<script setup>
import logo from '@/images/logo_byl.png';
import instagram from '@/images/instagram.png';
import youtube from '@/images/youtube.png';
</script>

<template>
<footer id="contact" class="bg-[#1f5459] text-white py-14 px-6">
  <div class="max-w-6xl mx-auto grid grid-cols-3 gap-10 items-start">

    <!-- LEFT -->
    <div class="w-full flex flex-col items-start gap-4">
      <img :src="logo" class="w-20 h-20 object-contain" />

      <p class="font-fahkwang text-2xl font-semibold tracking-widest text-white mb-0">
        S I K O B O Y
      </p>

      <p class="text-sm text-white/80 leading-relaxed max-w-xs">
        Sistem Kolaboratif Boyolali<br>
        Sekretariat Daerah Kabupaten Boyolali
      </p>
    </div>

    <!-- MIDDLE -->
    <div class="w-full flex flex-col items-start">
      <h3 class="text-sm font-semibold mb-3 text-white">
        Media Sosial
      </h3>

      <div class="w-12 h-[2px] bg-white/30 mb-4"></div>

      <div class="space-y-3 text-sm text-white/80">

        <div class="flex items-center gap-2">
          <img :src="instagram" alt="Instagram" class="w-4 h-4 object-contain" />
          <span>@setda_boyolali</span>
        </div>

        <div class="flex items-center gap-2">
          <img :src="youtube" alt="YouTube" class="w-4 h-4 object-contain" />
          <span>SETDA BOYOLALI</span>
        </div>

      </div>
    </div>

    <!-- RIGHT -->
    <div class="w-full flex flex-col items-start">
      <h3 class="text-sm font-semibold mb-3 text-white">
        Kontak
      </h3>

      <div class="w-12 h-[2px] bg-white/30 mb-4"></div>

      <div class="space-y-3 text-sm text-white/80">

        <div class="flex gap-2">
          <p>
            Biro Kerja Sama Daerah<br>
            Kabupaten Boyolali<br>
            Jl. Merdeka Barat, Kemiri, Boyolali
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span>✉️</span>
          <span>setda@boyolali.go.id</span>
        </div>

        <div class="flex items-center gap-2">
          <span>📞</span>
          <span>(0276) 321021</span>
        </div>

      </div>
    </div>

  </div>
</footer>
</template>