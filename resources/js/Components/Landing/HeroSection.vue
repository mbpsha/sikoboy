<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
//const landing = "/storage/images/landing.png";
const landing = new URL('@/images/landing.png', import.meta.url).href;

defineProps({
    title: {
        type: String,
        default: 'SIKOBOY',
    },
    subtitle: {
        type: String,
        default: 'Sistem Kolaborasi Boyolali',
    },
});

// 🔥 PARALLAX
const offset = ref(0)

const handleScroll = () => {
    offset.value = window.scrollY * 0.3
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <section class="relative overflow-hidden text-white min-h-[90vh] flex items-center">

        <!-- font fakhwang -->
         <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- BACKGROUND (AMAN, GA NUTUP BUTTON) -->
        <div class="absolute inset-0 pointer-events-none">
            
            <!-- PARALLAX IMAGE -->
            <div 
                class="w-full h-[120%] bg-cover bg-center"
                :style="{ 
                    backgroundImage: `url(${landing})`,
                    transform: `translateY(${offset}px)`
                }">
            </div>

            <!-- gradient putih (moved lower so subtitle remains on dark overlay) -->
            <div 
                class="absolute inset-0"
                style="background: linear-gradient(
                        to bottom,
                        rgba(255, 255, 255, 0.05) 10%,
                        rgba(255, 255, 255, 0.6) 70%,
                        rgba(255, 255, 255, 1) 100%
                );">
            </div>
        </div>

        <!-- CONTENT -->
        <div 
            class="relative z-10 mx-auto max-w-6xl px-6 text-center animate-fadeUp">
            
            <!-- TITLE (FAHKWANG) -->
            <h1 class="mx-auto mb-4 text-4xl sm:text-6xl font-semibold tracking-[1em] leading-none" 
            style="font-family: 'Fahkwang', sans-serif;"> {{ title }} </h1> 
            <div class="mx-auto mb-6 w-56 h-px bg-white/40"></div>

            <!-- LABEL BELOW TITLE -->
            <p class="mx-auto text-lg sm:text-xl text-white/80 font-light mb-4">
                Sistem Kolaboratif Boyolali
            </p>

            <!-- SUBTITLE -->
            <p class="mx-auto mt-2 max-w-3xl text-sm sm:text-base leading-relaxed text-white" style="text-shadow: 0 4px 18px rgba(0,0,0,0.45);">
                {{ subtitle }}
            </p>

            <!-- BUTTON -->
            <div class="mt-12 flex items-center justify-center gap-6 relative z-20">
                <a href="/register"
                   class="rounded-full px-10 py-3 text-sm font-semibold text-[#17464E] bg-white shadow-2xl hover:scale-105 transition">
                    Daftar
                </a>

                <a href="#about"
                   class="rounded-full px-6 py-3 text-sm font-semibold text-white flex items-center gap-3 hover:scale-105 transition"
                   style="background-color: rgba(12,80,92,0.95); box-shadow: 0 8px 20px rgba(12,80,92,0.25);">
                    
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h11" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 5l7 7-7 7" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>

                    <span>Baca Selengkapnya</span>
                </a>
            </div>
        </div>

        <!-- TRANSISI KE STATS -->
        <div class="absolute bottom-0 left-0 w-full">
            <svg viewBox="0 0 1440 180" class="w-full block" preserveAspectRatio="none">
                <path 
                    fill="#f8fafc"
                    d="M0,100C240,160,480,160,720,130C960,100,1200,60,1440,100L1440,180L0,180Z">
                </path>
            </svg>
        </div>

    </section>
</template>

<style>
/* ANIMASI MASUK */
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

/* FONT FAHKWANG */
.font-fahkwang {
    font-family: 'Fahkwang', sans-serif;
}
</style>