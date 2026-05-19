<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const landing = "/storage/images/beranda.png";

defineProps({
    title: {
        type: String,
        default: 'SIKERSA',
    },
    subtitle: {
        type: String,
        default: 'Sistem Kolaborasi Boyolali',
    },
});

// Check if user is logged in
const isLoggedIn = computed(() => {
    try {
        return !!(page.props.auth?.user)
    } catch (e) {
        return false
    }
})

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
    <section class="relative overflow-hidden text-white min-h-screen sm:min-h-[90vh] flex items-center">

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
            class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 md:px-8 text-center animate-fadeUp">
            
            <!-- TITLE (FAHKWANG) -->
            <h1 class="mx-auto mb-2 sm:mb-4 text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-widest leading-tight sm:leading-none" 
            style="font-family: 'Fahkwang', sans-serif;"> {{ title }} </h1> 
            <div class="mx-auto mb-3 sm:mb-6 w-32 sm:w-48 md:w-56 h-px bg-white/40"></div>

            <!-- LABEL BELOW TITLE -->
            <p class="mx-auto text-xs sm:text-base md:text-lg text-white/90 font-light mb-2 sm:mb-4">
                Sistem Kerjasama Boyolali
            </p>

            <!-- SUBTITLE -->
            <p class="mx-auto mt-2 sm:mt-3 md:mt-4 max-w-2xl sm:max-w-3xl text-xs sm:text-sm md:text-base lg:text-lg leading-relaxed text-white px-2 sm:px-0" style="text-shadow: 0 4px 18px rgba(0,0,0,0.45);">
                {{ subtitle }}
            </p>

            <!-- BUTTON -->
            <div class="mt-6 sm:mt-10 md:mt-12 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 md:gap-6 relative z-20">
                <a v-if="!isLoggedIn" href="/register"
                   class="rounded-full px-6 sm:px-8 md:px-10 py-2 sm:py-2.5 md:py-3 text-xs sm:text-sm md:text-base font-semibold text-[#17464E] bg-white shadow-2xl hover:scale-105 transition w-full sm:w-auto">
                    Daftar
                </a>

                <a href="#about"
                   class="rounded-full px-6 sm:px-8 md:px-10 py-2 sm:py-2.5 md:py-3 text-xs sm:text-sm md:text-base font-semibold text-white flex items-center justify-center gap-2 sm:gap-3 hover:scale-105 transition w-full sm:w-auto" 
                   style="background-color: rgba(12,80,92,0.95); box-shadow: 0 8px 20px rgba(12,80,92,0.25);">
                    
                    <svg width="14" height="14" class="sm:w-4 sm:h-4 md:w-5 md:h-5" viewBox="0 0 24 24" fill="none">
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