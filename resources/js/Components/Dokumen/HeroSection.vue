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
    <section
        class="relative overflow-hidden text-white min-h-[100vh] flex flex-col"
    >
        <!-- BACKGROUND -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- PARALLAX IMAGE -->
            <div
                class="w-full h-[120%] bg-cover bg-[center_30%]"
                :style="{
                    backgroundImage: `url(${heroTentang})`,
                    transform: `translateY(${offset}px)`,
                }"
            ></div>

            <!-- gradient bawah -->
            <div
                class="absolute inset-0"
                style="
                    background: linear-gradient(
                        to bottom,
                        rgba(255, 255, 255, 0.05) 20%,
                        rgba(255, 255, 255, 0.6) 70%,
                        rgba(255, 255, 255, 1) 100%
                    );
                "
            ></div>
        </div>

        <!-- CONTENT UTAMA (CENTERED DI TENGAH HERO) -->
        <div
            class="relative z-10 flex-1 flex items-center justify-center"
        >
            <div class="mx-auto max-w-6xl px-6 text-center animate-fadeUp">
                <h1 class="text-4xl md:text-5xl font-bold mb-3 tracking-widest">
                    DOKUMEN KERJA SAMA
                </h1>

                <div class="mx-auto mb-2 w-40 h-px bg-white/50"></div>

                <!-- DESKRIPSI PANJANG -->
                <p
                    class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-white/80 leading-relaxed"
                >
                    Buat dokumen pengajuan kerjasama baru Anda secara otomatis serta lihat template dokumen
                </p>
            </div>
        </div>

        <!-- TEKS BAWAH (DEKAT FADE) -->
        <div class="relative z-10 pb-20 mb-5">
            <p class="text-3xl md:text-4xl text-[#0C505C] font-bold text-center animate-fadeUp" style="animation-delay: 0.3s;">
                Template Dokumen Kerja Sama
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