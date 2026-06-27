<script setup>
import logo from '@/images/logo_byl.png';
import instagram from '@/images/instagram.png';
import youtube from '@/images/youtube.png';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.value?.auth?.user ?? null);
</script>

<template>
<footer id="contact" class="bg-[#1f5459] text-white py-8 sm:py-12 md:py-14 px-4 sm:px-6">
  <div class="max-w-6xl mx-auto">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 md:gap-10 items-start mb-8 sm:mb-10">

    <!-- LEFT -->
    <div class="w-full flex flex-col items-start gap-3 sm:gap-4">
      <img :src="logo" class="w-16 sm:w-20 h-16 sm:h-20 object-contain" />

      <p class="font-fahkwang text-lg sm:text-2xl font-semibold tracking-widest text-white mb-0">
        S I K A R S A
      </p>

      <p class="text-xs sm:text-sm text-white/80 leading-relaxed max-w-xs">
        Sistem Kerjasama Boyolali<br>
        Sekretariat Daerah Kabupaten Boyolali
      </p>
    </div>

    <!-- MIDDLE -->
    <div class="w-full flex flex-col items-start">
      <h3 class="text-xs sm:text-sm font-semibold mb-2 sm:mb-3 text-white">
        Media Sosial
      </h3>

      <div class="w-12 h-[2px] bg-white/30 mb-3 sm:mb-4"></div>

      <div class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-white/80">

        <div class="flex items-center gap-2">
          <img :src="instagram" alt="Instagram" class="w-3 sm:w-4 h-3 sm:h-4 object-contain" />
          <a href="https://www.instagram.com/setda_boyolali?igsh=MWlhZnl2eHhteGI3Mw==" target="_blank" rel="noopener noreferrer" class="underline hover:text-white transition break-all">@setda_boyolali</a>
        </div>

        <div class="flex items-center gap-2">
          <img :src="youtube" alt="YouTube" class="w-3 sm:w-4 h-3 sm:h-4 object-contain" />
          <a href="https://youtube.com/@setdaboyolali7125?si=iSWwmxc-knmGVdF0" target="_blank" rel="noopener noreferrer" class="underline hover:text-white transition break-all">SETDA BOYOLALI</a>
        </div>

      </div>
    </div>

    <!-- RIGHT -->
    <div class="w-full flex flex-col items-start">
      <h3 class="text-xs sm:text-sm font-semibold mb-2 sm:mb-3 text-white">
        Kontak
      </h3>

      <div class="w-12 h-[2px] bg-white/30 mb-3 sm:mb-4"></div>

      <div class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-white/80">

        <div class="flex gap-2">
          <p class="leading-relaxed">
            Biro Kerja Sama Daerah<br>
            Kabupaten Boyolali<br>
            Jl. Merdeka Barat, Kemiri, Boyolali
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span>✉️</span>
          <a href="mailto:tapem.setda.byl@gmail.com" class="underline hover:text-white transition break-all">tapem.setda.byl@gmail.com</a>
        </div>

        <div class="flex items-center gap-2">
          <span>📞</span>
          <a href="tel:+62276321021" class="underline hover:text-white transition">(0276) 321021</a>
        </div>

      </div>
    </div>

  </div>

  <!-- MAPS SECTION -->
  <div class="mt-8 sm:mt-10 pt-6 sm:pt-8 border-t border-white/20">
    <div class="max-w-6xl mx-auto">
      <h3 class="text-xs sm:text-sm font-semibold mb-3 sm:mb-4 text-white">
        Lokasi
      </h3>
      <iframe
        width="100%"
        height="280"
        class="sm:h-[350px] md:h-96 rounded-lg"
        style="border:0;"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.275006401413!2d110.6111086!3d-7.544959399999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a6956d45e1475%3A0x590dca8e8b6867ec!2sSekretariat%20Daerah%20Kabupaten%20Boyolali!5e0!3m2!1sid!2sid!4v1778244811391!5m2!1sid!2sid"
        allowfullscreen=""
        aria-hidden="false"
        tabindex="0">
      </iframe>
    </div>
  </div>
</footer>
</template>
