<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import heroPeraturan from "@/images/HeroPeraturan.png";

// PARALLAX
const offset = ref(0);

const handleScroll = () => {
  offset.value = window.scrollY * 0.3;
};

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
  <section
    class="relative overflow-hidden text-white min-h-[100vh] flex items-center"
  >
    <!-- BACKGROUND -->
    <div class="absolute inset-0 pointer-events-none">
      <!-- PARALLAX IMAGE -->
      <div
        class="w-full h-[100%] bg-cover bg-[center_30%]"
        :style="{
          backgroundImage: `url(${heroPeraturan})`,
          transform: `translateY(${offset}px)`,
        }"
      ></div>

      <!-- OVERLAY HIJAU BRAND -->
      <div class="absolute inset-0 bg-[#17464E]/70"></div>

      <!-- GRADIENT BAWAH (LEBIH TIPIS) -->
      <div
        class="absolute inset-0"
        style="
          background: linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.05) 70%,
            rgba(255, 255, 255, 0.6) 90%,
            rgba(255, 255, 255, 1) 100%
          );
        "
      ></div>
    </div>

    <!-- CONTENT -->
    <div
      class="relative z-10 mx-auto max-w-6xl px-6 text-center animate-fadeUp"
    >
      <h1 class="text-4xl md:text-5xl font-bold mb-3 tracking-widest">
        PERATURAN
      </h1>

      <div class="mx-auto mb-4 w-40 h-px bg-white/50"></div>

      <p
        class="max-w-2xl mx-auto text-sm md:text-base text-white/90 leading-relaxed"
      >
        Dokumen peraturan-peraturan terkait yang berlaku yang menjadi dasar
        pelaksanaan kerja sama.
      </p>
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