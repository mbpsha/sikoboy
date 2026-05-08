<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const heroTentang = "/storage/images/tentang.png";


// PARALLAX
const offset = ref(0);
const screenWidth = ref(0);

const handleScroll = () => {
  offset.value = window.scrollY * 0.3;
};

const updateWidth = () => {
  screenWidth.value = window.innerWidth;
};

onMounted(() => {
  updateWidth();
  window.addEventListener("resize", updateWidth);
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("resize", updateWidth);
  window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
  <section class="relative overflow-hidden text-white min-h-[85vh] md:min-h-[100vh] flex items-center">
    
    <!-- BACKGROUND -->
    <div class="absolute inset-0 pointer-events-none">
      
      <!-- PARALLAX IMAGE -->
      <div
        class="w-full h-[120%] bg-cover bg-[center_30%] md:h-[140%]"
        :style="{
          backgroundImage: `url(${heroTentang})`,
          transform: screenWidth >= 768 ? `translateY(${offset}px)` : 'none'
        }"
      ></div>

      <!-- Gradient bawah HIJAU -->
      <div
        class="absolute inset-0"
        style="
          background: linear-gradient(
            to bottom,
            rgba(138, 180, 187, 0.3) 10%,
            rgba(138, 180, 187, 0.9) 60%,
            rgba(255, 255, 255, 1) 100%
          );
        "
      ></div>
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 mx-auto max-w-6xl px-6 text-center animate-fadeUp">
      
      <h1 class="text-5xl sm:text-4xl md:text-5xl font-extrabold mb-3 tracking-widest">
        T E N T A N G
      </h1>

      <div class="mx-auto mb-4 w-120 h-px bg-white/50"></div>

      <p class="text-base sm:text-lg md:text-xl text-white/90 font-medium mb-2">
        SIKOBOY (Sistem Kolaboratif Boyolali)
      </p>

      <!-- DESKRIPSI PANJANG -->
      <p class="mt-4 max-w-xl md:max-w-2xl mx-auto text-sm sm:text-md lg:text-base font-bold text-white/80 leading-relaxed px-2">
        Mewujudkan Pengelolaan Kerja Sama Daerah yang Efektif, Transparan, dan Akuntabel untuk Kemajuan Kabupaten Boyolali
      </p>

      <!-- TEKS BAWAH removed here (rendered absolutely below to avoid shifting layout) -->

    </div>

    <!-- TEKS BAWAH: absolutely positioned so it appears lower without affecting layout -->
    <div class="absolute left-0 right-0 bottom-10 md:bottom-20 z-20">
      <div class="mx-auto max-w-6xl px-6 text-center">
        <p class="text-3xl sm:text-4xl md:text-2xl text-black">
          Ayo Berkenalan Dengan
          <span class="text-[#31717C] font-medium"> SIKOBOY</span>
        </p>
        <div class="mx-auto mt-2 w-80 h-px bg-black/50"></div>
      </div>
    </div>

  </section>
</template>

<style>
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(60px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeUp {
  animation: fadeUp 1s ease-out forwards;
}
</style>