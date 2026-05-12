<script setup>
import { ref, onMounted, onUnmounted } from "vue";
const heroTentang = "/storage/images/dokumen.png";

// PARALLAX (biar konsisten sama landing)
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
    <section class="relative overflow-hidden text-white min-h-[85vh] md:min-h-[100vh] flex items-center">
    
        <!-- BACKGROUND -->
        <div class="absolute inset-0 pointer-events-none">
      
            <!-- PARALLAX IMAGE -->
            <div
                class="w-full h-[120%] bg-cover bg-[center_30%] md:h-[140%]"
                :style="{
                    backgroundImage: `url(${heroTentang})`,
                    transform: `translateY(${offset}px)`
                }"
            ></div>

            <!-- Gradient bawah -->
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
                D O K U M E N   K E R J A   S A M A
            </h1>

            <div class="mx-auto mb-4 w-120 h-px bg-white/50"></div>

            <p class="text-base sm:text-lg md:text-xl text-white/90 font-medium mb-2">
                SIKOBOY (Sistem Kolaboratif Boyolali)
            </p>

            <!-- DESKRIPSI PANJANG -->
            <p class="mt-4 max-w-xl md:max-w-2xl mx-auto text-sm sm:text-md lg:text-base font-bold text-white/80 leading-relaxed px-2">
                Buat dokumen pengajuan kerjasama baru Anda secara otomatis serta lihat template dokumen
            </p>

        </div>

        <!-- TEKS BAWAH moved to next section -->

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